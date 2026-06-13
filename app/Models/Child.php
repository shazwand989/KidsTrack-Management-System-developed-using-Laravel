<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        'name',
        'age',
        'ic_number',
        'dob',
        'address',
        'photo',
        'classroom_id',
        'parent_id',
        'second_parent_id',
        'guardian_id',
        'medical_notes',
        'dietary',
        'is_active',
        'enrollment_date'
    ];

    protected $casts = [
        'dob'             => 'date',
        'enrollment_date' => 'date',
        'is_active'       => 'boolean'
    ];

    // ─── Relationships ────────────────────────────────

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function secondParent()
    {
        return $this->belongsTo(ParentModel::class, 'second_parent_id');
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'child_id');
    }

    // ─── Accessors ────────────────────────────────────

    public function getClassroomNameAttribute()
    {
        return $this->classroom ? $this->classroom->name : 'Not assigned';
    }

    public function getClassroomBadgeAttribute()
    {
        if ($this->classroom) {
            return '<span class="classroom-badge">🏫 ' . e($this->classroom->name) . ' (' . e($this->classroom->code) . ')</span>';
        }
        return '<span class="empty-text">—</span>';
    }

    // ─── Scopes ───────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }
}
