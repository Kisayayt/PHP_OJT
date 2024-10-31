<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'parent_id',
        'is_active',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function parent()
    {
        return $this->belongsTo(Departments::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Departments::class, 'parent_id')->where('is_active', 1);
    }

    public $timestamps = true;
}
