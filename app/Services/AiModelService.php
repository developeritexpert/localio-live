<?php 
namespace App\Services;

use App\Models\AiConfiguration;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Http;

class AiModelService
{
    protected $model;

    public function __construct(?AiConfiguration $model = null)
    {
        $this->model = $model ?? AiConfiguration::where('is_default', true)->first();
    }

    /**
     * Real-time generation (for individual admin use)
     */
    public function generateResponse(string $prompt, array $variables = []): ?string
    {
        if (!$this->model) {
            throw new \Exception("No AI model configured.");
        }
        switch ($this->model->model_name) {
            case 'gpt-4':
            case 'gpt-4o':
            case 'gpt-3.5-turbo':
                return $this->callOpenAi($prompt, $variables);
            case 'claude-3':
            case 'claude-opus-4-5':
            case 'claude-3-5-sonnet-20241022':
                return $this->callAnthropic($prompt);
            default:
                throw new \Exception("Unsupported model: {$this->model->model_name}");
        }
    }

    /**
     * Get OpenAI API key from WebSettings
     */
    public static function getOpenAIKey(): ?string
    {
        return optional(WebSetting::where('key', 'openai_api_key')->first())->value;
    }

    /**
     * Get Anthropic API key from WebSettings
     */
    public static function getAnthropicKey(): ?string
    {
        return optional(WebSetting::where('key', 'anthropic_api_key')->first())->value;
    }

    /**
     * Get OpenAI default model from WebSettings
     */
    public static function getOpenAIModel(): string
    {
        return optional(WebSetting::where('key', 'openai_default_model')->first())->value ?: 'gpt-4o';
    }

    /**
     * Get Anthropic default model from WebSettings
     */
    public static function getAnthropicModel(): string
    {
        return optional(WebSetting::where('key', 'anthropic_default_model')->first())->value ?: 'claude-opus-4-5';
    }

    protected function callOpenAi(string $prompt, array $variables = []): string
    {
        $apiKey = $this->model->api_key ?? self::getOpenAIKey();
        $model  = $this->model->model_name ?? self::getOpenAIModel();

        $response = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $this->replaceVariables($prompt, $variables)],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

        return $response['choices'][0]['message']['content'] ?? 'No response';
    }

    protected function callAnthropic(string $prompt): string
    {
        $apiKey = $this->model->api_key ?? self::getAnthropicKey();
        $model  = $this->model->model_name ?? self::getAnthropicModel();

        $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => 1000,
                'temperature' => 0.7,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        return $response['content'][0]['text'] ?? 'No response';
    }

    protected function replaceVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }
}
