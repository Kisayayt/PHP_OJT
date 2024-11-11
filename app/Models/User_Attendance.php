<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Attendance extends Model
{
    use HasFactory;

    protected $table = 'user_attendance';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'time', 'type', 'status',];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
