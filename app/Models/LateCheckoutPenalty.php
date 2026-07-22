<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LateCheckoutPenalty extends Model
{
    protected $fillable = [
        'attendance_id', 'child_id', 'parent_id', 'date',
        'scheduled_checkout', 'actual_checkout', 'late_minutes',
        'grace_period', 'penalty_amount', 'bill_code', 'transaction_id',
        'payment_status', 'paid_at', 'payment_method', 'remarks', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'paid_at' => 'datetime',
        'penalty_amount' => 'decimal:2',
    ];

    public function attendance() { return $this->belongsTo(Attendance::class); }
    public function child() { return $this->belongsTo(Child::class); }
    public function parent() { return $this->belongsTo(User::class, 'parent_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function scopePending($q) { return $q->where('payment_status', 'pending'); }
    public function scopePaid($q) { return $q->where('payment_status', 'paid'); }
}
