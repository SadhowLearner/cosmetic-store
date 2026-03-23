<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    use SoftDeletes;
    protected $guarded = [];

    /**
     * Get all of the cosmetics for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cosmetic(): HasMany
    {
        return $this->hasMany(Cosmetic::class);
    }

    // protected static function booted()
    // {
    //     static::creating(fn($category) => $category->update(['slug' =>  str($category->name)->slug()]));
    // }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str($value)->slug();
    }

}