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
            'business_icon/1746103824_3H8UUe4f7h.png',
            'business_icon/1746103787_c7J7kSWbJN.png',
            'business_icon/1746103760_rpmXVNYcF2.png',
            'business_icon/1746103678_aBqoscEoLL.png',
            'business_icon/1748244192_NT3NodH2Zg.png',
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
