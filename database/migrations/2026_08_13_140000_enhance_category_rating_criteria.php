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
        Schema::table('category_rating_criteria', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->boolean('is_default')->default(false)->after('description');
            $table->string('default_key')->nullable()->after('is_default');
        });

        Schema::create('default_rating_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('default_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed initial default criteria
        DB::table('default_rating_criteria')->insert([
            [
                'key' => 'features',
                'name' => 'Features',
                'default_description' => null,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ease_of_use',
                'name' => 'Ease of use',
                'default_description' => null,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'value_for_money',
                'name' => 'Value for money',
                'default_description' => null,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_rating_criteria');

        Schema::table('category_rating_criteria', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_default', 'default_key']);
        });
    }
};
