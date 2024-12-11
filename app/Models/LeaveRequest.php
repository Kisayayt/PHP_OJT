<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * Các trường có thể gán hàng loạt.
     */
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'leave_type',
        'is_paid',
        'reason',
        'duration',
        'status',
    ];

    /**
     * Quan hệ với model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
