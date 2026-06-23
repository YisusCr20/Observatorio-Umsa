<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomeSlide extends Model
{
    protected $fillable = [
        'title_highlight',
        'title_normal',
        'description',
        'image_path',
        'image_dark_path',
        'image_light_path',
        'image_shape',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}