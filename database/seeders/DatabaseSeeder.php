<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategoryTableSeeder::class,
            AdvertorialSeeder::class,
            AlegeriSeeder::class,
            AntiFakeSeeder::class,
            CulturaSeeder::class,
            EconomicSeeder::class,
            EditorialSeeder::class,
            ExterneSeeder::class,
            InterviuSeeder::class,
            InvestigatiiSeeder::class,
            OpiniiSeeder::class,
            PoliticSeeder::class,
            SocialSeeder::class,
            SportSeeder::class,
            TransnistriaSeeder::class
        ]);
    }
}
