<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('id');
            $table->index('position'); // Add index for better performance
        });

        // Set initial positions for existing FAQs
        $faqs = DB::table('faqs')->orderBy('id')->get();
        foreach ($faqs as $index => $faq) {
            DB::table('faqs')
                ->where('id', $faq->id)
                ->update(['position' => $index + 1]);
        }
    }

    public function down()
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex(['position']);
            $table->dropColumn('position');
        });
    }
};
