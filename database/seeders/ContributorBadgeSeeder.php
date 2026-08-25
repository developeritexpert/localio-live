<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContributorBadge;

class ContributorBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            ['name' => 'New contributor', 'min_reviews' => 1, 'max_reviews' => 2, 'icon' => '🏅'],
            ['name' => 'Contributor', 'min_reviews' => 3, 'max_reviews' => 5, 'icon' => '🏅'],
            ['name' => 'Active contributor', 'min_reviews' => 6, 'max_reviews' => 10, 'icon' => '🏅'],
            ['name' => 'Experienced contributor', 'min_reviews' => 11, 'max_reviews' => 25, 'icon' => '🏅'],
            ['name' => 'Top contributor', 'min_reviews' => 26, 'max_reviews' => 50, 'icon' => '🏅'],
            ['name' => 'Community expert', 'min_reviews' => 51, 'max_reviews' => null, 'icon' => '🏅'],
        ];

        foreach ($badges as $badge) {
            ContributorBadge::updateOrCreate(['name' => $badge['name']], $badge);
        }
    }
}
