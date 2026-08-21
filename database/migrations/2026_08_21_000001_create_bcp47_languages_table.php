<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bcp47_languages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        $now = now();
        DB::table('bcp47_languages')->insert([
            ['code' => 'en-US',  'name' => 'English (United States)',       'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en-GB',  'name' => 'English (United Kingdom)',      'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en-CA',  'name' => 'English (Canada)',              'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en-AU',  'name' => 'English (Australia)',           'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en-IN',  'name' => 'English (India)',               'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'es-ES',  'name' => 'Spanish (Spain)',               'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'es-419', 'name' => 'Spanish (Latin America)',       'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'pt-BR',  'name' => 'Portuguese (Brazil)',           'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'pt-PT',  'name' => 'Portuguese (Portugal)',         'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'fr-FR',  'name' => 'French (France)',               'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'de-DE',  'name' => 'German (Germany)',              'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'it-IT',  'name' => 'Italian (Italy)',               'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'nl-NL',  'name' => 'Dutch (Netherlands)',           'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'pl-PL',  'name' => 'Polish (Poland)',               'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'tr-TR',  'name' => 'Turkish (Turkey)',              'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'sv-SE',  'name' => 'Swedish (Sweden)',              'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'da-DK',  'name' => 'Danish (Denmark)',              'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'nb-NO',  'name' => 'Norwegian (Norway)',            'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'fi-FI',  'name' => 'Finnish (Finland)',             'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ja-JP',  'name' => 'Japanese (Japan)',              'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'zh-CN',  'name' => 'Chinese Simplified (China)',    'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'zh-TW',  'name' => 'Chinese Traditional (Taiwan)',  'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ko-KR',  'name' => 'Korean (Korea)',                'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ar-SA',  'name' => 'Arabic (Saudi Arabia)',         'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bcp47_languages');
    }
};
