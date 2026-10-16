<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add pricing_updated_at to businesses if not present
        if (!Schema::hasColumn("businesses", "pricing_updated_at")) {
            Schema::table("businesses", function (Blueprint $table) {
                $table->timestamp("pricing_updated_at")->nullable()->after("updated_at");
            });
        }

        // 2. Business Offer Products
        if (!Schema::hasTable("business_offer_products")) {
            Schema::create("business_offer_products", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("business_id")->index();
                $table->unsignedBigInteger("product_id")->nullable()->index(); // for JSON import reference (e.g. 10852)
                $table->string("name");
                $table->text("description")->nullable();
                $table->unsignedBigInteger("primary_category_id")->nullable();
                $table->unsignedBigInteger("primary_subcategory_id")->nullable();
                $table->json("category_ids")->nullable();
                $table->json("subcategory_ids")->nullable();
                $table->json("names_by_locale")->nullable();
                $table->boolean("has_plans")->default(true);
                $table->string("status", 50)->default("public");
                $table->integer("sort_order")->default(0);
                $table->timestamps();

                $table->foreign("business_id")->references("id")->on("businesses")->onDelete("cascade");
            });
        }

        // 3. Business Offer Plans
        if (!Schema::hasTable("business_offer_plans")) {
            Schema::create("business_offer_plans", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("business_product_id")->index();
                $table->unsignedBigInteger("plan_id")->nullable()->index(); // for JSON import reference (e.g. 20401)
                $table->string("name");
                $table->string("short_description", 500)->nullable();
                $table->json("names_by_locale")->nullable();
                $table->boolean("is_featured")->default(false);
                $table->integer("sort_order")->default(0);
                $table->string("status", 50)->default("public");
                $table->timestamps();

                $table->foreign("business_product_id")->references("id")->on("business_offer_products")->onDelete("cascade");
            });
        }

        // 4. Business Offer Prices
        if (!Schema::hasTable("business_offer_prices")) {
            Schema::create("business_offer_prices", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("business_plan_id")->nullable()->index();
                $table->unsignedBigInteger("business_product_id")->nullable()->index();
                $table->string("country_region", 50)->default("all");
                $table->string("currency", 10)->default("USD");
                $table->string("billing_period", 50)->default("1m"); // 1m, 3m, 6m, 12m, 24m, 36m, once, none, custom
                $table->string("commitment_period", 50)->nullable();
                $table->integer("custom_months")->nullable();
                $table->string("unit", 50)->nullable();
                $table->string("custom_unit", 100)->nullable();
                $table->decimal("regular_price", 10, 2)->default(0.00);
                $table->decimal("renewal_price", 10, 2)->nullable();
                $table->decimal("intro_price", 10, 2)->nullable();
                $table->string("intro_duration", 100)->nullable();
                $table->string("price_model", 50)->default("fixed"); // fixed, free, quote
                $table->boolean("is_active")->default(true);
                $table->timestamps();

                $table->foreign("business_plan_id")->references("id")->on("business_offer_plans")->onDelete("cascade");
                $table->foreign("business_product_id")->references("id")->on("business_offer_products")->onDelete("cascade");
            });
        }

        // 5. Business Offer Plan Features (mapped to features table)
        if (!Schema::hasTable("business_offer_plan_features")) {
            Schema::create("business_offer_plan_features", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("business_plan_id")->nullable()->index();
                $table->unsignedBigInteger("business_product_id")->nullable()->index();
                $table->unsignedBigInteger("feature_id")->index();
                $table->boolean("is_featured_highlight")->default(false); // 4-6 key features
                $table->integer("sort_order")->default(0);
                $table->timestamps();

                $table->foreign("business_plan_id")->references("id")->on("business_offer_plans")->onDelete("cascade");
                $table->foreign("business_product_id")->references("id")->on("business_offer_products")->onDelete("cascade");
            });
        }

        // 6. Business Offer Specifications
        if (!Schema::hasTable("business_offer_specifications")) {
            Schema::create("business_offer_specifications", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger("business_plan_id")->index();
                $table->string("code", 100);
                $table->string("value", 255);
                $table->string("unit", 50)->nullable();
                $table->timestamps();

                $table->foreign("business_plan_id")->references("id")->on("business_offer_plans")->onDelete("cascade");
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists("business_offer_specifications");
        Schema::dropIfExists("business_offer_plan_features");
        Schema::dropIfExists("business_offer_prices");
        Schema::dropIfExists("business_offer_plans");
        Schema::dropIfExists("business_offer_products");

        if (Schema::hasColumn("businesses", "pricing_updated_at")) {
            Schema::table("businesses", function (Blueprint $table) {
                $table->dropColumn("pricing_updated_at");
            });
        }
    }
};

