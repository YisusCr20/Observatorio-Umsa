<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
