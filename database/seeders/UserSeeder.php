<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();

        $users = [
            [1, 'Diana binti Azman', '30', 'diana@taskakids.com', '013-344 3554', 'admin'],
            [2, 'Nurul Syuhada binti Rahim', '28', 'syuhada@taskakids.com', '019-887 6543', 'admin'],
            [3, 'Ahmad Fikri bin Hassan', '35', 'fikri@taskakids.com', '012-556 7890', 'admin'],
            [10, 'Norazila binti Mahmud', '32', 'norazila@gmail.com', '012-242 4534', 'parent1'],
            [11, 'Mohd Hafiz bin Ismail', '36', 'hafiz.ismail@gmail.com', '019-876 5432', 'parent2'],
            [13, 'Zulkifli bin Omar (Atuk)', '62', 'zulkifli.omar@gmail.com', '011-9945 8141', 'guardian'],
            [16, 'Rosnani binti Shuib', '34', 'rosnani.shuib@gmail.com', '018-775 4432', 'parent1'],
            [17, 'Azman bin Hashim', '38', 'azman.hashim@gmail.com', '011-5678 9800', 'parent2'],
            [18, 'Mak Cik Kamsiah', '58', 'kamsiah@gmail.com', '018-775 3232', 'guardian'],
            [19, 'Siti Hajar binti Rahman', '31', 'hajar.rahman@gmail.com', '016-554 3232', 'parent1'],
            [20, 'Farid bin Zainal', '35', 'farid.zainal@gmail.com', '017-889 9090', 'parent2'],
            [21, 'Pak Cik Dollah', '65', 'dollah@gmail.com', '018-997 8777', 'guardian'],
            [22, 'Noraini binti Samad', '33', 'noraini.samad@gmail.com', '013-889 7654', 'parent1'],
            [23, 'Rashid bin Ghazali', '37', 'rashid.ghazali@gmail.com', '019-334 5678', 'parent2'],
            [24, 'Mak Cik Timah', '60', 'timah@gmail.com', '017-773 9561', 'guardian'],
            [25, 'Kamarul bin Ariffin', '40', 'kamarul.ariffin@gmail.com', '017-773 9574', 'parent1'],
            [26, 'Zuraidah binti Mustafa', '38', 'zuraidah@gmail.com', '011-3345 7654', 'parent2'],
            [27, 'Pak Ngah Senawi', '68', 'senawi@gmail.com', '011-0045 8141', 'guardian'],
            [34, 'SITI ARBI', '29', 'sitiarbi@gmail.com', '012-987 6543', 'parent1'],
        ];

        foreach ($users as $u) {
            DB::table('users')->insert([
                'id'          => $u[0],
                'name'        => $u[1],
                'age'         => $u[2],
                'email'       => $u[3],
                'password'    => Hash::make('password'),
                'phone_number'=> $u[4],
                'role'        => $u[5],
                'verified'    => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('  ✓ users: ' . count($users));
    }
}
