<?php

namespace App\Models;

use Hoa\Event\Listens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
 

class learner extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'learner_info'; // Change this if your table name is different

    protected $primaryKey = 'learner_no'; // Define primary key

    public $timestamps = true; // Disable timestamps if 'created_at' and 'updated_at' do not exist

    protected $fillable = [
        'learn_inf_id',
        'name',
        'email',
        'phoneNum',
        'address',
        'image',
        'course',
        'year',
        'subjects',
        'learn_modality',
        'learn_sty',
        'availability',
        'prefSessDur',
        'bio',
        'goals',
    ];

    protected $casts = [
        'subjects' => 'array', // Convert longtext to array
        'availability' => 'array', // Convert longtext to array
    ];
}
