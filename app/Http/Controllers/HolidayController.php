<?php

namespace App\Http\Controllers;

use App\Helpers\HolidayHelper;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function getHolidays($year)
    {
        $holidays = HolidayHelper::getHolidays($year);
        return response()->json([
            'success' => true,
            'year' => $year,
            'holidays' => $holidays
        ]);
    }

    public function checkDate($date)
    {
        return response()->json([
            'date' => $date,
            'is_holiday' => HolidayHelper::isHoliday($date),
            'name' => HolidayHelper::getHolidayName($date)
        ]);
    }

    public function getCurrentMonthHolidays(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        
        $holidays = HolidayHelper::getHolidays($year);
        
        $monthHolidays = [];
        foreach ($holidays as $date => $holiday) {
            if (date('m', strtotime($date)) == str_pad($month, 2, '0', STR_PAD_LEFT)) {
                $monthHolidays[$date] = $holiday;
            }
        }
        
        return response()->json([
            'success' => true,
            'year' => $year,
            'month' => $month,
            'holidays' => $monthHolidays
        ]);
    }
}