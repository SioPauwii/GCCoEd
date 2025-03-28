<?php

namespace App\Models;

use Hoa\Event\Listens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentor extends Model
{
    use HasFactory;

    protected $table = 'mentor_info'; // Change this if your table name is different

    protected $primaryKey = 'mentor_no'; // Define primary key

    public $timestamps = true; // Disable timestamps if 'created_at' and 'updated_at' do not exist

    protected $fillable = [
        'ment_inf_id',
        'name',
        'email',
        'phoneNum',
        'city_muni',
        'brgy',
        'image',
        'course',
        'department',
        'year',
        'subjects',
        'proficiency',
        'learn_modality',
        'teach_sty',
        'availability',
        'prefSessDur',
        'bio',
        'exp',
    ];

    protected $casts = [
        'subjects' => 'array', // Convert longtext to array
        'availability' => 'array', // Convert longtext to array
    ];
}
