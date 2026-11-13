<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\StaticContentKey;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $key = StaticContentKey::where('key', 'do_you_recommend_question')->first();
        if ($key) {
            if ($key->default_value === 'Do you recommend this business?') {
                $key->update(['default_value' => 'Do you recommend']);
            }
        } else {
            StaticContentKey::create([
                'key' => 'do_you_recommend_question',
                'name' => 'Do You Recommend Question',
                'default_value' => 'Do you recommend',
                'field_type' => 'text',
            ]);
        }

        Cache::forget('static_texts_all');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $key = StaticContentKey::where('key', 'do_you_recommend_question')->first();
        if ($key && $key->default_value === 'Do you recommend') {
            $key->update(['default_value' => 'Do you recommend this business?']);
        }

        Cache::forget('static_texts_all');
    }
};

