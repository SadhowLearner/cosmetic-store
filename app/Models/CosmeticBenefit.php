<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CosmeticBenefit extends Model
{
    use SoftDeletes;
   protected $guarded = [];

   /**
    * Get the cosmetic that owns the CosmeticBenefit
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function cosmetic(): BelongsTo
   {
       return $this->belongsTo(Cosmetic::class);
   }
}
