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
        $dummyIcons = [
            'front/img/default_business_logo.svg',
            'front/img/logo.svg',
            'front/img/default.png',
            'images/default.png',
            'front/img/slider1_img.svg',
            'front/img/slider2_img.svg',
            'front/img/slider3_img.svg',
        ];

        DB::table('businesses')
            ->whereIn('icon_id', $dummyIcons)
            ->update(['icon_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore dummy placeholder icons
    }
};
