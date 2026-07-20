<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'child_id',
        'user_id',
        'date',
        'status',
        'status_note',
        'late_reason',
        'checkin_time',
        'checkout_time',
        'drop_off_by',
        'pickup_by',
        'is_verified',
        'is_late',
        'notes'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'checkin_time' => 'datetime',
        'checkout_time' => 'datetime',
        'is_verified' => 'boolean',
        'is_late' => 'boolean'
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
