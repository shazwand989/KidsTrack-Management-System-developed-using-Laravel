<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimerSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_name',
        'morning_start',
        'morning_end',
        'afternoon_start',
        'afternoon_end',
        'evening_start',
        'evening_end',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getAllTimers()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $timers = self::all()->keyBy('day_name');
        
        $result = [];
        foreach ($days as $day) {
            if (isset($timers[$day])) {
                $result[$day] = [
                    'morning' => [
                        'start' => $timers[$day]->morning_start ? date('H:i', strtotime($timers[$day]->morning_start)) : '07:00',
                        'end' => $timers[$day]->morning_end ? date('H:i', strtotime($timers[$day]->morning_end)) : '07:30'
                    ],
                    'afternoon' => [
                        'start' => $timers[$day]->afternoon_start ? date('H:i', strtotime($timers[$day]->afternoon_start)) : '12:00',
                        'end' => $timers[$day]->afternoon_end ? date('H:i', strtotime($timers[$day]->afternoon_end)) : '12:30'
                    ],
                    'evening' => [
                        'start' => $timers[$day]->evening_start ? date('H:i', strtotime($timers[$day]->evening_start)) : '17:00',
                        'end' => $timers[$day]->evening_end ? date('H:i', strtotime($timers[$day]->evening_end)) : '17:30'
                    ]
                ];
            } else {
                $result[$day] = [
                    'morning' => ['start' => '07:00', 'end' => '07:30'],
                    'afternoon' => ['start' => '12:00', 'end' => '12:30'],
                    'evening' => ['start' => '17:00', 'end' => '17:30']
                ];
            }
        }
        
        return $result;
    }

    public static function getTimerForDay($dayName)
    {
        $timer = self::where('day_name', $dayName)->first();
        
        if ($timer) {
            return [
                'morning' => [
                    'start' => $timer->morning_start ? date('H:i', strtotime($timer->morning_start)) : '07:00',
                    'end' => $timer->morning_end ? date('H:i', strtotime($timer->morning_end)) : '07:30'
                ],
                'afternoon' => [
                    'start' => $timer->afternoon_start ? date('H:i', strtotime($timer->afternoon_start)) : '12:00',
                    'end' => $timer->afternoon_end ? date('H:i', strtotime($timer->afternoon_end)) : '12:30'
                ],
                'evening' => [
                    'start' => $timer->evening_start ? date('H:i', strtotime($timer->evening_start)) : '17:00',
                    'end' => $timer->evening_end ? date('H:i', strtotime($timer->evening_end)) : '17:30'
                ]
            ];
        }
        
        return [
            'morning' => ['start' => '07:00', 'end' => '07:30'],
            'afternoon' => ['start' => '12:00', 'end' => '12:30'],
            'evening' => ['start' => '17:00', 'end' => '17:30']
        ];
    }

    public static function saveTimer($dayName, $data)
    {
        // 🔥 FIX: Support BOTH formats
        if (isset($data['morning']) && is_array($data['morning'])) {
            // Format: { "morning": {"start": "07:00", "end": "07:30"}, ... }
            $morningStart = $data['morning']['start'] ?? '07:00';
            $morningEnd = $data['morning']['end'] ?? '07:30';
            $afternoonStart = $data['afternoon']['start'] ?? '12:00';
            $afternoonEnd = $data['afternoon']['end'] ?? '12:30';
            $eveningStart = $data['evening']['start'] ?? '17:00';
            $eveningEnd = $data['evening']['end'] ?? '17:30';
        } else {
            // Alternative format: { "morning_start": "07:00", "morning_end": "07:30", ... }
            $morningStart = $data['morning_start'] ?? '07:00';
            $morningEnd = $data['morning_end'] ?? '07:30';
            $afternoonStart = $data['afternoon_start'] ?? '12:00';
            $afternoonEnd = $data['afternoon_end'] ?? '12:30';
            $eveningStart = $data['evening_start'] ?? '17:00';
            $eveningEnd = $data['evening_end'] ?? '17:30';
        }

        // 🔥 FIXED: Use self:: instead of $this->
        $morningStart = self::formatTime($morningStart);
        $morningEnd = self::formatTime($morningEnd);
        $afternoonStart = self::formatTime($afternoonStart);
        $afternoonEnd = self::formatTime($afternoonEnd);
        $eveningStart = self::formatTime($eveningStart);
        $eveningEnd = self::formatTime($eveningEnd);

        return self::updateOrCreate(
            ['day_name' => $dayName],
            [
                'morning_start' => $morningStart,
                'morning_end' => $morningEnd,
                'afternoon_start' => $afternoonStart,
                'afternoon_end' => $afternoonEnd,
                'evening_start' => $eveningStart,
                'evening_end' => $eveningEnd,
                'is_active' => true
            ]
        );
    }

    // 🔥 Helper to format time
    private static function formatTime($time)
    {
        // If already has seconds, return as is
        if (strlen($time) == 8 && substr_count($time, ':') == 2) {
            return $time;
        }
        // If only HH:MM, add :00
        if (strlen($time) == 5 && substr_count($time, ':') == 1) {
            return $time . ':00';
        }
        return '07:00:00';
    }

    public static function saveAllTimers($settings)
    {
        foreach ($settings as $dayName => $data) {
            self::saveTimer($dayName, $data);
        }
        return true;
    }

    public static function resetAllTimers()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            self::updateOrCreate(
                ['day_name' => $day],
                [
                    'morning_start' => '07:00:00',
                    'morning_end' => '07:30:00',
                    'afternoon_start' => '12:00:00',
                    'afternoon_end' => '12:30:00',
                    'evening_start' => '17:00:00',
                    'evening_end' => '17:30:00',
                    'is_active' => true
                ]
            );
        }
        return true;
    }
}