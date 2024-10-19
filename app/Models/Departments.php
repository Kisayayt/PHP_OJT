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
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public $timestamps = true;
}
