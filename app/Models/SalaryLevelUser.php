<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryLevelUser extends Model
{
    protected $table = 'salary_level_user'; // Tên bảng
    protected $fillable = ['user_id', 'salary_level_id', 'start_date', 'end_date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function salaryLevel()
    {
        return $this->belongsTo(SalaryLevel::class, 'salary_level_id');
    }
}
