<?php

namespace App\Models;

use Hoa\Event\Listens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentor extends Model
{
    
    use HasFactory;

    protected $table = 'mentor_infos'; // Change this if your table name is different

    protected $primaryKey = 'mentor_no'; // Define primary key

    public $timestamps = true; // Disable timestamps if 'created_at' and 'updated_at' do not exist

    protected $fillable = [
        'ment_inf_id',
        'gender',
        'phoneNum',
        'address',
        'image',
        'course',
        'year',
        'subjects',
        'proficiency',
        'learn_modality',
        'teach_sty',
        'availability',
        'prefSessDur',
        'bio',
        'exp',
        'credentials',
        'rating_ave',
        'account_status',
        'approval_status',
        'approved',
    ];

    protected $casts = [
        'subjects' => 'array', // Convert longtext to array
        'availability' => 'array', // Convert longtext to array
        'credentials' => 'array', // Convert longtext to array
        'rating_ave' => 'float'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'ment_inf_id', 'id');
    }

}
