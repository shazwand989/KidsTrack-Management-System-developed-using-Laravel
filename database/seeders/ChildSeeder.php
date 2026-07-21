<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildSeeder extends Seeder
{
    private array $maleNames   = ['Adam','Afiq','Aiman','Amir','Arif','Danial','Faris','Hadi','Hakim','Haziq',
                                   'Irfan','Ismail','Luqman','Mirza','Naufal','Rizqi','Syafiq','Zikri','Hariz','Imran',
                                   'Aqil','Danish','Fikri','Hilmi','Iqbal','Rayyan','Umar','Zafran','Amsyar','Fayyadh'];
    private array $femaleNames = ['Aina','Alia','Alya','Amira','Balqis','Damia','Dania','Farah','Hana','Intan',
                                   'Izzah','Jannah','Maisarah','Nadia','Qistina','Sofia','Yasmin','Zahra','Zara','Amani',
                                   'Aisyah','Aqilah','Batrisyia','Dhia','Hannah','Maryam','Naura','Safiyya','Wafiyya','Zulaikha'];
    private array $binBinti    = ['bin', 'binti'];
    private array $addresses   = [
        'Kampung Batu 11, Jementah', 'Taman Gemilang, 85000 Segamat',
        'Taman Mewah, 85000 Segamat', 'Kampung Tengah, 85200 Segamat',
        'Kampung Paya Lebar, 85200 Segamat', 'Taman Delima, 85000 Segamat',
        'Taman Seri Jementah, 85200 Segamat', 'Kampung Jawa, 85000 Segamat',
        'Taman Anggerik, 43000 Kajang', 'Taman Kempas, 81200 Johor Bahru',
    ];

    public function run(): void
    {
        DB::table('children')->truncate();

        // Get all main parents — each is a "family"
        $mainParents = DB::table('users')->where('role', 'parent1')->orderBy('id')->get();

        $counter = 1;
        $total   = 0;

        foreach ($mainParents as $parent) {
            // Each family has 1-3 children (random)
            $numChildren = mt_rand(1, 3);
            $isMale      = $this->binBinti[array_rand($this->binBinti)];

            for ($i = 0; $i < $numChildren; $i++) {
                $gender = mt_rand(0, 1);
                $firstName = $gender
                    ? $this->femaleNames[array_rand($this->femaleNames)]
                    : $this->maleNames[array_rand($this->maleNames)];
                $name = $firstName . ' ' . $this->binBinti[$gender] . ' ' . $this->extractLastName($parent->name);

                $age    = mt_rand(1, 4);
                $year   = 2026 - $age;
                $month  = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
                $day    = str_pad(mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
                $ic     = "$year$month$day-01-" . str_pad(mt_rand(100, 9999), 4, '0', STR_PAD_LEFT);
                $dob    = "$year-$month-$day";

                $classroomId = $age <= 2 ? 1 : ($age <= 3 ? 2 : 3);

                // ~10% chance of medical note / dietary
                $medical = null;
                $dietary = null;
                $roll = mt_rand(1, 100);
                if ($roll <= 5)  { $medical = 'Asma ringan'; }
                if ($roll > 90)  { $dietary = 'Vegetarian'; }
                elseif ($roll > 85) { $dietary = 'Alahan makanan laut'; }
                elseif ($roll > 80) { $dietary = 'Lactose intolerant'; }

                DB::table('children')->insert([
                    'name'            => $name,
                    'age'             => $age,
                    'ic_number'       => $ic,
                    'dob'             => $dob,
                    'address'         => $this->addresses[array_rand($this->addresses)],
                    'classroom_id'    => $classroomId,
                    'medical_notes'   => $medical,
                    'dietary'         => $dietary,
                    'is_active'       => true,
                    'enrollment_date' => now(),
                    'qr_code'         => 'KID-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                $counter++;
                $total++;
            }
        }

        $this->command->info('  ✓ children: ' . $total . ' (across ' . count($mainParents) . ' families)');
    }

    private function extractLastName(string $fullName): string
    {
        // "Norazila binti Mahmud" → "Mahmud"
        // "Mohd Hafiz bin Ismail" → "Ismail"
        $parts = explode(' ', $fullName);
        return end($parts);
    }
}
