<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationClock extends Model
{
    protected $table = 'simulation_clock';

    protected $fillable = [
        'simulation_time',
        'morning_start',
        'morning_end',
        'evening_start',
        'evening_end',
        'use_simulation'
    ];

    protected $casts = [
        'simulation_time' => 'datetime',
        'use_simulation' => 'boolean',
    ];

    public static function getClock()
    {
        return self::first();
    }

    public static function getCurrentTime()
    {
        $clock = self::first();
        if ($clock && $clock->use_simulation) {
            return strtotime($clock->simulation_time);
        }
        return time();
    }

    public static function getFormattedTime()
    {
        $time = self::getCurrentTime();
        return date('h:i A', $time);
    }

    public static function getFormattedDate()
    {
        $time = self::getCurrentTime();
        return date('d F Y', $time);
    }

    public static function getTimerForToday()
    {
        $clock = self::first();
        if (!$clock) {
            return null;
        }

        return [
            'morning' => [
                'start' => date('H:i', strtotime($clock->morning_start)),
                'end' => date('H:i', strtotime($clock->morning_end))
            ],
            'evening' => [
                'start' => date('H:i', strtotime($clock->evening_start)),
                'end' => date('H:i', strtotime($clock->evening_end))
            ]
        ];
    }

    public static function getCurrentSlot()
    {
        $clock = self::first();
        if (!$clock) {
            return null;
        }

        $currentTime = self::getCurrentTime();
        $hour = date('H', $currentTime);
        $minute = date('i', $currentTime);
        $currentTimeInt = (int)($hour . $minute);

        $morningStart = (int)str_replace(':', '', $clock->morning_start);
        $morningEnd = (int)str_replace(':', '', $clock->morning_end);
        $eveningStart = (int)str_replace(':', '', $clock->evening_start);
        $eveningEnd = (int)str_replace(':', '', $clock->evening_end);

        if ($currentTimeInt >= $morningStart && $currentTimeInt <= $morningEnd) {
            return ['slot' => 'morning', 'type' => 'checkin', 'label' => 'Morning (Check-in)'];
        }

        if ($currentTimeInt >= $eveningStart && $currentTimeInt <= $eveningEnd) {
            return ['slot' => 'evening', 'type' => 'checkout', 'label' => 'Evening (Check-out)'];
        }

        return null;
    }

    public static function getStatus()
    {
        $slot = self::getCurrentSlot();
        if ($slot) {
            return [
                'status' => $slot['label'],
                'color' => 'green',
                'type' => $slot['type']
            ];
        }
        return [
            'status' => '🔒 Outside Operating Hours',
            'color' => 'red',
            'type' => null
        ];
    }

    public static function getGreeting()
    {
        $time = self::getCurrentTime();
        $hour = date('H', $time);

        if ($hour >= 5 && $hour < 12) {
            return '☀️ Good Morning';
        } elseif ($hour >= 12 && $hour < 18) {
            return '🌤️ Good Afternoon';
        } else {
            return '🌙 Good Evening';
        }
    }
}