<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_number',
    'user_id',
    'branch_id',
    'created_by_user_id',
    'user_address_id',
    'channel',
    'fulfillment_method',
    'status',
    'recipient_name',
    'recipient_phone',
    'province_code',
    'ghn_district_id',
    'ghn_ward_code',
    'shipping_address',
    'subtotal',
    'discount_amount',
    'shipping_fee',
    'total_amount',
    'note',
    'placed_at',
    'cancelled_at',
])]
class Order extends Model
{
    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'ghn_district_id' => 'integer',
            'subtotal' => 'integer',
            'discount_amount' => 'integer',
            'shipping_fee' => 'integer',
            'total_amount' => 'integer',
            'placed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * @return BelongsTo<UserAddress, $this>
     */
    public function userAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }
}
