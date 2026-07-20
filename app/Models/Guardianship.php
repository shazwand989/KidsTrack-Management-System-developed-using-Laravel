<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardianship extends Model
{
    protected $fillable = [
        'user_id', 'child_id', 'relationship', 'is_emergency_contact',
    ];

    protected $casts = [
        'is_emergency_contact' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
