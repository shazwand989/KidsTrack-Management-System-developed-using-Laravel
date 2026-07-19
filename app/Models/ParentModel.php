<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'name',
        'age',
        'phone',
        'address',
        'photo',
        'type',         // <-- TAMBAH INI
        'verified',
        'emergency',
    ];

    // ============================================
    // CASTS
    // ============================================
    
    protected $casts = [
        'verified' => 'boolean',
        'emergency' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIP DENGAN USER
    // ============================================
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ============================================
    // RELATIONSHIP YANG SEDIA ADA
    // ============================================

    public function secondParent()
    {
        return $this->hasOne(SecondParent::class, 'parent_id');
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Child::class, 'parent_id');
    }

    public function secondChildren()
    {
        return $this->hasMany(Child::class, 'second_parent_id');
    }

    // ============================================
    // HELPERS
    // ============================================

    public function getFullInfoAttribute()
    {
        return $this->name . ' - ' . $this->phone;
    }

    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function getRoleLabelAttribute()
    {
        if ($this->user) {
            return match($this->user->role) {
                'parent1' => 'Parent Utama',
                'parent2' => 'Parent Kedua',
                'guardian' => 'Guardian',
                default => 'Parent'
            };
        }
        return 'Tiada User';
    }

    // Helper untuk dapatkan type label
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'main' => 'Parent Utama',
            'second' => 'Parent Kedua',
            'guardian' => 'Guardian',
            default => 'Parent'
        };
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function scopeEmergency($query)
    {
        return $query->where('emergency', true);
    }

    public function scopeMain($query)
    {
        return $query->where('type', 'main');
    }

    public function scopeSecond($query)
    {
        return $query->where('type', 'second');
    }

    public function scopeGuardian($query)
    {
        return $query->where('type', 'guardian');
    }
}