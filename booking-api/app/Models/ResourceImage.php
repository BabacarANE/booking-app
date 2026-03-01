<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceImage extends Model
{
    protected $fillable = [
        'resource_id',
        'path',
        'url',
        'is_primary',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
