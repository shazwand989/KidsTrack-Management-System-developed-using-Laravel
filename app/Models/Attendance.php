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
        'parent_id',
        'date',
        'status',
        'late_reason',
        'checkin_time',
        'checkout_time',
        'drop_off_by',
        'pickup_by',
        'is_verified',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'checkin_time' => 'datetime',
        'checkout_time' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }
}