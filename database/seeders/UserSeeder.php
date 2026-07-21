<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    // Malaysian name components for generation
    private array $maleFirst   = ['Adam','Afiq','Aiman','Amir','Arif','Danial','Faris','Hadi','Hakim','Haziq',
                                   'Irfan','Ismail','Luqman','Mirza','Naufal','Rizqi','Syafiq','Zikri','Hariz','Imran'];
    private array $femaleFirst = ['Aina','Alia','Alya','Amira','Balqis','Damia','Dania','Farah','Hana','Intan',
                                   'Izzah','Jannah','Maisarah','Nadia','Qistina','Sofia','Yasmin','Zahra','Zara','Amani'];
    private array $maleLast    = ['bin Abdullah','bin Ahmad','bin Azman','bin Hassan','bin Ismail','bin Kamal',
                                   'bin Razak','bin Salleh','bin Wahid','bin Zainal'];
    private array $femaleLast  = ['binti Abdullah','binti Ahmad','binti Azman','binti Hassan','binti Ismail',
                                   'binti Kamal','binti Razak','binti Salleh','binti Wahid','binti Zainal'];
    private array $domains     = ['gmail.com','yahoo.com','outlook.com','hotmail.com'];
    private array $usedEmails  = [];
    private int   $emailCounter = 0;

    public function run(): void
    {
        DB::table('users')->truncate();

        // ── Admins ──────────────────────────────────
        $admins = [
            ['Diana binti Azman',           '30', 'diana@taskakids.com',    '0133443554'],
            ['Nurul Syuhada binti Rahim',    '28', 'syuhada@taskakids.com', '0198876543'],
            ['Ahmad Fikri bin Hassan',       '35', 'fikri@taskakids.com',   '0125567890'],
        ];
        $this->insertUsers($admins, 'admin');

        // ── Generate 35+35+35 = 105 parents ──────────
        $mainParents   = $this->generateParents(35, 'parent1');
        $secondParents = $this->generateParents(35, 'parent2');
        $guardians     = $this->generateParents(35, 'guardian', 50, 70); // older

        $this->insertUsers($mainParents, 'parent1');
        $this->insertUsers($secondParents, 'parent2');
        $this->insertUsers($guardians, 'guardian');

        $total = count($admins) + count($mainParents) + count($secondParents) + count($guardians);
        $this->command->info('  ✓ users: ' . $total);
    }

    private function generateParents(int $count, string $role, int $minAge = 28, int $maxAge = 45): array
    {
        $users = [];

        for ($i = 0; $i < $count; $i++) {
            $isFemale = mt_rand(0, 1);
            $first    = $isFemale
                ? $this->femaleFirst[array_rand($this->femaleFirst)]
                : $this->maleFirst[array_rand($this->maleFirst)];
            $last     = $isFemale
                ? $this->femaleLast[array_rand($this->femaleLast)]
                : $this->maleLast[array_rand($this->maleLast)];
            $name     = "$first $last";
            $age      = (string) mt_rand($minAge, $maxAge);

            // Globally unique email
            $slug  = strtolower(str_replace([' ', 'binti ', 'bin '], ['', '', ''], $name));
            $email = $slug . (++$this->emailCounter) . '@' . $this->domains[array_rand($this->domains)];
            while (in_array($email, $this->usedEmails)) {
                $email = $slug . (++$this->emailCounter) . '@' . $this->domains[array_rand($this->domains)];
            }
            $this->usedEmails[] = $email;

            // Malaysian-style phone (01xxxxxxxx)
            $prefix = ['011','012','013','014','016','017','018','019'][array_rand(range(0,7))];
            $phone  = $prefix . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

            $users[] = [$name, (string)$age, $email, $phone];
        }

        return $users;
    }

    private function insertUsers(array $users, string $role): void
    {
        foreach ($users as $u) {
            DB::table('users')->insert([
                'name'         => $u[0],
                'age'          => $u[1],
                'email'        => $u[2],
                'password'     => Hash::make('password'),
                'phone_number' => $u[3],
                'role'         => $role,
                'verified'     => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}