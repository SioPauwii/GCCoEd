<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class user_feedback extends Model
{
    protected $table = 'user_feedback';
    protected $primaryKey = 'id';
    public $timestamps = true; // Disable timestamps if not used

    protected $fillable = [
        'reviewer_id',
        'reviewee_id',
        'feedback',
        'rating'
    ];

    protected $casts = [
        'feedback' => 'string',
        'rating' => 'integer'
    ];

    public function reviewee()
    {
        return $this->belongsTo(Mentor::class, 'reviewee_id', 'ment_inf_id');
    }

    // Relationship to Learner
    public function reviewer()
    {
        return $this->belongsTo(Learner::class, 'reviewer_id', 'learn_inf_id');
    }
}
