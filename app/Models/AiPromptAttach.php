<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPromptAttach extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'resource_id','frontend_img_path','backend_img_path','prompt_id','page_type'
    ];

    public function prompt(){
        return $this->belongsTo(AiPrompt::class ,'prompt_id');
    }
}
