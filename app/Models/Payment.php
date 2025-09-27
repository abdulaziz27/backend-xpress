<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToStore;

class Payment extends Model
{
    use HasFactory, HasUuids, BelongsToStore;

    protected $fillable = [
        'store_id',
        'order_id',
        'payment_method',
        'amount',
        'reference_number',
        'status',
        'processed_at',
        'processed_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];



    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the refunds for the payment.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Mark payment as processed.
     */
    public function markAsProcessed(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Check if payment can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' && 
               $this->refunds()->sum('amount') < $this->amount;
    }

    /**
     * Get remaining refundable amount.
     */
    public function getRefundableAmount(): float
    {
        return $this->amount - $this->refunds()->sum('amount');
    }

    /**
     * Scope to get payments by method.
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope to get completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
