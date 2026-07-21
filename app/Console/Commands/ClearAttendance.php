<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearAttendance extends Command
{
    protected $signature = 'attendance:clear 
                            {--date= : Clear only for a specific date (Y-m-d)} 
                            {--all : Clear ALL attendance records}';

    protected $description = 'Clear attendance records. Use --date to clear one day, --all for everything.';

    public function handle()
    {
        $date = $this->option('date');
        $all  = $this->option('all');

        if ($date) {
            $count = DB::table('attendance')->whereDate('date', $date)->delete();
            $this->info("✓ Deleted {$count} attendance records for {$date}");
        } elseif ($all) {
            if (!$this->confirm('Delete ALL attendance records? This cannot be undone.')) {
                $this->info('Cancelled.');
                return;
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('attendance')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('✓ All attendance records cleared.');
        } else {
            $this->error('Use --date=YYYY-MM-DD or --all');
            $this->line('  php artisan attendance:clear --date=2026-07-21');
            $this->line('  php artisan attendance:clear --all');
        }
    }
}
