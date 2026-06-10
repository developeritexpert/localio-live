<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Helpers\Translator;
use App\Models\Language;

class LanguageContentService
{
    protected $defaultLangId = 1;

    public function getOrCreateTranslatedContent(string $table, int $langId)
    {
        $existing = DB::table($table)->where('lang_id', $langId)->pluck('meta_value', 'meta_key');
        if ($existing->isNotEmpty()) return $existing;

        // Get target language code
        $targetLangCode = Language::find($langId)?->lang_code ?? 'en';

        $defaultContent = DB::table($table)->where('lang_id', $this->defaultLangId)->get();

        foreach ($defaultContent as $item) {
            $data = (array) $item;
            unset($data['id']);
            $data['lang_id'] = $langId;

            // Auto translate text
            $data['meta_value'] = Translator::translate($data['meta_value'], $targetLangCode);

            DB::table($table)->insert($data);
        }

        return DB::table($table)->where('lang_id', $langId)->pluck('meta_value', 'meta_key');
    }
    public function getStaticImageContent(string $table, array $keys)
    {
        return DB::table($table)
            ->where('lang_id', $this->defaultLangId)
            ->whereIn('meta_key', $keys)
            ->get()
            ->keyBy('meta_key');
    }
}
