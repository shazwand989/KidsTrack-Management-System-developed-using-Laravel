<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'name', 'age', 'ic_number', 'dob', 'address', 'photo',
        'classroom_id', 'medical_notes', 'dietary',
        'is_active', 'enrollment_date', 'qr_code', 'qr_code_url',
    ];

    protected $casts = [
        'dob' => 'date', 'enrollment_date' => 'datetime', 'is_active' => 'boolean',
    ];

    public function classroom() { return $this->belongsTo(Classroom::class); }
    public function guardianships() { return $this->hasMany(Guardianship::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }

    // Main parent via guardianships
    public function parent()
    {
        return $this->hasOneThrough(User::class, Guardianship::class, 'child_id', 'id', 'id', 'user_id')
            ->where('guardianships.relationship', 'main_parent');
    }

    // Second parent via guardianships
    public function secondParent()
    {
        return $this->hasOneThrough(User::class, Guardianship::class, 'child_id', 'id', 'id', 'user_id')
            ->where('guardianships.relationship', 'second_parent');
    }

    // Guardian via guardianships
    public function guardian()
    {
        return $this->hasOneThrough(User::class, Guardianship::class, 'child_id', 'id', 'id', 'user_id')
            ->where('guardianships.relationship', 'guardian');
    }

    public function linkedUsers()
    {
        return $this->belongsToMany(User::class, 'guardianships')
            ->withPivot('relationship', 'is_emergency_contact')->withTimestamps();
    }

    public function scopeActive($query) { return $query->where('is_active', true); }
}
