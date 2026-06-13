<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondParent extends Model
{
    protected $table = 'second_parents';

    protected $fillable = [
        'parent_id',
        'name',
        'age',
        'phone',
        'address',
        'photo',
    ];

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }
}