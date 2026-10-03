<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Delegates to MailTemplatesSeeder which contains all 15 mail templates.
     */
    public function run(): void
    {
        $this->call(MailTemplatesSeeder::class);
    }
}
