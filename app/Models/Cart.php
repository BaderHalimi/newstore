<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotal(): float
    {
        return $this->items->sum(function ($item) {
            return $item->product->getCurrentPrice() * $item->quantity;
        });
    }

    public function getItemsCount(): int
    {
        return $this->items->sum('quantity');
    }
}
