<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitFeedback extends Model
{
    use HasFactory;

    protected $table = 'visit_feedback';

    protected $fillable = [
        'user_id',
        'reserva_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
