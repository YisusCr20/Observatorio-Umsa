<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicContentItem extends Model
{
    protected $fillable = [
        'page',
        'title',
        'category',
        'event_date',
        'body',
        'image_path',
        'button_label',
        'button_url',
        'position',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
    ];
}
