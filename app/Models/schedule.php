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
        'date',
        'time',
    ];

    protected $casts = [
        //
    ];
}
