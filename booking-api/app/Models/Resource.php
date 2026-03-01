<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_per_night',
        'capacity',
        'amenities',
        'images',
        'location',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array', // ✅ JSON auto-converti en tableau
            'images'    => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function images_list()
    {
        return $this->hasMany(ResourceImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ResourceImage::class)->where('is_primary', true);
    }
}
