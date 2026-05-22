<?php

namespace Database\Seeders;

use App\Models\Roli;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'admin', 'description' => 'Administratori i sistemit'],
            ['role_name' => 'perfaqesues_kompanie', 'description' => 'Perfaqesuesi i startup-it ose kompanise'],
            ['role_name' => 'investitor_individual', 'description' => 'Investitori individual'],
            ['role_name' => 'perfaqesues_fondi', 'description' => 'Perfaqesuesi i fondit te investimit'],
            ['role_name' => 'perdorues_fundor', 'description' => 'Perdoruesi fundor'],
            ['role_name' => 'verifikues', 'description' => 'Verifikuesi institucional'],
        ];

        foreach ($roles as $role) {
            Roli::firstOrCreate(
                ['role_name' => $role['role_name']],
                ['description' => $role['description']]
            );
        }
    }
}
