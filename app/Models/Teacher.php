<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $fillable = [
        'name',
        'position',
        'age',
        'phone',
        'email',
        'address',
        'photo',
        'nursery_class',
        'classroom_id',  // <-- TAMBAH INI
        'status',
        'qualifications',
        'join_date'
    ];

    protected $casts = [
        'join_date' => 'date',
        'age' => 'integer'
    ];

    // Relationships
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Helper Methods
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '✅ Active',
            'inactive' => '❌ Inactive',
            'on_leave' => '⏳ On Leave'
        ];
        return $badges[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'active',
            'inactive' => 'inactive',
            'on_leave' => 'on-leave'
        ];
        return $colors[$this->status] ?? 'inactive';
    }

    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClass($query, $class)
    {
        return $query->where('nursery_class', $class);
    }

    public function scopeByClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }
}