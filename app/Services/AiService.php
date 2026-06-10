<?php

namespace App\Services;

use App\Models\AiPrompt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\CredentialsLoader;


class AiService
{
    protected $apiEndpoint;
    protected $projectId;
    protected $locationId;
    protected $model;
    protected $modelRef;
    protected $settings;
    protected $apiKey;



    public function __construct()
    {
        $settings = web_setting(null, false, 'ai' );
        $this->apiEndpoint = $settings['api_endpoint'];
        $this->projectId = $settings['project_id'];
        $this->locationId = $settings['location_id'];
        $this->model = $settings['model_id'];

    }


    private function getAccessToken()
    {
        // dd('here');
        // putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/gcloud/service_account/credentails.json'));
        // env();


        $accessTokenFilePath = env('GOOGLE_APPLICATION_CREDENTIALS');
        // dd($accessTokenFilePath);


        // if ($accessToken) {
        //     return $accessToken;
        // }

        $credentials = CredentialsLoader::makeCredentials(
            ['https://www.googleapis.com/auth/cloud-platform'],
            json_decode(file_get_contents($accessTokenFilePath), true)
        );

        $token = $credentials->fetchAuthToken();
        return $token['access_token'] ?? null;
    }

    public function getGeminiAiResponse($prompt){

        $accessToken = $this->getAccessToken();

        // dd($accessToken);

        if (!$accessToken) {
            return "Error: Unable to retrieve access token.";
        }

        $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:streamGenerateContent";

        //  dd($url);

        $requestPayload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "responseModalities" => ["TEXT"],
                "temperature" => 1,
                "maxOutputTokens" => 8192,
                "topP" => 0.95
            ],
            "safetySettings" => []
        ];


        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json'
        ])->post($url, $requestPayload);

        // dd($response);

        if ( $response === false ) {
            return "Error: Unable to connect to AI service.";
            // dd('false');
        }

        $responseDecoded = json_decode( $response, true );
        $jsonParts = [];
        foreach ( $responseDecoded as $candidateGroup ) {
            foreach ( $candidateGroup[ 'candidates' ] as $candidate ) {
                foreach ( $candidate[ 'content' ][ 'parts' ] as $part ) {
                    $jsonParts[] = $part[ 'text' ];
                }
            }
        }
        $rawText = implode('', $jsonParts);

        $cleanText = preg_replace([
            '/\*\*(.*?)\*\*/s',
            '/\*(.*?)\*/s',
            '/^\s*[\*\-+]\s+/m',
            '/`{1,3}(.*?)`{1,3}/s'
        ], [
            '$1',
            '$1',
            '',
            '$1'
        ], $rawText);

        $cleanText = preg_replace("/[\r\n]{2,}/", "\n\n", trim($cleanText));
        // dd($cleanText);
        return $cleanText;
    }
}
