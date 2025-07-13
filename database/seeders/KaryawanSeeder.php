<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $divisions = [
            'DIV001', // Mobile Apps
            'DIV002', // QA
            'DIV003', // Full Stack
            'DIV004', // Backend
            'DIV005', // Frontend
            'DIV006', // UI/UX Designer
        ];

        foreach (range(1, 20) as $i) {
            $divisionId = $faker->randomElement($divisions);
            $divisionSuffix = substr($divisionId, -3);
            $karyawanId = 'KRY' . $divisionSuffix . strtoupper(Str::random(4));

            Karyawan::create([
                'id' => $karyawanId,
                'image' => null,
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'division_id' => $divisionId,
                'position' => $faker->jobTitle,
            ]);
        }
    }
}
