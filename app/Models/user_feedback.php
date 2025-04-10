<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user_feedback extends Model
{
    protected $table = 'user_feedback';
    protected $primaryKey = 'id';
    public $timestamps = true; // Disable timestamps if not used

    protected $fillable = [
        'reviewer_id',
        'reviewee_id',
        'comment',
        'rating',
    ];

    protected $casts = [
        'comment' => 'string',
        'rating' => 'integer',
    ];
}
