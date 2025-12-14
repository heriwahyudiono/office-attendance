<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Office Location (Monas, Jakarta)
        \App\Models\OfficeLocation::create([
            'name' => 'Head Office (Jakarta)',
            'latitude' => -6.319499233171469,
            'longitude' => 106.76188768530464,
            'radius_meter' => 150,
        ]);
    }
}
