<?php

namespace App\Models;

use Hoa\Event\Listens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules'; // Change this if your table name is different

    protected $primaryKey = 'id'; // Define primary key

    public $timestamps = true; // Disable timestamps if 'created_at' and 'updated_at' do not exist

    protected $fillable = [
        'creator_id',
        'participant_id',
        'subject',
        'date',
        'time',
        'location',
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'participant_id', 'mentor_no')->with('user');
    }

    // Relationship to Learner
    public function learner()
    {
        return $this->belongsTo(Learner::class, 'creator_id', 'learn_inf_id')->with('user');
    }

    
    protected $casts = [
        //
    ];
}
