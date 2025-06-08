<?php

namespace App\Models;

use Hoa\Event\Listens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
 

class Learner extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'learner_info'; // Change this if your table name is different

    protected $primaryKey = 'learner_no'; // Define primary key

    public $timestamps = true; // Disable timestamps if 'created_at' and 'updated_at' do not exist

    protected $fillable = [
        'learn_inf_id',
        'gender',
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
        'account_status',
    ];      

    protected $casts = [
        'subjects' => 'array', // Convert longtext to array
        'availability' => 'array', // Convert longtext to array
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'learn_inf_id', 'id');
    }

}
