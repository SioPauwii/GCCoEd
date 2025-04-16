<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    protected $table = 'messages'; 

    protected $primaryKey = 'id';

    public $timestamps = true; 

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
    ];

    protected $casts = [
        'message' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // public function sender()
    // {
    //     return $this->belongsTo(message::class, 'id'); // Assuming 'sender_id' is the foreign key
    // }

    // public function receiver()
    // {
    //     return $this->belongsTo(message::class, 'id'); // Assuming 'receiver_id' is the foreign key
    // }
}
