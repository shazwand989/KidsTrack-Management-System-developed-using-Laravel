<?php

namespace App\Helper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HolidayHelper
{
    /**
     * Get Malaysia public holidays for a specific year
     */
    public static function getHolidays($year = null)
    {
        $year = $year ?? now()->year;
        
        // Cache untuk 7 hari (kurangkan API call)
        $cacheKey = "malaysia_holidays_{$year}";
        
        return Cache::remember($cacheKey, 60 * 60 * 24 * 7, function () use ($year) {
            // Try Nager.Date API (free, no API key needed)
            $response = Http::get("https://date.nager.at/api/v3/PublicHolidays/{$year}/MY");
            
            if ($response->successful()) {
                $holidays = [];
                foreach ($response->json() as $holiday) {
                    $holidays[$holiday['date']] = [
                        'name' => $holiday['localName'] ?? $holiday['name'],
                        'type' => 'public'
                    ];
                }
                return $holidays;
            }
            
            // Fallback: Manual holidays for Malaysia
            return self::getManualHolidays($year);
        });
    }
    
    /**
     * Manual holidays for Malaysia (fallback)
     */
    private static function getManualHolidays(int $year): array
    {
        $holidays = [
            // National Holidays (fixed dates)
            "{$year}-01-01" => ['name' => "New Year's Day", 'type' => 'public'],
            "{$year}-05-01" => ['name' => "Labour Day", 'type' => 'public'],
            "{$year}-06-01" => ['name' => "Gawai Dayak", 'type' => 'public'],
            "{$year}-08-31" => ['name' => "National Day (Merdeka)", 'type' => 'public'],
            "{$year}-09-16" => ['name' => "Malaysia Day", 'type' => 'public'],
            "{$year}-12-25" => ['name' => "Christmas Day", 'type' => 'public'],
        ];
        
        return $holidays;
    }
    
    /**
     * Check if a specific date is a holiday
     */
    public static function isHoliday(string $date): bool
    {
        $year = date('Y', strtotime($date));
        $holidays = self::getHolidays($year);
        return isset($holidays[$date]);
    }
    
    /**
     * Get holiday name for a specific date
     */
    public static function getHolidayName(string $date): ?string
    {
        $year = date('Y', strtotime($date));
        $holidays = self::getHolidays($year);
        return $holidays[$date]['name'] ?? null;
    }
}