<?php

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @extends BaseRepository<Cart>
 */
class CartRepository extends BaseRepository
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function getOrCreateForUser(int $userId): Cart
    {
        /** @var Cart $cart */
        $cart = $this->query()->firstOrCreate(['user_id' => $userId]);

        return $cart;
    }

    public function updateBranch(Cart $cart, int $branchId): Cart
    {
        $cart->fill(['branch_id' => $branchId])->save();

        return $cart->refresh();
    }

    public function loadDetails(Cart $cart): Cart
    {
        return $cart->load([
            'branch:id,name,address',
            'items' => fn (Builder|HasMany $itemQuery): Builder|HasMany => $itemQuery->orderBy('id'),
            'items.productVariant.product:id,name,slug',
            'items.productVariant.product.images' => fn (Builder|HasMany $imageQuery): Builder|HasMany => $imageQuery
                ->where('is_primary', true)
                ->orderBy('sort_order'),
            'items.productVariant.inventories' => fn (Builder|HasMany $inventoryQuery): Builder|HasMany => $inventoryQuery
                ->whereHas('branch', fn (Builder $branchQuery): Builder => $branchQuery->where('is_active', true)),
        ]);
    }
}
