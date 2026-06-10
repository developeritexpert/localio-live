<?php

namespace App\Services;

use Google\Cloud\Translate\V2\TranslateClient;

class TranslationService
{
    protected $translate;

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . env('GOOGLE_APPLICATION_CREDENTIALS'));

        $this->translate = new TranslateClient([
            'keyFilePath' => env('GOOGLE_APPLICATION_CREDENTIALS'),
            'suppressKeyFileNotice' => true,
        ]);
    }

    public function translate($text, $targetLanguage)
    {

        try{
            $result = $this->translate->translate($text, [
                'target' => $targetLanguage,
            ]);

            return $result['text'];

        }catch(\Exception $e ){
            saveLog('translateService', 'error in translation', $targetLanguage, $e->getMessage());
            return false ;
        }

    }
}
