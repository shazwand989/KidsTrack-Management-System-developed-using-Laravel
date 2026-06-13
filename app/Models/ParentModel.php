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
        'verified',      // <-- Tambah
        'emergency',     // <-- Tambah
    ];

    // Relationship dengan Second Parent (self relationship)
    public function secondParent()
    {
        return $this->hasOne(SecondParent::class, 'parent_id');
    }

    // Relationship dengan Guardian
    public function guardian()
    {
        return $this->hasOne(Guardian::class, 'parent_id');
    }

    // Untuk child sebagai parent utama
    public function children()
    {
        return $this->hasMany(Child::class, 'parent_id');
    }

    // Untuk child sebagai second parent
    public function secondChildren()
    {
        return $this->hasMany(Child::class, 'second_parent_id');
    }

    // Helper untuk dapatkan full info
    public function getFullInfoAttribute()
    {
        return $this->name . ' - ' . $this->phone;
    }

    // Helper untuk dapatkan initial untuk avatar
    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    // Scope untuk parent yang verified
    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    // Scope untuk emergency contact
    public function scopeEmergency($query)
    {
        return $query->where('emergency', true);
    }
}