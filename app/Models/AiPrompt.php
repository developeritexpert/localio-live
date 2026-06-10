<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPrompt extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'original_prompt',
        'type',
        'updated_prompt',
        'description',
        'is_active',

    ];

    protected $casts = [

        'is_active' => 'boolean',

    ];

    public function getProcessedPrompt($variables = [])
    {
        $prompt = $this->prompt_template;

        foreach ($variables as $key => $value) {
            $prompt = str_replace('{' . $key . '}', $value, $prompt);
        }

        return $prompt;
    }

    public function promptAttach(){
        return $this->hasMany(AiPromptAttach::class);
    }
}

