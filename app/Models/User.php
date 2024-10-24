<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'avatar',
        'role',
        'email',
        'password',
        'phone_number',
        'department_id',
    ];

    protected $appends = ['isCheckedIn'];

    public function department()
    {
        return $this->belongsTo(Departments::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function attendances()
    {
        return $this->hasMany(User_Attendance::class, 'user_id');
    }

    public function getIsCheckedInAttribute()
    {
        $lastCheckIn = $this->attendances()
            ->where('type', 'in')
            ->orderBy('created_at', 'desc')
            ->first();


        if (!$lastCheckIn) {
            return false;
        }


        $lastCheckOut = $this->attendances()
            ->where('type', 'out')
            ->where('created_at', '>', $lastCheckIn->created_at)
            ->first();

        return !$lastCheckOut;
    }



    public $timestamps = true;
}
