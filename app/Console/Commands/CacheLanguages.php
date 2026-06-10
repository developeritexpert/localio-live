<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\Language; // Update if your model path is different

class CacheLanguages extends Command
{
    protected $signature = 'cache:languages';
    protected $description = 'Store languages table data in Redis';

    public function handle()
    {
        // Fetch data from the database
        $languages = Language::all();

        // Store in Redis as JSON
        Redis::set('laravel_database_languages', $languages->toJson());

        $this->info('Languages table data cached successfully in Redis.');
    }
}
