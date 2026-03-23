<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    /**
     * Get all of the cosmetic for the Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cosmetic(): HasMany
    {
        return $this->hasMany(Cosmetic::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str($value)->slug();
    }
    
}
