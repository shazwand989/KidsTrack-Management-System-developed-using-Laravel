<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTimetable extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'day_id',
        'hall_id',
        'lecturer_group_id',
        'time_from',
        'time_to',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function lecturerGroup()
    {
        return $this->belongsTo(LecturerGroup::class);
    }
}