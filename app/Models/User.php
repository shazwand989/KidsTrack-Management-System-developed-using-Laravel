<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Auditable, SoftDeletes;

    protected $fillable = [
        'name',
        'age',
        'email',
        'password',
        'phone_number',
        'address',
        'photo',
        'role',
        'verified',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified' => 'boolean',
        ];
    }

    // Children linked via guardianships
    public function guardianships()
    {
        return $this->hasMany(Guardianship::class);
    }

    public function children()
    {
        return $this->belongsToMany(Child::class, 'guardianships')
            ->withPivot('relationship', 'is_emergency_contact')
            ->withTimestamps();
    }
}
