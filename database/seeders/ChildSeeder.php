<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('children')->truncate();

        $children = [
            [1, 'Adam bin Mohd Hafiz', 2, '200704-01-0123', '2024-07-04', 'Kampung Batu 11, Jementah', 1, 'Alahan makanan laut'],
            [2, 'Aisyah binti Mohd Hafiz', 1, '200805-01-0456', '2025-08-05', 'Kampung Batu 11, Jementah', 1, null],
            [3, 'Aqilah binti Azman', 2, '200706-01-0789', '2024-06-06', 'Taman Gemilang, 85000 Segamat', 1, 'Asma ringan'],
            [4, 'Danish bin Farid', 2, '200708-01-0321', '2024-08-08', 'Taman Mewah, 85000 Segamat', 1, null],
            [5, 'Damia binti Farid', 1, '200910-01-0654', '2025-10-09', 'Taman Mewah, 85000 Segamat', 1, null],
            [6, 'Farah binti Rashid', 2, '200709-01-0987', '2024-09-09', 'Kampung Tengah, 85200 Segamat', 1, 'Lactose intolerant'],
            [7, 'Haris bin Rashid', 1, '201001-01-0432', '2025-10-10', 'Kampung Tengah, 85200 Segamat', 1, 'Vegetarian'],
            [8, 'Irfan bin Kamarul', 2, '200710-01-0789', '2024-10-10', 'Kampung Paya Lebar, 85200 Segamat', 1, null],
            [9, 'Sarah binti Kamarul', 3, '200605-01-0345', '2023-05-06', 'Kampung Paya Lebar, 85200 Segamat', 2, null],
            [10, 'Iman binti Kamarul', 3, '200608-01-0678', '2023-08-06', 'Kampung Paya Lebar, 85200 Segamat', 2, null],
            [13, 'Afiqah', 3, '200703-14-0001', '2023-03-14', 'No. 55, Jalan Delima, Taman Delima', 2, null],
            [14, 'Afiq', 3, '200704-14-0002', '2023-04-14', 'No. 55, Jalan Delima, Taman Delima', 2, null],
        ];

        foreach ($children as $c) {
            DB::table('children')->insert([
                'id'              => $c[0],
                'name'            => $c[1],
                'age'             => $c[2],
                'ic_number'       => $c[3],
                'dob'             => $c[4],
                'address'         => $c[5],
                'classroom_id'    => $c[6],
                'medical_notes'   => $c[7],
                'dietary'         => $c[7] === 'Vegetarian' ? 'Vegetarian' : ($c[7] === 'Lactose intolerant' ? 'Lactose intolerant' : ($c[7] ?? null)),
                'is_active'       => true,
                'enrollment_date' => now(),
                'qr_code'         => 'KID-' . str_pad($c[0], 4, '0', STR_PAD_LEFT),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Correct dietary/medical for specific children
        DB::table('children')->where('id', 1)->update(['dietary' => 'Alahan makanan laut']);
        DB::table('children')->where('id', 6)->update(['dietary' => 'Lactose intolerant']);
        DB::table('children')->where('id', 7)->update(['dietary' => 'Vegetarian']);
        DB::table('children')->where('id', 3)->update(['medical_notes' => 'Asma ringan']);

        $this->command->info('  ✓ children: ' . count($children));
    }
}
