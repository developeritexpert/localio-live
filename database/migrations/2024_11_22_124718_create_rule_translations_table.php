<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rule_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id'); // Foreign key column without constraint
            $table->unsignedBigInteger('language_id'); // Foreign key column without constraint
            $table->string('title'); // Title of the rule in the selected language
            $table->string('slug')->nullable();
            $table->longText('description'); // Description of the rule in the selected language
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status field to indicate whether the translation is active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_translations');
    }
};
