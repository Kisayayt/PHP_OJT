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

    public function checkInRecord()
    {
        return $this->hasOne(User_Attendance::class, 'user_id', 'user_id')
            ->where('type', 'in')
            ->where('created_at', '<', function ($query) {
                $query->select('created_at')
                    ->from('user_attendance')
                    ->whereColumn('user_id', 'user_id')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            })
            ->orderBy('created_at', 'desc');
    }
}
