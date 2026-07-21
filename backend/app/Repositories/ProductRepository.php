<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<Product>
 */
class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array<string, mixed> $filters
     * @param list<int> $categoryIds
     * @return LengthAwarePaginator<int, Product>
     */
    public function paginateActive(array $filters, array $categoryIds = []): LengthAwarePaginator
    {
        $query = $this->query()
            ->select('products.*')
            ->addSelect(['minimum_price' => $this->minimumPriceSubquery()])
            ->with([
                'category:id,name,parent_id',
                'brand:id,name',
                'images' => fn (Builder|HasMany $query): Builder|HasMany => $query
                    ->where('is_primary', true)
                    ->orderBy('sort_order'),
            ])
            ->withExists([
                'variants as has_discount' => fn (Builder|HasMany $query): Builder|HasMany => $query
                    ->where('is_active', true)
                    ->whereNotNull('sale_price')
                    ->whereColumn('sale_price', '<', 'price'),
            ])
            ->where('products.is_active', true)
            ->whereHas('variants', fn (Builder $query): Builder => $query->where('is_active', true));

        if ($categoryIds !== []) {
            $query->whereIn('category_id', $categoryIds);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', (int) $filters['brand_id']);
        }

        if (isset($filters['price_min'])) {
            $this->whereMinimumPrice($query, '>=', (int) $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $this->whereMinimumPrice($query, '<=', (int) $filters['price_max']);
        }

        if (! empty($filters['keyword'])) {
            $query->where('products.name', 'like', '%'.$filters['keyword'].'%');
        }

        $this->applySort($query, (string) ($filters['sort'] ?? 'newest'));

        return $query->paginate((int) ($filters['per_page'] ?? 20));
    }

    /**
     * Return the lowest effective variant price for each product.
     *
     * @return Builder<ProductVariant>
     */
    private function minimumPriceSubquery(): Builder
    {
        return ProductVariant::query()
            ->selectRaw('MIN(CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN sale_price ELSE price END)')
            ->whereColumn('product_id', 'products.id')
            ->where('is_active', true);
    }

    /**
     * @param Builder<Product> $query
     */
    private function whereMinimumPrice(Builder $query, string $operator, int $price): void
    {
        $query->where(
            fn ($subquery) => $subquery
                ->selectRaw('MIN(CASE WHEN sale_price IS NOT NULL AND sale_price < price THEN sale_price ELSE price END)')
                ->from('product_variants')
                ->whereColumn('product_id', 'products.id')
                ->where('is_active', true)
                ->whereNull('deleted_at'),
            $operator,
            $price,
        );
    }

    /**
     * @param Builder<Product> $query
     */
    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('minimum_price')->orderByDesc('products.id'),
            'price_desc' => $query->orderByDesc('minimum_price')->orderByDesc('products.id'),
            // TODO: Replace with real sales data when order analytics are implemented.
            'best_selling' => $query->orderByDesc('products.created_at')->orderByDesc('products.id'),
            default => $query->orderByDesc('products.created_at')->orderByDesc('products.id'),
        };
    }
}
