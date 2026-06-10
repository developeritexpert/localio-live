<?php

namespace Database\Seeders;

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
            // PolicysSeeder::class,
            CountrySeeder::class,
            LanguagesSeeder::class,
            UserSeeder::class,
            HomeContentSeeder::class,
            FooterSeeder::class,
            CategoryPageContent::class,
            EnglishSeeder::class,
            ExpertGuideSeeder::class,
            WhoWeAreSeeder::class,
            ContactContentSeeder::class,
            TopProductContentSeeder::class,
            CategorySeeder::class,
            HeaderContentSeeder::class,
            WebSettingsSeeder::class,
        ]);
    }
}