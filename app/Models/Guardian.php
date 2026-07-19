<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $table = 'guardians';

    protected $fillable = [
        'parent_id',
        'user_id',
        'name',
        'age',
        'phone',
        'address',
        'photo',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship balik ke parent
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    // Relationship dengan User (untuk login)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Guardian menjaga ramai children
    public function children()
    {
        return $this->hasMany(Child::class, 'guardian_id');
    }

    // Helpers
    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function getRoleLabelAttribute()
    {
        if ($this->user) {
            return match($this->user->role) {
                'guardian' => 'Guardian',
                default => 'Guardian'
            };
        }
        return 'Tiada User';
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'guardian' => 'Guardian',
            default => 'Guardian'
        };
    }
}