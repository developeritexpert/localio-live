<?php 
namespace App\Services;

use App\Models\AiConfiguration;
use Illuminate\Support\Facades\Http;

class AiModelService
{
    protected $model;

    public function __construct(?AiConfiguration $model = null)
    {
        $this->model = $model ?? AiConfiguration::where('is_default', true)->first();
    }

    public function generateResponse(string $prompt, array $variables = []): ?string
    {
        if (!$this->model) {
            throw new \Exception("No AI model configured.");
        }
        switch ($this->model->model_name) {
            case 'gpt-4':
            case 'gpt-3.5-turbo':
                return $this->callOpenAi($prompt, $variables);
            case 'claude-3':
                return $this->callAnthropic($prompt);
            // Add more models as needed
            default:
                throw new \Exception("Unsupported model: {$this->model->model_name}");
        }
    }
    protected function callOpenAi(string $prompt, array $variables = []): string
    {
        $response = Http::withToken($this->model->api_key)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model->model_name,
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
        $response = Http::withHeaders([
                'x-api-key' => $this->model->api_key,
                'anthropic-version' => '2023-06-01',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model->model_name,
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