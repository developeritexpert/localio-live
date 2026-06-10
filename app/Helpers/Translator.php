<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Translator
{
    public static function translate(string $text, string $targetLang, string $sourceLang = 'en'): string
    {
        $apiKey = env('GOOGLE_TRANSLATE_API_KEY');
        if (!$apiKey || trim($text) === '') {
            return $text;
        }

        $response = Http::post("https://translation.googleapis.com/language/translate/v2", [
            'q' => $text,
            'source' => $sourceLang,
            'target' => $targetLang,
            'format' => 'text',
            'key' => $apiKey,
        ]);

        return $response->json('data.translations.0.translatedText') ?? $text;
    }
}
