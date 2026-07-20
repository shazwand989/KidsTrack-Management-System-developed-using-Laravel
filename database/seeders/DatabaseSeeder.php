<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding normalized KidsTrack...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('guardianships')->truncate();
        DB::table('attendance')->truncate();
        DB::table('children')->truncate();
        DB::table('users')->truncate();
        DB::table('classrooms')->truncate();
        DB::table('teachers')->truncate();
        DB::table('timer_settings')->truncate();

        $this->seedUsers();
        $this->seedClassrooms();
        $this->seedTeachers();
        $this->seedChildren();
        $this->seedGuardianships();
        $this->seedAttendance();
        $this->seedTimerSettings();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('Done! Database normalized.');
    }

    private function seedUsers(): void
    {
        $users = [
            [1,'Diana binti Azman','30','diana@taskakids.com','013-344 3554','admin'],
            [2,'Nurul Syuhada binti Rahim','28','syuhada@taskakids.com','019-887 6543','admin'],
            [3,'Ahmad Fikri bin Hassan','35','fikri@taskakids.com','012-556 7890','admin'],
            [10,'Norazila binti Mahmud','32','norazila@gmail.com','012-242 4534','parent1'],
            [11,'Mohd Hafiz bin Ismail','36','hafiz.ismail@gmail.com','019-876 5432','parent2'],
            [13,'Zulkifli bin Omar (Atuk)','62','zulkifli.omar@gmail.com','011-9945 8141','guardian'],
            [16,'Rosnani binti Shuib','34','rosnani.shuib@gmail.com','018-775 4432','parent1'],
            [17,'Azman bin Hashim','38','azman.hashim@gmail.com','011-5678 9800','parent2'],
            [18,'Mak Cik Kamsiah','58','kamsiah@gmail.com','018-775 3232','guardian'],
            [19,'Siti Hajar binti Rahman','31','hajar.rahman@gmail.com','016-554 3232','parent1'],
            [20,'Farid bin Zainal','35','farid.zainal@gmail.com','017-889 9090','parent2'],
            [21,'Pak Cik Dollah','65','dollah@gmail.com','018-997 8777','guardian'],
            [22,'Noraini binti Samad','33','noraini.samad@gmail.com','013-889 7654','parent1'],
            [23,'Rashid bin Ghazali','37','rashid.ghazali@gmail.com','019-334 5678','parent2'],
            [24,'Mak Cik Timah','60','timah@gmail.com','017-773 9561','guardian'],
            [25,'Kamarul bin Ariffin','40','kamarul.ariffin@gmail.com','017-773 9574','parent1'],
            [26,'Zuraidah binti Mustafa','38','zuraidah@gmail.com','011-3345 7654','parent2'],
            [27,'Pak Ngah Senawi','68','senawi@gmail.com','011-0045 8141','guardian'],
            [34,'SITI ARBI','29','sitiarbi@gmail.com','012-987 6543','parent1'],
        ];

        foreach ($users as $u) {
            DB::table('users')->insert([
                'id' => $u[0], 'name' => $u[1], 'age' => $u[2], 'email' => $u[3],
                'password' => Hash::make('password'), 'phone_number' => $u[4],
                'role' => $u[5], 'verified' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->command->info('  ✓ users: '.count($users));
    }

    private function seedClassrooms(): void
    {
        DB::table('classrooms')->insert([
            ['id'=>1,'name'=>'Ceria 1 (2 Tahun)','code'=>'C1','age_group'=>'2 Tahun','min_age'=>1,'max_age'=>2,'capacity'=>15,'color'=>'#45B7D1','status'=>'active','created_at'=>now(),'updated_at'=>now()],
            ['id'=>2,'name'=>'Ceria 2 (3 Tahun)','code'=>'C2','age_group'=>'3 Tahun','min_age'=>2,'max_age'=>3,'capacity'=>15,'color'=>'#FF6B6B','status'=>'active','created_at'=>now(),'updated_at'=>now()],
            ['id'=>3,'name'=>'Bestari (4 Tahun)','code'=>'B1','age_group'=>'4 Tahun','min_age'=>3,'max_age'=>4,'capacity'=>15,'color'=>'#4ECDC4','status'=>'active','created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    private function seedTeachers(): void
    {
        DB::table('teachers')->insert([
            ['id'=>1,'name'=>'Hasanatun Nasuha binti Md Azlee','position'=>'Guru Besar','age'=>35,'phone'=>'011-3345 8141','email'=>'hasanatun@taskakids.com','classroom_id'=>1,'status'=>'active','join_date'=>'2025-01-02','created_at'=>now(),'updated_at'=>now()],
            ['id'=>2,'name'=>'Nur Fatimah binti Azmi','position'=>'Guru Kanan','age'=>29,'phone'=>'013-778 9901','email'=>'fatimah@taskakids.com','classroom_id'=>2,'status'=>'active','join_date'=>'2025-03-15','created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    private function seedChildren(): void
    {
        $children = [
            [1,'Adam bin Mohd Hafiz',2,'200704-01-0123','2024-07-04','Kampung Batu 11, Jementah',1,'Alahan makanan laut'],
            [2,'Aisyah binti Mohd Hafiz',1,'200805-01-0456','2025-08-05','Kampung Batu 11, Jementah',1,null],
            [3,'Aqilah binti Azman',2,'200706-01-0789','2024-06-06','Taman Gemilang, 85000 Segamat',1,'Asma ringan'],
            [4,'Danish bin Farid',2,'200708-01-0321','2024-08-08','Taman Mewah, 85000 Segamat',1,null],
            [5,'Damia binti Farid',1,'200910-01-0654','2025-10-09','Taman Mewah, 85000 Segamat',1,null],
            [6,'Farah binti Rashid',2,'200709-01-0987','2024-09-09','Kampung Tengah, 85200 Segamat',1,'Lactose intolerant'],
            [7,'Haris bin Rashid',1,'201001-01-0432','2025-10-10','Kampung Tengah, 85200 Segamat',1,'Vegetarian'],
            [8,'Irfan bin Kamarul',2,'200710-01-0789','2024-10-10','Kampung Paya Lebar, 85200 Segamat',1,null],
            [9,'Sarah binti Kamarul',3,'200605-01-0345','2023-05-06','Kampung Paya Lebar, 85200 Segamat',2,null],
            [10,'Iman binti Kamarul',3,'200608-01-0678','2023-08-06','Kampung Paya Lebar, 85200 Segamat',2,null],
            [13,'Afiqah',3,'200703-14-0001','2023-03-14','No. 55, Jalan Delima, Taman Delima',2,null],
            [14,'Afiq',3,'200704-14-0002','2023-04-14','No. 55, Jalan Delima, Taman Delima',2,null],
        ];

        foreach ($children as $c) {
            DB::table('children')->insert([
                'id' => $c[0], 'name' => $c[1], 'age' => $c[2], 'ic_number' => $c[3],
                'dob' => $c[4], 'address' => $c[5], 'classroom_id' => $c[6],
                'medical_notes' => $c[7], 'dietary' => $c[7] === 'Vegetarian' ? 'Vegetarian' : ($c[7] === 'Lactose intolerant' ? 'Lactose intolerant' : ($c[7] ?? null)),
                'is_active' => true, 'enrollment_date' => now(),
                'qr_code' => 'KID-'.str_pad($c[0],4,'0',STR_PAD_LEFT),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->command->info('  ✓ children: '.count($children));

        // Correct dietary for specific children
        DB::table('children')->where('id',1)->update(['dietary'=>'Alahan makanan laut']);
        DB::table('children')->where('id',6)->update(['dietary'=>'Lactose intolerant']);
        DB::table('children')->where('id',7)->update(['dietary'=>'Vegetarian']);
        DB::table('children')->where('id',3)->update(['medical_notes'=>'Asma ringan']);
    }

    private function seedGuardianships(): void
    {
        // [child_id, user_id, relationship, is_emergency]
        $links = [
            [1,10,'main_parent',1],[1,11,'second_parent',0],[1,13,'guardian',1],
            [2,10,'main_parent',1],[2,11,'second_parent',0],[2,13,'guardian',1],
            [3,16,'main_parent',1],[3,17,'second_parent',0],[3,18,'guardian',1],
            [4,19,'main_parent',1],[4,20,'second_parent',0],[4,21,'guardian',1],
            [5,19,'main_parent',1],[5,20,'second_parent',0],[5,21,'guardian',1],
            [6,22,'main_parent',1],[6,23,'second_parent',0],[6,24,'guardian',1],
            [7,22,'main_parent',1],[7,23,'second_parent',0],[7,24,'guardian',1],
            [8,25,'main_parent',1],[8,26,'second_parent',0],[8,27,'guardian',1],
            [9,25,'main_parent',1],[9,26,'second_parent',0],[9,27,'guardian',1],
            [10,25,'main_parent',1],[10,26,'second_parent',0],[10,27,'guardian',1],
            [13,34,'main_parent',1],
            [14,34,'main_parent',1],
        ];

        foreach ($links as $l) {
            DB::table('guardianships')->insert([
                'child_id' => $l[0], 'user_id' => $l[1],
                'relationship' => $l[2], 'is_emergency_contact' => $l[3],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->command->info('  ✓ guardianships: '.count($links));
    }

    private function seedAttendance(): void
    {
        $records = [
            ['child_id'=>1,'user_id'=>10,'date'=>'2026-07-21','status'=>'present','checkin_time'=>'07:15:00','drop_off_by'=>'Norazila binti Mahmud','is_verified'=>1],
            ['child_id'=>2,'user_id'=>10,'date'=>'2026-07-21','status'=>'present','checkin_time'=>'07:15:00','drop_off_by'=>'Norazila binti Mahmud','is_verified'=>1],
            ['child_id'=>3,'user_id'=>16,'date'=>'2026-07-21','status'=>'late','checkin_time'=>'08:05:00','drop_off_by'=>'Rosnani binti Shuib','is_verified'=>1,'late_reason'=>'Kesesakan lalu lintas'],
        ];

        foreach ($records as $rec) {
            DB::table('attendance')->insert(array_merge($rec, ['created_at' => now(), 'updated_at' => now()]));
        }
        $this->command->info('  ✓ attendance: '.count($records));
    }

    private function seedTimerSettings(): void
    {
        $days = ['Isnin (Monday)','Selasa (Tuesday)','Rabu (Wednesday)','Khamis (Thursday)','Jumaat (Friday)'];
        foreach ($days as $day) {
            DB::table('timer_settings')->insert([
                'day_name' => $day,
                'morning_start'=>'07:00:00','morning_end'=>'07:30:00',
                'afternoon_start'=>'12:00:00','afternoon_end'=>'12:30:00',
                'evening_start'=>'17:00:00','evening_end'=>'17:30:00',
                'is_active'=>1,'created_at'=>now(),'updated_at'=>now(),
            ]);
        }
    }
}
