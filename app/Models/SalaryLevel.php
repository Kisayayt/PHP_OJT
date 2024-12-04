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
        'monthly_salary',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'salary_level_user')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }
}
