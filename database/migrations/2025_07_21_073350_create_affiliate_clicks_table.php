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
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('click_id')->unique();
            $table->string('gclid')->nullable();
            $table->string('msclkid')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->decimal('revenue', 8, 2)->default(0);
            $table->boolean('converted')->default(false);
            $table->timestamp('clicked_at');
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};
