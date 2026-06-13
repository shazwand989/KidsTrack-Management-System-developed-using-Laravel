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
        'date',
        'status',
        'checkin_time',
        'checkout_time',
        'drop_off_by',
        'pickup_by',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'checkin_time' => 'datetime',
        'checkout_time' => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}