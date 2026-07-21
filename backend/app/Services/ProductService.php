<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService extends BaseService
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, Product>
     */
    public function getActiveProducts(array $filters): LengthAwarePaginator
    {
        $categoryIds = isset($filters['category_id'])
            ? $this->getCategoryAndDescendantIds((int) $filters['category_id'])
            : [];

        return $this->products->paginateActive($filters, $categoryIds);
    }

    /**
     * @return list<int>
     */
    private function getCategoryAndDescendantIds(int $categoryId): array
    {
        $categories = $this->categories->getActiveOrdered();

        if (! $categories->contains('id', $categoryId)) {
            return [-1];
        }

        $childrenByParent = $categories->groupBy('parent_id');
        $ids = [];
        $queue = [$categoryId];

        for ($index = 0; $index < count($queue); $index++) {
            $currentId = $queue[$index];
            $ids[] = $currentId;

            /** @var Category $child */
            foreach ($childrenByParent->get($currentId, collect()) as $child) {
                $queue[] = $child->id;
            }
        }

        return $ids;
    }
}
