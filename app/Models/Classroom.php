<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classrooms';

    protected $fillable = [
        'name',
        'code',
        'age_group',
        'min_age',
        'max_age',
        'capacity',
        'teacher_id',
        'start_time',
        'end_time',
        'status',
        'description',
        'color'
    ];

    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'capacity' => 'integer'
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    // ============================================
    // HELPER METHODS - STATISTICS
    // ============================================
    public function getTotalChildrenAttribute()
    {
        return $this->children()->where('is_active', true)->count();
    }

    public function getTotalPresentAttribute()
    {
        $today = now('Asia/Kuala_Lumpur')->toDateString();
        return $this->children()
            ->whereHas('attendances', function($q) use ($today) {
                $q->whereDate('date', $today)
                  ->whereIn('status', ['present', 'checkin', 'late']);
            })
            ->count();
    }

    public function getTotalCheckoutAttribute()
    {
        $today = now('Asia/Kuala_Lumpur')->toDateString();
        return $this->children()
            ->whereHas('attendances', function($q) use ($today) {
                $q->whereDate('date', $today)
                  ->where('status', 'checkout');
            })
            ->count();
    }

    public function getTotalAbsentAttribute()
    {
        return $this->getTotalChildrenAttribute() - $this->getTotalPresentAttribute();
    }

    public function getCapacityPercentageAttribute()
    {
        if ($this->capacity == 0) return 0;
        return round(($this->getTotalChildrenAttribute() / $this->capacity) * 100);
    }

    public function getAgeRangeAttribute()
    {
        return "{$this->min_age} - {$this->max_age} years";
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 'active') {
            return '✅ Active';
        }
        return '❌ Inactive';
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}