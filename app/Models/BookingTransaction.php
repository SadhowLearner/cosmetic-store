<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BookingTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get all of the detail for the BookingTransaction
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function generateUniqueTrxId(): string
    {
        $prefix = 'SVX-';
        do {
            $randomString = $prefix.Str::upper(Str::random(10)); // Generate a random string of 10 characters (5 bytes)
        } while (self::where('booking_trx_id', $randomString)->exists());

        return $randomString;
    }
}
