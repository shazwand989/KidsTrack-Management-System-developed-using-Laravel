<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    protected $table = 'guardians';

    protected $fillable = [
        'parent_id',    // <-- link balik ke parent
        'name',
        'age',
        'phone',
        'address',
        'photo',
    ];

    // Relationship balik ke parent
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    // Guardian menjaga ramai children
    public function children()
    {
        return $this->hasMany(Child::class, 'guardian_id');
    }

    public function getInitialAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }
}