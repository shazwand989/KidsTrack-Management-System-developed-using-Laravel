<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondParent extends Model
{
    protected $table = 'second_parents';

    protected $fillable = [
        'parent_id',
        'user_id',
        'name',
        'age',
        'phone',
        'address',
        'photo',
        'type',         // <-- TAMBAH INI
    ];

    // ============================================
    // CASTS
    // ============================================
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    // Relationship dengan main parent
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    // Relationship dengan User (untuk login)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ============================================
    // HELPERS
    // ============================================

    // Helper untuk dapatkan initial untuk avatar
    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    // Helper untuk dapatkan role label
    public function getRoleLabelAttribute()
    {
        if ($this->user) {
            return match($this->user->role) {
                'parent2' => 'Parent Kedua',
                default => 'Parent'
            };
        }
        return 'Tiada User';
    }

    // Helper untuk dapatkan type label
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'second' => 'Parent Kedua',
            default => 'Parent'
        };
    }
}