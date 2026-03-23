<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cosmetic extends Model
{

    use SoftDeletes;
    protected $table = 'cosmetics';
    //
    protected $guarded = ['id'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function benefits()
    {
        return $this->hasMany(CosmeticBenefit::class);
    }

    public function photos()
    {
        return $this->hasMany(CosmeticPhoto::class);
    }

    public function testimonials()
    {
        return $this->hasMany(CosmeticTestimonial::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str($value)->slug();
    }

    public function scopePopular($query)
    {
        return $query->where('is_populer', true);
    }
}
