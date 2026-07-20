<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KidsTrackSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding KidsTrack with Malaysian data...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->seedUsers();
        $this->seedClassrooms();
        $this->seedTeachers();
        $this->seedParents();
        $this->seedSecondParents();
        $this->seedGuardians();
        $this->seedChildren();
        $this->seedAttendance();
        $this->seedTimerSettings();
        $this->seedSimulationClock();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('Seeding complete!');
    }

    private function seedUsers(): void
    {
        // Admin staff + Parent accounts
        DB::table('users')->insert([
            ['id' => 1,  'name' => 'Diana binti Azman',     'age' => null, 'email' => 'diana@taskakids.com',     'password' => '$2y$12$bN5tG.udD8DB3VeQeO1jWOVaWPEEH39wTWaOe.se.DtgdF6GD98VS', 'phone_number' => '013-344 3554', 'address' => 'No. 12, Jalan Kempas 5, Taman Kempas, 81200 Johor Bahru, Johor',          'role' => 'admin',    'remember_token' => null, 'created_at' => '2026-01-15 08:00:00', 'updated_at' => '2026-01-15 08:00:00'],
            ['id' => 2,  'name' => 'Nurul Syuhada binti Rahim', 'age' => null, 'email' => 'syuhada@taskakids.com',  'password' => '$2y$12$svSwla.m9JVgogd1wG7VgOEr.1VzIOrNXSllWcBCiNXbx.esQtLiK', 'phone_number' => '019-887 6543', 'address' => 'No. 45, Jalan Anggerik 2, Taman Anggerik, 43000 Kajang, Selangor',          'role' => 'admin',    'remember_token' => null, 'created_at' => '2026-01-20 09:30:00', 'updated_at' => '2026-01-20 09:30:00'],
            ['id' => 3,  'name' => 'Ahmad Fikri bin Hassan',   'age' => null, 'email' => 'fikri@taskakids.com',     'password' => '$2y$12$PBb2kLPw7g6IqFXW2rOqveOhqJ8xDv90dPFhn.hoDbeIy.HVys3E6', 'phone_number' => '012-556 7890', 'address' => 'No. 8, Lorong Cempaka 3, Taman Cempaka, 68000 Ampang, Selangor',              'role' => 'admin',    'remember_token' => null, 'created_at' => '2026-02-01 07:45:00', 'updated_at' => '2026-02-01 07:45:00'],
            // Parents
            ['id' => 10, 'name' => 'Norazila binti Mahmud',    'age' => null, 'email' => 'norazila@gmail.com',      'password' => '$2y$12$giygbwLSo/FHtSTBJd1J7eCzXz8YcAZesinu5aX62Y2LKsuc7ICei', 'phone_number' => null, 'address' => null, 'role' => 'parent1',  'remember_token' => null, 'created_at' => '2026-03-10 14:00:00', 'updated_at' => '2026-03-10 14:00:00'],
            ['id' => 11, 'name' => 'Mohd Hafiz bin Ismail',     'age' => null, 'email' => 'hafiz.ismail@gmail.com',   'password' => '$2y$12$p7ufHeyJof5z55NwBJDn8.2b.CSyFXj6f1mpljwg9j2HApz5f3Cwu', 'phone_number' => null, 'address' => null, 'role' => 'parent1',  'remember_token' => null, 'created_at' => '2026-03-15 10:20:00', 'updated_at' => '2026-03-15 10:20:00'],
            ['id' => 12, 'name' => 'Siti Aminah binti Abdullah','age' => null, 'email' => 'aminah.abdullah@gmail.com','password' => '$2y$12$g/jgL.GT8npO27KNXDbAA.x4Ep2EFXbfGOUQ5tcqPFp3PJc7VVIZi', 'phone_number' => null, 'address' => null, 'role' => 'parent2',  'remember_token' => null, 'created_at' => '2026-03-15 10:25:00', 'updated_at' => '2026-03-15 10:25:00'],
            ['id' => 13, 'name' => 'Zulkifli bin Omar',         'age' => null, 'email' => 'zulkifli.omar@gmail.com',  'password' => '$2y$12$W2YoFuvxfYXdvxa5iQPcmefuoHAGnE2uKyoTxTcaKbjKhxYkEqRWW', 'phone_number' => null, 'address' => null, 'role' => 'guardian', 'remember_token' => null, 'created_at' => '2026-03-15 10:30:00', 'updated_at' => '2026-03-15 10:30:00'],
            ['id' => 16, 'name' => 'Rosnani binti Shuib',       'age' => null, 'email' => 'rosnani.shuib@gmail.com',  'password' => '$2y$12$UVXs9Mo6vabuHUaN7I5JquN8NvJK74ZbBZTa5.IKv9ukxNhtIYDzS', 'phone_number' => null, 'address' => null, 'role' => 'parent1',  'remember_token' => null, 'created_at' => '2026-04-05 08:15:00', 'updated_at' => '2026-04-05 08:15:00'],
            ['id' => 17, 'name' => 'Azman bin Hashim',          'age' => null, 'email' => 'azman.hashim@gmail.com',   'password' => '$2y$12$7sqQDt6pEGvLnYr.P.2tmOhT4u849yaeJ51jxTA.sNA0bDDWQv1Ai', 'phone_number' => null, 'address' => null, 'role' => 'parent2',  'remember_token' => null, 'created_at' => '2026-04-05 08:20:00', 'updated_at' => '2026-04-05 08:20:00'],
            ['id' => 18, 'name' => 'Mak Cik Kamsiah',           'age' => null, 'email' => 'kamsiah@gmail.com',        'password' => '$2y$12$V1yQGpL8AlkNhBy9sr0TueMzjyYI9zJUmct0Mt1E7J5buZ5QfwOr6', 'phone_number' => null, 'address' => null, 'role' => 'guardian', 'remember_token' => null, 'created_at' => '2026-04-05 08:25:00', 'updated_at' => '2026-04-05 08:25:00'],
            ['id' => 19, 'name' => 'Siti Hajar binti Rahman',   'age' => null, 'email' => 'hajar.rahman@gmail.com',   'password' => '$2y$12$ZiLebP/oR0RZ36T.TxrLaejCawsxv8QY3EGIbtFt2ftE5twIXCMp6', 'phone_number' => null, 'address' => null, 'role' => 'parent1',  'remember_token' => null, 'created_at' => '2026-04-20 11:00:00', 'updated_at' => '2026-04-20 11:00:00'],
            ['id' => 20, 'name' => 'Farid bin Zainal',           'age' => null, 'email' => 'farid.zainal@gmail.com',   'password' => '$2y$12$DkZgzKDqUo/3pcK0I9Wc7./5CyROu0RSg9N/6fnlX/P7kTQbD59Wm', 'phone_number' => null, 'address' => null, 'role' => 'parent2',  'remember_token' => null, 'created_at' => '2026-04-20 11:05:00', 'updated_at' => '2026-04-20 11:05:00'],
            ['id' => 21, 'name' => 'Pak Cik Dollah',             'age' => null, 'email' => 'dollah@gmail.com',        'password' => '$2y$12$5m8TC7u/STX.WAJzYGSKUO6yGFzdkE8VObmhxB2mPUP5u4IDrzHOG', 'phone_number' => null, 'address' => null, 'role' => 'guardian', 'remember_token' => null, 'created_at' => '2026-04-20 11:10:00', 'updated_at' => '2026-04-20 11:10:00'],
            ['id' => 22, 'name' => 'Noraini binti Samad',        'age' => null, 'email' => 'noraini.samad@gmail.com',  'password' => '$2y$12$lpgdw3hs7wgHj2i3YV1iWOJiaXrztVwqq.JvihGiChJyKtD5EWvC2', 'phone_number' => null, 'address' => null, 'role' => 'parent1',  'remember_token' => null, 'created_at' => '2026-05-10 09:30:00', 'updated_at' => '2026-05-10 09:30:00'],
            ['id' => 23, 'name' => 'Rashid bin Ghazali',         'age' => null, 'email' => 'rashid.ghazali@gmail.com', 'password' => '$2y$12$IPQJFxYXObVOS5iUb2MXO.qgo8dHZpTsXODwwKnPMRx.L3Vmq8vsu', 'phone_number' => null, 'address' => null, 'role' => 'parent2',  'remember_token' => null, 'created_at' => '2026-05-10 09:35:00', 'updated_at' => '2026-05-10 09:35:00'],
            ['id' => 24, 'name' => 'Mak Cik Timah',              'age' => null, 'email' => 'timah@gmail.com',          'password' => '$2y$12$HSYqKBUL3lh6rEO7HbQENexYGTx.pBCv8Ty3ADJ3V5s7A1ikgduEC', 'phone_number' => null, 'address' => null, 'role' => 'guardian', 'remember_token' => null, 'created_at' => '2026-05-10 09:40:00', 'updated_at' => '2026-05-10 09:40:00'],
            ['id' => 25, 'name' => 'Kamarul bin Ariffin',        'age' => null, 'email' => 'kamarul.ariffin@gmail.com','password' => '$2y$12$lqMit4Se4gInden3m99FnukkbMj12eZfoK/.yzgJWiI7wFHJxELpG', 'phone_number' => null, 'address' => null, 'role' => 'parent1',  'remember_token' => null, 'created_at' => '2026-05-25 13:00:00', 'updated_at' => '2026-05-25 13:00:00'],
            ['id' => 26, 'name' => 'Zuraidah binti Mustafa',     'age' => null, 'email' => 'zuraidah@gmail.com',       'password' => '$2y$12$T.pDDH/jvNfeKH2afehWiO3Ssf64.HEDZjwOAAfg/UTS2m72PB40m', 'phone_number' => null, 'address' => null, 'role' => 'parent2',  'remember_token' => null, 'created_at' => '2026-05-25 13:05:00', 'updated_at' => '2026-05-25 13:05:00'],
            ['id' => 27, 'name' => 'Pak Ngah Senawi',            'age' => null, 'email' => 'senawi@gmail.com',         'password' => '$2y$12$1I2nAQ10tqxhcUpmvDh5n.miMtODa/w8suJHE2DdqSD3.L6HmR9JK', 'phone_number' => null, 'address' => null, 'role' => 'guardian', 'remember_token' => null, 'created_at' => '2026-05-25 13:10:00', 'updated_at' => '2026-05-25 13:10:00'],
        ]);
        $this->command->info('  ✓ users: 21');
    }

    private function seedClassrooms(): void
    {
        DB::table('classrooms')->insert([
            ['id' => 1, 'name' => 'Ceria 1 (2 Tahun)', 'code' => 'C1', 'age_group' => '2 Tahun', 'min_age' => 1, 'max_age' => 2, 'capacity' => 15, 'teacher_id' => null, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'status' => 'active', 'description' => 'Kelas untuk kanak-kanak berumur 1-2 tahun', 'color' => '#45B7D1', 'created_at' => '2026-06-13 06:34:44', 'updated_at' => '2026-06-13 06:34:44'],
            ['id' => 2, 'name' => 'Ceria 2 (3 Tahun)', 'code' => 'C2', 'age_group' => '3 Tahun', 'min_age' => 2, 'max_age' => 3, 'capacity' => 15, 'teacher_id' => 1,    'start_time' => '08:00:00', 'end_time' => '17:00:00', 'status' => 'active', 'description' => 'Kelas untuk kanak-kanak berumur 2-3 tahun', 'color' => '#FF6B6B', 'created_at' => '2026-07-19 01:15:25', 'updated_at' => '2026-07-19 01:15:25'],
            ['id' => 3, 'name' => 'Bestari (4 Tahun)',  'code' => 'B1', 'age_group' => '4 Tahun', 'min_age' => 3, 'max_age' => 4, 'capacity' => 15, 'teacher_id' => null, 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'status' => 'active', 'description' => 'Kelas untuk kanak-kanak berumur 3-4 tahun', 'color' => '#4ECDC4', 'created_at' => '2026-07-19 01:15:25', 'updated_at' => '2026-07-19 01:15:25'],
        ]);
        $this->command->info('  ✓ classrooms: 3');
    }

    private function seedTeachers(): void
    {
        DB::table('teachers')->insert([
            ['id' => 1, 'name' => 'Hasanatun Nasuha binti Md Azlee', 'position' => 'Guru Besar / Head Teacher', 'age' => 35, 'phone' => '011-3345 8141', 'email' => 'hasanatun@taskakids.com', 'address' => "Lot 456, Kampung Batu 11,\nJementah, 85200 Segamat, Johor", 'photo' => 'teachers/hasanatun.jpg', 'classroom_id' => 1, 'status' => 'active', 'qualifications' => 'Diploma Pendidikan Awal Kanak-Kanak (KPM)', 'join_date' => '2025-01-02', 'created_at' => '2026-06-13 06:46:37', 'updated_at' => '2026-06-13 06:46:37'],
            ['id' => 2, 'name' => 'Nur Fatimah binti Azmi',          'position' => 'Guru Kanan',                'age' => 29, 'phone' => '013-778 9901', 'email' => 'fatimah@taskakids.com',   'address' => "No. 23, Taman Seri Jementah,\n85200 Segamat, Johor",       'photo' => null, 'classroom_id' => 2, 'status' => 'active', 'qualifications' => 'Sijil Perguruan Prasekolah',            'join_date' => '2025-03-15', 'created_at' => '2026-07-01 08:00:00', 'updated_at' => '2026-07-01 08:00:00'],
        ]);
        $this->command->info('  ✓ teachers: 2');
    }

    private function seedParents(): void
    {
        DB::table('parents')->insert([
            ['id' => 1,  'user_id' => 10, 'name' => 'Norazila binti Mahmud',    'age' => '32', 'phone' => '012-242 4534', 'address' => "Lot 456, Kampung Batu 11,\nJementah, 85200 Segamat, Johor", 'photo' => null, 'type' => 'main',   'verified' => true,  'emergency' => true,  'created_at' => '2026-03-10 14:00:00', 'updated_at' => '2026-03-10 14:00:00'],
            ['id' => 2,  'user_id' => 11, 'name' => 'Mohd Hafiz bin Ismail',     'age' => '36', 'phone' => '019-876 5432', 'address' => "Lot 456, Kampung Batu 11,\nJementah, 85200 Segamat, Johor", 'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-03-10 14:05:00', 'updated_at' => '2026-03-10 14:05:00'],
            ['id' => 3,  'user_id' => 16, 'name' => 'Rosnani binti Shuib',       'age' => '34', 'phone' => '018-775 4432', 'address' => "No. 8, Jalan Gemilang,\nTaman Gemilang, 85000 Segamat, Johor", 'photo' => null, 'type' => 'main',   'verified' => true,  'emergency' => true,  'created_at' => '2026-04-05 08:15:00', 'updated_at' => '2026-04-05 08:15:00'],
            ['id' => 4,  'user_id' => 17, 'name' => 'Azman bin Hashim',          'age' => '38', 'phone' => '011-5678 9800', 'address' => "No. 8, Jalan Gemilang,\nTaman Gemilang, 85000 Segamat, Johor", 'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-04-05 08:20:00', 'updated_at' => '2026-04-05 08:20:00'],
            ['id' => 5,  'user_id' => 19, 'name' => 'Siti Hajar binti Rahman',   'age' => '31', 'phone' => '016-554 3232', 'address' => "No. 15, Taman Mewah,\n85000 Segamat, Johor",                'photo' => null, 'type' => 'main',   'verified' => true,  'emergency' => true,  'created_at' => '2026-04-20 11:00:00', 'updated_at' => '2026-04-20 11:00:00'],
            ['id' => 6,  'user_id' => 20, 'name' => 'Farid bin Zainal',           'age' => '35', 'phone' => '017-889 9090', 'address' => "No. 15, Taman Mewah,\n85000 Segamat, Johor",                'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-04-20 11:05:00', 'updated_at' => '2026-04-20 11:05:00'],
            ['id' => 7,  'user_id' => 22, 'name' => 'Noraini binti Samad',        'age' => '33', 'phone' => '013-889 7654', 'address' => "No. 3, Jalan Muhibbah,\nKampung Tengah, 85200 Segamat, Johor",'photo' => null, 'type' => 'main',   'verified' => true,  'emergency' => true,  'created_at' => '2026-05-10 09:30:00', 'updated_at' => '2026-05-10 09:30:00'],
            ['id' => 8,  'user_id' => 23, 'name' => 'Rashid bin Ghazali',         'age' => '37', 'phone' => '019-334 5678', 'address' => "No. 3, Jalan Muhibbah,\nKampung Tengah, 85200 Segamat, Johor",'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-05-10 09:35:00', 'updated_at' => '2026-05-10 09:35:00'],
            ['id' => 9,  'user_id' => 25, 'name' => 'Kamarul bin Ariffin',        'age' => '40', 'phone' => '017-773 9574', 'address' => "Lot 789, Kampung Paya Lebar,\n85200 Segamat, Johor",            'photo' => null, 'type' => 'main',   'verified' => true,  'emergency' => true,  'created_at' => '2026-05-25 13:00:00', 'updated_at' => '2026-05-25 13:00:00'],
            ['id' => 10, 'user_id' => 26, 'name' => 'Zuraidah binti Mustafa',     'age' => '38', 'phone' => '011-3345 7654', 'address' => "Lot 789, Kampung Paya Lebar,\n85200 Segamat, Johor",            'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-05-25 13:05:00', 'updated_at' => '2026-05-25 13:05:00'],
        ]);
        $this->command->info('  ✓ parents: 10');
    }

    private function seedSecondParents(): void
    {
        DB::table('second_parents')->insert([
            ['id' => 1,  'parent_id' => 1, 'user_id' => 11, 'name' => 'Mohd Hafiz bin Ismail',     'age' => '36', 'phone' => '019-876 5432', 'address' => "Lot 456, Kampung Batu 11,\nJementah, 85200 Segamat, Johor", 'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-03-10 14:05:00', 'updated_at' => '2026-03-10 14:05:00'],
            ['id' => 2,  'parent_id' => 3, 'user_id' => 17, 'name' => 'Azman bin Hashim',          'age' => '38', 'phone' => '011-5678 9800', 'address' => "No. 8, Jalan Gemilang,\nTaman Gemilang, 85000 Segamat, Johor", 'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-04-05 08:20:00', 'updated_at' => '2026-04-05 08:20:00'],
            ['id' => 3,  'parent_id' => 5, 'user_id' => 20, 'name' => 'Farid bin Zainal',           'age' => '35', 'phone' => '017-889 9090', 'address' => "No. 15, Taman Mewah,\n85000 Segamat, Johor",                'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-04-20 11:05:00', 'updated_at' => '2026-04-20 11:05:00'],
            ['id' => 4,  'parent_id' => 7, 'user_id' => 23, 'name' => 'Rashid bin Ghazali',         'age' => '37', 'phone' => '019-334 5678', 'address' => "No. 3, Jalan Muhibbah,\nKampung Tengah, 85200 Segamat, Johor",'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-05-10 09:35:00', 'updated_at' => '2026-05-10 09:35:00'],
            ['id' => 5,  'parent_id' => 9, 'user_id' => 26, 'name' => 'Zuraidah binti Mustafa',     'age' => '38', 'phone' => '011-3345 7654', 'address' => "Lot 789, Kampung Paya Lebar,\n85200 Segamat, Johor",            'photo' => null, 'type' => 'second', 'verified' => true,  'emergency' => false, 'created_at' => '2026-05-25 13:05:00', 'updated_at' => '2026-05-25 13:05:00'],
        ]);
        $this->command->info('  ✓ second_parents: 5');
    }

    private function seedGuardians(): void
    {
        DB::table('guardians')->insert([
            ['id' => 1, 'parent_id' => 1, 'user_id' => null, 'name' => 'Zulkifli bin Omar (Atuk)',     'age' => '62', 'phone' => '011-9945 8141', 'address' => "Kampung Batu 11, 85200 Segamat, Johor",      'photo' => null, 'type' => 'guardian', 'verified' => true,  'emergency' => true,  'created_at' => '2026-03-10 14:10:00', 'updated_at' => '2026-03-10 14:10:00'],
            ['id' => 2, 'parent_id' => 3, 'user_id' => 18, 'name' => 'Mak Cik Kamsiah (Nenek Saudara)', 'age' => '58', 'phone' => '018-775 3232', 'address' => "Kampung Jawa, 85000 Segamat, Johor",            'photo' => null, 'type' => 'guardian', 'verified' => true,  'emergency' => true,  'created_at' => '2026-04-05 08:25:00', 'updated_at' => '2026-04-05 08:25:00'],
            ['id' => 3, 'parent_id' => 5, 'user_id' => 21, 'name' => 'Pak Cik Dollah (Atuk Saudara)',   'age' => '65', 'phone' => '018-997 8777', 'address' => "Kampung Tengah, 85200 Segamat, Johor",          'photo' => null, 'type' => 'guardian', 'verified' => true,  'emergency' => true,  'created_at' => '2026-04-20 11:10:00', 'updated_at' => '2026-04-20 11:10:00'],
            ['id' => 4, 'parent_id' => 7, 'user_id' => 24, 'name' => 'Mak Cik Timah (Nenek)',           'age' => '60', 'phone' => '017-773 9561', 'address' => "Kampung Tengah, 85200 Segamat, Johor",          'photo' => null, 'type' => 'guardian', 'verified' => true,  'emergency' => false, 'created_at' => '2026-05-10 09:40:00', 'updated_at' => '2026-05-10 09:40:00'],
            ['id' => 5, 'parent_id' => 9, 'user_id' => 27, 'name' => 'Pak Ngah Senawi (Atuk)',          'age' => '68', 'phone' => '011-0045 8141', 'address' => "Kampung Paya Lebar, 85200 Segamat, Johor",     'photo' => null, 'type' => 'guardian', 'verified' => true,  'emergency' => true,  'created_at' => '2026-05-25 13:10:00', 'updated_at' => '2026-05-25 13:10:00'],
        ]);
        $this->command->info('  ✓ guardians: 5');
    }

    private function seedChildren(): void
    {
        DB::table('children')->insert([
            // Family 1: Norazila & Hafiz — 2 kids
            ['id' => 1,  'name' => 'Adam bin Mohd Hafiz',         'age' => 2, 'ic_number' => '200704-01-0123', 'dob' => '2024-07-04', 'address' => 'Kampung Batu 11, Jementah, 85200 Segamat, Johor', 'photo' => null, 'qr_code' => 'KID-0001', 'qr_code_url' => null, 'parent_id' => 1, 'second_parent_id' => 1, 'guardian_id' => 1, 'medical_notes' => null, 'dietary' => 'Tidak boleh makan makanan laut (alahan)', 'is_active' => true, 'enrollment_date' => '2026-03-15', 'classroom_id' => 1, 'created_at' => '2026-03-15 08:00:00', 'updated_at' => '2026-03-15 08:00:00'],
            ['id' => 2,  'name' => 'Aisyah binti Mohd Hafiz',      'age' => 1, 'ic_number' => '200805-01-0456', 'dob' => '2025-08-05', 'address' => 'Kampung Batu 11, Jementah, 85200 Segamat, Johor', 'photo' => null, 'qr_code' => 'KID-0002', 'qr_code_url' => null, 'parent_id' => 1, 'second_parent_id' => 1, 'guardian_id' => 1, 'medical_notes' => null, 'dietary' => null, 'is_active' => true, 'enrollment_date' => '2026-06-01', 'classroom_id' => 1, 'created_at' => '2026-06-01 08:00:00', 'updated_at' => '2026-06-01 08:00:00'],
            // Family 2: Rosnani & Azman — 1 kid
            ['id' => 3,  'name' => 'Aqilah binti Azman',           'age' => 2, 'ic_number' => '200706-01-0789', 'dob' => '2024-06-06', 'address' => 'Taman Gemilang, 85000 Segamat, Johor',            'photo' => null, 'qr_code' => 'KID-0003', 'qr_code_url' => null, 'parent_id' => 3, 'second_parent_id' => 2, 'guardian_id' => 2, 'medical_notes' => 'Asma ringan (inhaler di dalam beg)', 'dietary' => null, 'is_active' => true, 'enrollment_date' => '2026-04-10', 'classroom_id' => 1, 'created_at' => '2026-04-10 08:00:00', 'updated_at' => '2026-04-10 08:00:00'],
            // Family 3: Siti Hajar & Farid — 2 kids
            ['id' => 4,  'name' => 'Danish bin Farid',             'age' => 2, 'ic_number' => '200708-01-0321', 'dob' => '2024-08-08', 'address' => 'Taman Mewah, 85000 Segamat, Johor',               'photo' => null, 'qr_code' => 'KID-0004', 'qr_code_url' => null, 'parent_id' => 5, 'second_parent_id' => 3, 'guardian_id' => 3, 'medical_notes' => null, 'dietary' => null, 'is_active' => true, 'enrollment_date' => '2026-05-01', 'classroom_id' => 1, 'created_at' => '2026-05-01 08:00:00', 'updated_at' => '2026-05-01 08:00:00'],
            ['id' => 5,  'name' => 'Damia binti Farid',            'age' => 1, 'ic_number' => '200910-01-0654', 'dob' => '2025-10-09', 'address' => 'Taman Mewah, 85000 Segamat, Johor',               'photo' => null, 'qr_code' => 'KID-0005', 'qr_code_url' => null, 'parent_id' => 5, 'second_parent_id' => 3, 'guardian_id' => 3, 'medical_notes' => null, 'dietary' => null, 'is_active' => true, 'enrollment_date' => '2026-06-15', 'classroom_id' => 1, 'created_at' => '2026-06-15 08:00:00', 'updated_at' => '2026-06-15 08:00:00'],
            // Family 4: Noraini & Rashid — 2 kids
            ['id' => 6,  'name' => 'Farah binti Rashid',           'age' => 2, 'ic_number' => '200709-01-0987', 'dob' => '2024-09-09', 'address' => 'Kampung Tengah, 85200 Segamat, Johor',             'photo' => null, 'qr_code' => 'KID-0006', 'qr_code_url' => null, 'parent_id' => 7, 'second_parent_id' => 4, 'guardian_id' => 4, 'medical_notes' => null, 'dietary' => 'Tidak boleh minum susu lembu (Lactose intolerant)', 'is_active' => true, 'enrollment_date' => '2026-05-15', 'classroom_id' => 1, 'created_at' => '2026-05-15 08:00:00', 'updated_at' => '2026-05-15 08:00:00'],
            ['id' => 7,  'name' => 'Haris bin Rashid',             'age' => 1, 'ic_number' => '201001-01-0432', 'dob' => '2025-10-10', 'address' => 'Kampung Tengah, 85200 Segamat, Johor',             'photo' => null, 'qr_code' => 'KID-0007', 'qr_code_url' => null, 'parent_id' => 7, 'second_parent_id' => 4, 'guardian_id' => 4, 'medical_notes' => null, 'dietary' => 'Vegetarian', 'is_active' => true, 'enrollment_date' => '2026-07-01', 'classroom_id' => 1, 'created_at' => '2026-07-01 08:00:00', 'updated_at' => '2026-07-01 08:00:00'],
            // Family 5: Kamarul & Zuraidah — 3 kids (one in Ceria 1, two in Ceria 2)
            ['id' => 8,  'name' => 'Irfan bin Kamarul',            'age' => 2, 'ic_number' => '200710-01-0789', 'dob' => '2024-10-10', 'address' => 'Kampung Paya Lebar, 85200 Segamat, Johor',          'photo' => null, 'qr_code' => 'KID-0008', 'qr_code_url' => null, 'parent_id' => 9, 'second_parent_id' => 5, 'guardian_id' => 5, 'medical_notes' => null, 'dietary' => null, 'is_active' => true, 'enrollment_date' => '2026-06-01', 'classroom_id' => 1, 'created_at' => '2026-06-01 08:00:00', 'updated_at' => '2026-06-01 08:00:00'],
            ['id' => 9,  'name' => 'Sarah binti Kamarul',          'age' => 3, 'ic_number' => '200605-01-0345', 'dob' => '2023-05-06', 'address' => 'Kampung Paya Lebar, 85200 Segamat, Johor',          'photo' => null, 'qr_code' => 'KID-0009', 'qr_code_url' => null, 'parent_id' => 9, 'second_parent_id' => 5, 'guardian_id' => 5, 'medical_notes' => null, 'dietary' => null, 'is_active' => true, 'enrollment_date' => '2026-01-02', 'classroom_id' => 2, 'created_at' => '2026-01-02 08:00:00', 'updated_at' => '2026-01-02 08:00:00'],
            ['id' => 10, 'name' => 'Iman binti Kamarul',           'age' => 3, 'ic_number' => '200608-01-0678', 'dob' => '2023-08-06', 'address' => 'Kampung Paya Lebar, 85200 Segamat, Johor',          'photo' => null, 'qr_code' => 'KID-0010', 'qr_code_url' => null, 'parent_id' => 9, 'second_parent_id' => 5, 'guardian_id' => 5, 'medical_notes' => null, 'dietary' => null, 'is_active' => true, 'enrollment_date' => '2026-01-02', 'classroom_id' => 2, 'created_at' => '2026-01-02 08:00:00', 'updated_at' => '2026-01-02 08:00:00'],
        ]);
        $this->command->info('  ✓ children: 10');
    }

    private function seedAttendance(): void
    {
        DB::table('attendance')->insert([
            // Adam (child 1)
            ['id' => 1,  'child_id' => 1, 'parent_id' => 1, 'date' => '2026-07-14', 'status' => 'checkin',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:15:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Norazila)', 'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-13 23:15:00', 'updated_at' => '2026-07-13 23:15:00'],
            ['id' => 2,  'child_id' => 1, 'parent_id' => 1, 'date' => '2026-07-15', 'status' => 'checkout',     'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:20:00', 'checkout_time' => '17:15:00', 'drop_off_by' => 'Ayah (Hafiz)',   'pickup_by' => 'Ibu (Norazila)',  'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-14 23:20:00', 'updated_at' => '2026-07-15 09:15:00'],
            ['id' => 3,  'child_id' => 1, 'parent_id' => null, 'date' => '2026-07-16', 'status' => 'checkin',  'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:10:00', 'checkout_time' => null,       'drop_off_by' => 'Atuk (Zulkifli)', 'pickup_by' => null,           'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-15 23:10:00', 'updated_at' => '2026-07-15 23:10:00'],
            // Aisyah (child 2)
            ['id' => 4,  'child_id' => 2, 'parent_id' => 1, 'date' => '2026-07-14', 'status' => 'checkin',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:15:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Norazila)', 'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-13 23:15:00', 'updated_at' => '2026-07-13 23:15:00'],
            ['id' => 5,  'child_id' => 2, 'parent_id' => 1, 'date' => '2026-07-15', 'status' => 'checkout',     'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:20:00', 'checkout_time' => '17:15:00', 'drop_off_by' => 'Ayah (Hafiz)',   'pickup_by' => 'Ibu (Norazila)',  'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-14 23:20:00', 'updated_at' => '2026-07-15 09:15:00'],
            // Aqilah (child 3)
            ['id' => 6,  'child_id' => 3, 'parent_id' => 3, 'date' => '2026-07-14', 'status' => 'late',         'confirmed' => false, 'confirmed_at' => null, 'late_reason' => 'Kereta rosak', 'checkin_time' => '08:15:00', 'checkout_time' => null,  'drop_off_by' => 'Ibu (Rosnani)', 'pickup_by' => null,            'is_verified' => true,  'notes' => 'Maklum awal melalui WhatsApp', 'created_at' => '2026-07-14 00:15:00', 'updated_at' => '2026-07-14 00:15:00'],
            ['id' => 7,  'child_id' => 3, 'parent_id' => 3, 'date' => '2026-07-15', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:25:00', 'checkout_time' => null,       'drop_off_by' => 'Ayah (Azman)',   'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-14 23:25:00', 'updated_at' => '2026-07-14 23:25:00'],
            // Danish & Damia (children 4,5)
            ['id' => 8,  'child_id' => 4, 'parent_id' => 5, 'date' => '2026-07-16', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:05:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Siti Hajar)','pickup_by' => null,           'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-15 23:05:00', 'updated_at' => '2026-07-15 23:05:00'],
            ['id' => 9,  'child_id' => 5, 'parent_id' => 5, 'date' => '2026-07-16', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:05:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Siti Hajar)','pickup_by' => null,           'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-15 23:05:00', 'updated_at' => '2026-07-15 23:05:00'],
            ['id' => 10, 'child_id' => 4, 'parent_id' => 5, 'date' => '2026-07-17', 'status' => 'late_checkout','confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:10:00', 'checkout_time' => '17:45:00', 'drop_off_by' => 'Ayah (Farid)',   'pickup_by' => 'Atuk (Dollah)',   'is_verified' => true,  'notes' => 'Pickup lambat — hujan lebat', 'created_at' => '2026-07-16 23:10:00', 'updated_at' => '2026-07-17 09:45:00'],
            // Farah & Haris (children 6,7)
            ['id' => 11, 'child_id' => 6, 'parent_id' => 7, 'date' => '2026-07-17', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:20:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Noraini)', 'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-16 23:20:00', 'updated_at' => '2026-07-16 23:20:00'],
            ['id' => 12, 'child_id' => 7, 'parent_id' => 7, 'date' => '2026-07-17', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:20:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Noraini)', 'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-16 23:20:00', 'updated_at' => '2026-07-16 23:20:00'],
            // Irfan, Sarah, Iman (children 8,9,10) — Family Kamarul
            ['id' => 13, 'child_id' => 8, 'parent_id' => 9, 'date' => '2026-07-18', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:05:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Zuraidah)','pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-17 23:05:00', 'updated_at' => '2026-07-17 23:05:00'],
            ['id' => 14, 'child_id' => 9, 'parent_id' => 9, 'date' => '2026-07-18', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:05:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Zuraidah)','pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-17 23:05:00', 'updated_at' => '2026-07-17 23:05:00'],
            ['id' => 15, 'child_id' => 10,'parent_id' => 9, 'date' => '2026-07-18', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:05:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Zuraidah)','pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-17 23:05:00', 'updated_at' => '2026-07-17 23:05:00'],
            ['id' => 16, 'child_id' => 9, 'parent_id' => 9, 'date' => '2026-07-19', 'status' => 'late_checkout','confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:10:00', 'checkout_time' => '17:50:00', 'drop_off_by' => 'Ayah (Kamarul)', 'pickup_by' => 'Atuk (Senawi)',    'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-18 23:10:00', 'updated_at' => '2026-07-19 09:50:00'],
            ['id' => 17, 'child_id' => 10,'parent_id' => 9, 'date' => '2026-07-19', 'status' => 'late_checkout','confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:10:00', 'checkout_time' => '17:50:00', 'drop_off_by' => 'Ayah (Kamarul)', 'pickup_by' => 'Atuk (Senawi)',    'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-18 23:10:00', 'updated_at' => '2026-07-19 09:50:00'],
            // Recent attendance for this week
            ['id' => 18, 'child_id' => 1, 'parent_id' => 1, 'date' => '2026-07-19', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:18:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Norazila)', 'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-18 23:18:00', 'updated_at' => '2026-07-18 23:18:00'],
            ['id' => 19, 'child_id' => 2, 'parent_id' => 1, 'date' => '2026-07-19', 'status' => 'absent',       'confirmed' => false, 'confirmed_at' => null, 'late_reason' => 'Demam — dimaklumkan oleh ibu melalui WhatsApp', 'checkin_time' => null, 'checkout_time' => null, 'drop_off_by' => null, 'pickup_by' => null, 'is_verified' => false, 'notes' => 'MC akan dihantar', 'created_at' => '2026-07-18 23:00:00', 'updated_at' => '2026-07-18 23:00:00'],
            ['id' => 20, 'child_id' => 6, 'parent_id' => 7, 'date' => '2026-07-19', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:22:00', 'checkout_time' => null,       'drop_off_by' => 'Ayah (Rashid)',  'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-18 23:22:00', 'updated_at' => '2026-07-18 23:22:00'],
            ['id' => 21, 'child_id' => 7, 'parent_id' => 7, 'date' => '2026-07-19', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:22:00', 'checkout_time' => null,       'drop_off_by' => 'Ayah (Rashid)',  'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-18 23:22:00', 'updated_at' => '2026-07-18 23:22:00'],
            ['id' => 22, 'child_id' => 8, 'parent_id' => 9, 'date' => '2026-07-19', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:08:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Zuraidah)','pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-18 23:08:00', 'updated_at' => '2026-07-18 23:08:00'],
            // Today
            ['id' => 23, 'child_id' => 4, 'parent_id' => 5, 'date' => '2026-07-20', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:05:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Siti Hajar)','pickup_by' => null,           'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-19 23:05:00', 'updated_at' => '2026-07-19 23:05:00'],
            ['id' => 24, 'child_id' => 5, 'parent_id' => 5, 'date' => '2026-07-20', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:05:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Siti Hajar)','pickup_by' => null,           'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-19 23:05:00', 'updated_at' => '2026-07-19 23:05:00'],
            ['id' => 25, 'child_id' => 1, 'parent_id' => 1, 'date' => '2026-07-20', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:12:00', 'checkout_time' => null,       'drop_off_by' => 'Ayah (Hafiz)',   'pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-19 23:12:00', 'updated_at' => '2026-07-19 23:12:00'],
            ['id' => 26, 'child_id' => 2, 'parent_id' => 1, 'date' => '2026-07-20', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:12:00', 'checkout_time' => null,       'drop_off_by' => 'Ayah (Hafiz)',   'pickup_by' => null,            'is_verified' => true,  'notes' => 'Kembali selepas demam semalam', 'created_at' => '2026-07-19 23:12:00', 'updated_at' => '2026-07-19 23:12:00'],
            ['id' => 27, 'child_id' => 3, 'parent_id' => 3, 'date' => '2026-07-20', 'status' => 'late',         'confirmed' => false, 'confirmed_at' => null, 'late_reason' => 'Kesesakan lalu lintas', 'checkin_time' => '08:05:00', 'checkout_time' => null, 'drop_off_by' => 'Ibu (Rosnani)', 'pickup_by' => null,     'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-20 00:05:00', 'updated_at' => '2026-07-20 00:05:00'],
            ['id' => 28, 'child_id' => 9, 'parent_id' => 9, 'date' => '2026-07-20', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:08:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Zuraidah)','pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-19 23:08:00', 'updated_at' => '2026-07-19 23:08:00'],
            ['id' => 29, 'child_id' => 10,'parent_id' => 9, 'date' => '2026-07-20', 'status' => 'present',      'confirmed' => false, 'confirmed_at' => null, 'late_reason' => null, 'checkin_time' => '07:08:00', 'checkout_time' => null,       'drop_off_by' => 'Ibu (Zuraidah)','pickup_by' => null,            'is_verified' => true,  'notes' => null, 'created_at' => '2026-07-19 23:08:00', 'updated_at' => '2026-07-19 23:08:00'],
        ]);
        $this->command->info('  ✓ attendance: 29');
    }

    private function seedTimerSettings(): void
    {
        DB::table('timer_settings')->insert([
            ['id' => 1, 'day_name' => 'Isnin (Monday)',    'morning_start' => '07:00:00', 'morning_end' => '07:30:00', 'afternoon_start' => '12:00:00', 'afternoon_end' => '12:30:00', 'evening_start' => '17:00:00', 'evening_end' => '17:30:00', 'is_active' => true, 'created_at' => '2026-06-01 08:00:00', 'updated_at' => '2026-06-01 08:00:00'],
            ['id' => 2, 'day_name' => 'Selasa (Tuesday)',   'morning_start' => '07:00:00', 'morning_end' => '07:30:00', 'afternoon_start' => '12:00:00', 'afternoon_end' => '12:30:00', 'evening_start' => '17:00:00', 'evening_end' => '17:30:00', 'is_active' => true, 'created_at' => '2026-06-01 08:00:00', 'updated_at' => '2026-06-01 08:00:00'],
            ['id' => 3, 'day_name' => 'Rabu (Wednesday)',   'morning_start' => '07:00:00', 'morning_end' => '07:30:00', 'afternoon_start' => '12:00:00', 'afternoon_end' => '12:30:00', 'evening_start' => '17:00:00', 'evening_end' => '17:30:00', 'is_active' => true, 'created_at' => '2026-06-01 08:00:00', 'updated_at' => '2026-06-01 08:00:00'],
            ['id' => 4, 'day_name' => 'Khamis (Thursday)',  'morning_start' => '07:00:00', 'morning_end' => '07:30:00', 'afternoon_start' => '12:00:00', 'afternoon_end' => '12:30:00', 'evening_start' => '17:00:00', 'evening_end' => '17:30:00', 'is_active' => true, 'created_at' => '2026-06-01 08:00:00', 'updated_at' => '2026-06-01 08:00:00'],
            ['id' => 5, 'day_name' => 'Jumaat (Friday)',    'morning_start' => '07:00:00', 'morning_end' => '07:30:00', 'afternoon_start' => '12:00:00', 'afternoon_end' => '14:30:00', 'evening_start' => '17:00:00', 'evening_end' => '17:30:00', 'is_active' => true, 'created_at' => '2026-06-01 08:00:00', 'updated_at' => '2026-06-01 08:00:00'],
        ]);
        $this->command->info('  ✓ timer_settings: 5');
    }

    private function seedSimulationClock(): void
    {
        DB::table('simulation_clock')->truncate();
        DB::table('simulation_clock')->insert([
            ['id' => 1, 'simulation_time' => '2026-07-20 07:00:00', 'morning_start' => '07:00:00', 'morning_end' => '07:30:00', 'evening_start' => '17:00:00', 'evening_end' => '17:30:00', 'use_simulation' => false, 'created_at' => '2026-07-19 18:00:00', 'updated_at' => '2026-07-19 18:00:00'],
        ]);
        $this->command->info('  ✓ simulation_clock: 1');
    }
}
