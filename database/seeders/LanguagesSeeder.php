<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('languages')->truncate();

    $languages = [
        ['name' => 'Argentina - Español', 'lang_code' => 'es-ar'],
        ['name' => 'Australia - English', 'lang_code' => 'en-au'],
        ['name' => 'Brasil - Português', 'lang_code' => 'pt-br'],
        ['name' => 'Canada - English', 'lang_code' => 'en-ca'],
        ['name' => 'Canada - Français', 'lang_code' => 'fr-ca'],
        ['name' => 'Chile - Español', 'lang_code' => 'es-cl'],
        ['name' => 'Colombia - Español', 'lang_code' => 'es-co'],
        ['name' => 'Deutschland - Deutsch', 'lang_code' => 'de-de'],
        ['name' => 'España - Español', 'lang_code' => 'es-es'],
        ['name' => 'Estados Unidos - Español', 'lang_code' => 'es-us'],
        ['name' => 'France - Français', 'lang_code' => 'fr-fr'],
        ['name' => 'Hong Kong - English', 'lang_code' => 'en-hk'],
        ['name' => 'India - English', 'lang_code' => 'en-in'],
        ['name' => 'Ireland - English', 'lang_code' => 'en-ie'],
        ['name' => 'Israel - English', 'lang_code' => 'en-il'],
        ['name' => 'Svizzera - Italiano', 'lang_code' => 'it-ch'],
        ['name' => 'Malaysia - English', 'lang_code' => 'en-my'],
        ['name' => 'México - Español', 'lang_code' => 'es-mx'],
        ['name' => 'New Zealand - English', 'lang_code' => 'en-nz'],
        ['name' => 'Österreich - Deutsch', 'lang_code' => 'de-at'],
        ['name' => 'Pakistan - English', 'lang_code' => 'en-pk'],
        ['name' => 'Perú - Español', 'lang_code' => 'es-pe'],
        ['name' => 'Philippines - English', 'lang_code' => 'en-ph'],
        ['name' => 'Polska - Polski', 'lang_code' => 'pl-pl'],
        ['name' => 'Portuguese - Portugal', 'lang_code' => 'pt-pt'],
        ['name' => 'Schweiz - Deutsch', 'lang_code' => 'de-ch'],
        ['name' => 'Singapore - English', 'lang_code' => 'en-sg'],
        ['name' => 'South Africa - English', 'lang_code' => 'en-za'],
        ['name' => 'Suisse - Français', 'lang_code' => 'fr-ch'],
        ['name' => 'Türkiye - Türkçe', 'lang_code' => 'tr-tr'],
        ['name' => 'United Arab Emirates - English', 'lang_code' => 'en-ae'],
        ['name' => 'United Kingdom - English', 'lang_code' => 'en-uk'],
        ['name' => 'United States - English', 'lang_code' => 'en-us'],
        ['name' => 'Venezuela - Español', 'lang_code' => 'es-ve'],
    ];

    DB::table('languages')->insert($languages);
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
}

}
