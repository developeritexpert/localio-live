<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class AiServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('web_settings')->insert([
            [
                'name' => 'Service Account File Path ',
                'key'=>'server_account_file_path',
                'value' => '/goolge/credentials.json',
                'field_type' => 'text',
                'type' => 'ai',
            ],[
                'name' => 'Project ID ',
                'key'=>'project_id',
                'value' => 'legalio-435913',
                'field_type' => 'text',
                'type' => 'ai',
            ],[
                'name' => 'API Endpoint ',
                'key'=>'api_endpoint',
                'value' => 'us-central1-aiplatform.googleapis.com',
                'field_type' => 'text',
                'type' => 'ai',
            ],[
                'name' => 'Model ID',
                'key'=>'model_id',
                'value' => 'gemini-2.0-flash-001',
                'field_type' => 'text',
                'type' => 'ai',
            ],[
                'name' => 'Generate Content API ',
                'key'=>'generate_content_api',
                'value' => 'GENERATE_CONTENT_API',
                'field_type' => 'text',
                'type' => 'ai',
            ],[
                'name' => 'Location ID ',
                'key'=>'location_id',
                'value' => 'us-central1',
                'field_type' => 'text',
                'type' => 'ai',
            ],
        ]);

    
    }
}
