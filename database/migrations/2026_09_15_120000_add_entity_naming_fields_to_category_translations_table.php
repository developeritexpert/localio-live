<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("category_translations", function (Blueprint $table) {
            $table->string("category_cap")->nullable()->after("name");
            $table->string("category_tc")->nullable()->after("category_cap");
            $table->string("category_lc")->nullable()->after("category_tc");

            $table->string("entity1_cap")->nullable()->after("category_lc");
            $table->string("entity1_lc")->nullable()->after("entity1_cap");
            $table->string("entities1_cap")->nullable()->after("entity1_lc");
            $table->string("entities1_lc")->nullable()->after("entities1_cap");
            $table->string("entity1_short_cap")->nullable()->after("entities1_lc");
            $table->string("entity1_short_lc")->nullable()->after("entity1_short_cap");
            $table->string("entities1_short_cap")->nullable()->after("entity1_short_lc");
            $table->string("entities1_short_lc")->nullable()->after("entities1_short_cap");

            $table->string("entity2_cap")->nullable()->after("entities1_short_lc");
            $table->string("entity2_lc")->nullable()->after("entity2_cap");
            $table->string("entities2_cap")->nullable()->after("entity2_lc");
            $table->string("entities2_lc")->nullable()->after("entities2_cap");
            $table->string("entity2_short_cap")->nullable()->after("entities2_lc");
            $table->string("entity2_short_lc")->nullable()->after("entity2_short_cap");
            $table->string("entities2_short_cap")->nullable()->after("entity2_short_lc");
            $table->string("entities2_short_lc")->nullable()->after("entities2_short_cap");
        });
    }

    public function down(): void
    {
        Schema::table("category_translations", function (Blueprint $table) {
            $table->dropColumn([
                "category_cap",
                "category_tc",
                "category_lc",
                "entity1_cap",
                "entity1_lc",
                "entities1_cap",
                "entities1_lc",
                "entity1_short_cap",
                "entity1_short_lc",
                "entities1_short_cap",
                "entities1_short_lc",
                "entity2_cap",
                "entity2_lc",
                "entities2_cap",
                "entities2_lc",
                "entity2_short_cap",
                "entity2_short_lc",
                "entities2_short_cap",
                "entities2_short_lc"
            ]);
        });
    }
};
