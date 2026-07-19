<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
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
        'enrollment_date',
        'qr_code',
        'qr_code_url',
    ];

    protected $casts = [
        'dob' => 'date',
        'enrollment_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ============================================
    // RELATIONSHIPS - FIXED! 🔥
    // ============================================

    // Main Parent - rujuk parents table
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    // 🔥🔥🔥 FIX: Second Parent - rujuk parents table (BUKAN second_parents!) 🔥🔥🔥
    // RUJUK SecondParent (BUKAN ParentModel!)
public function secondParent()
{
    return $this->belongsTo(SecondParent::class, 'second_parent_id');  // ✅
}
    // Guardian - rujuk guardians table
    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}