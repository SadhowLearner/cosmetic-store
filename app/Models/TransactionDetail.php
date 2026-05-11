<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * Get the cosmetic that owns the TransactionDetail
     */
    public function cosmetic(): BelongsTo
    {
        return $this->belongsTo(Cosmetic::class);
    }

    /**
     * Get the booking that owns the TransactionDetail
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(BookingTransaction::class);
    }
}
