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

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    // Helper Methods - Statistics
    public function getTotalChildrenAttribute()
    {
        return $this->children()->count();
    }

    public function getTotalPresentAttribute()
    {
        return $this->children()->whereHas('attendances', function($q) {
            $q->whereDate('date', today())->where('status', 'present');
        })->count();
    }

    public function getTotalDropOffAttribute()
    {
        return $this->children()->whereHas('attendances', function($q) {
            $q->whereDate('date', today())->whereNotNull('drop_off_time');
        })->count();
    }

    public function getTotalPickupAttribute()
    {
        return $this->children()->whereHas('attendances', function($q) {
            $q->whereDate('date', today())->whereNotNull('pickup_time');
        })->count();
    }

    public function getTotalNotPickupAttribute()
    {
        return $this->getTotalPresentAttribute() - $this->getTotalPickupAttribute();
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}