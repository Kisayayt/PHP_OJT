<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryLevel extends Model
{
    use HasFactory;


    protected $table = 'salary_levels';


    protected $fillable = [
        'level_name',
        'salary_coefficient',
        'daily_salary',
    ];


    public function users()
    {
        return $this->hasMany(User::class, 'salary_level_id');
    }
}
