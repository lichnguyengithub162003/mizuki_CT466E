<?php

namespace App\Services;

use App\Models\BranchInventory;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Repositories\CartItemRepository;
use App\Repositories\CartRepository;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartService extends BaseService
{
    public function __construct(
        private readonly CartRepository $carts,
        private readonly CartItemRepository $items,
    ) {
    }

    public function getForUser(User $user): Cart
    {
        return $this->prepareCart($this->carts->getOrCreateForUser($user->id));
    }

    public function addItem(User $user, int $variantId, int $quantity): Cart
    {
        $cart = $this->carts->getOrCreateForUser($user->id);
        $variant = $this->getActiveVariantOrFail($variantId);
        $item = $this->items->findVariantInCart($cart->id, $variantId);
        $newQuantity = ($item?->quantity ?? 0) + $quantity;

        $this->validateStock($cart, $variant, $newQuantity);

        if ($item === null) {
            $this->items->createForCart($cart, $variantId, $newQuantity);
        } else {
            $this->items->updateQuantity($item, $newQuantity);
        }

        return $this->prepareCart($cart);
    }

    public function updateItem(User $user, int $itemId, int $quantity): ?Cart
    {
        $cart = $this->carts->getOrCreateForUser($user->id);
        $item = $this->items->findForCart($cart->id, $itemId);

        if ($item === null) {
            return null;
        }

        $variant = $this->getActiveVariantOrFail($item->product_variant_id);
        $this->validateStock($cart, $variant, $quantity);
        $this->items->updateQuantity($item, $quantity);

        return $this->prepareCart($cart);
    }

    public function removeItem(User $user, int $itemId): ?Cart
    {
        $cart = $this->carts->getOrCreateForUser($user->id);
        $item = $this->items->findForCart($cart->id, $itemId);

        if ($item === null) {
            return null;
        }

        $this->items->deleteItem($item);

        return $this->prepareCart($cart);
    }

    public function selectBranch(User $user, int $branchId): Cart
    {
        $cart = $this->carts->getOrCreateForUser($user->id);
        $cart = $this->carts->updateBranch($cart, $branchId);

        return $this->prepareCart($cart);
    }

    private function getActiveVariantOrFail(int $variantId): ProductVariant
    {
        $variant = $this->items->findActiveVariant($variantId);

        if ($variant === null) {
            throw ValidationException::withMessages([
                'product_variant_id' => ['Biến thể sản phẩm không tồn tại hoặc đã ngừng bán'],
            ]);
        }

        return $variant;
    }

    private function validateStock(Cart $cart, ProductVariant $variant, int $requestedQuantity): void
    {
        $inventories = $this->items->getActiveBranchInventories($variant->id);
        $totalAvailable = $this->totalAvailableQuantity($inventories);

        if ($cart->branch_id === null) {
            if ($totalAvailable <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => ['Sản phẩm hiện đã hết hàng'],
                ]);
            }

            return;
        }

        $branchAvailable = $inventories
            ->where('branch_id', $cart->branch_id)
            ->sum(fn (BranchInventory $inventory): int => $this->availableQuantity($inventory));

        if ($requestedQuantity > $branchAvailable) {
            throw ValidationException::withMessages([
                'quantity' => ["Chi nhánh đã chọn chỉ còn {$branchAvailable} sản phẩm"],
            ]);
        }
    }

    private function prepareCart(Cart $cart): Cart
    {
        $cart = $this->carts->loadDetails($cart);
        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($cart->items as $item) {
            $variant = $item->productVariant;
            $effectivePrice = $variant->sale_price !== null && $variant->sale_price < $variant->price
                ? $variant->sale_price
                : $variant->price;
            $totalSystemAvailable = $this->totalAvailableQuantity($variant->inventories);
            $availableForCart = $cart->branch_id === null
                ? $totalSystemAvailable
                : (int) $variant->inventories
                    ->where('branch_id', $cart->branch_id)
                    ->sum(fn (BranchInventory $inventory): int => $this->availableQuantity($inventory));
            $subtotal = $effectivePrice * $item->quantity;

            $item->setAttribute('effective_price', $effectivePrice);
            $item->setAttribute('subtotal', $subtotal);
            $item->setAttribute('available_quantity', $availableForCart);
            $item->setAttribute('total_system_available_quantity', $totalSystemAvailable);
            $item->setAttribute(
                'stock_warning',
                $cart->branch_id === null ? $totalSystemAvailable <= 0 : $item->quantity > $availableForCart,
            );

            $totalQuantity += $item->quantity;
            $totalAmount += $subtotal;
        }

        $cart->setAttribute('total_quantity', $totalQuantity);
        $cart->setAttribute('total_amount', $totalAmount);

        return $cart;
    }

    private function availableQuantity(BranchInventory $inventory): int
    {
        return max(0, $inventory->quantity - $inventory->reserved_quantity);
    }

    /**
     * @param Collection<int, BranchInventory> $inventories
     */
    private function totalAvailableQuantity(Collection $inventories): int
    {
        return (int) $inventories->sum(
            fn (BranchInventory $inventory): int => $this->availableQuantity($inventory),
        );
    }
}
