<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('base_languages')) {
            Schema::create('base_languages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('language_tag');
                $table->boolean('is_master')->default(false);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

            // Seed standard initial base languages
            $now = now();
            $baseLanguages = [
                ['name' => 'English (US) — Master', 'code' => 'en-US', 'language_tag' => 'English', 'is_master' => true, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'English (UK)', 'code' => 'en-GB', 'language_tag' => 'English', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'English (Australia)', 'code' => 'en-AU', 'language_tag' => 'English', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'English (Canada)', 'code' => 'en-CA', 'language_tag' => 'English', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Spanish (Spain)', 'code' => 'es-ES', 'language_tag' => 'Spanish', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Spanish (Latin America)', 'code' => 'es-419', 'language_tag' => 'Spanish', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Portuguese (Brazil)', 'code' => 'pt-BR', 'language_tag' => 'Portuguese', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Portuguese (Portugal)', 'code' => 'pt-PT', 'language_tag' => 'Portuguese', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'French (France)', 'code' => 'fr-FR', 'language_tag' => 'French', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'German (Germany)', 'code' => 'de-DE', 'language_tag' => 'German', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Italian (Italy)', 'code' => 'it-IT', 'language_tag' => 'Italian', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Turkish (Turkey)', 'code' => 'tr-TR', 'language_tag' => 'Turkish', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Polish (Poland)', 'code' => 'pl-PL', 'language_tag' => 'Polish', 'is_master' => false, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ];
            DB::table('base_languages')->insert($baseLanguages);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('base_languages');
    }
};
