<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiPrompt;
use App\Models\AiPromptAttach;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiPromptController extends Controller
{
    public function index(Request $request)
    {
        $query = AiPrompt::query();
    
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        $prompts = $query->orderBy('type')->paginate(10);
    
        return view('Admin.ai-prompts.index', compact('prompts'));
    }
    public function create()
    {
        return view('Admin.ai-prompts.create');
    }

    public function store(Request $request)
    {
    
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'original_prompt' => 'required|string',
            'description' => 'required|string',
        ]);
 
        $prompt = AiPrompt::create([
            'name' => $request->name,
            'type' => $request->type,
            'original_prompt' => $request->original_prompt,
            'updated_prompt' => $request->original_prompt,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('ai-prompts.index')
            ->with('success', 'AI Prompt created successfully');
    }

    public function edit(AiPrompt $aiPrompt)
    {
        return view('Admin.ai-prompts.edit', compact('aiPrompt'));
    }

    public function update(Request $request, AiPrompt $aiPrompt)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'original_prompt' => 'required|string',
            'description' => 'required|string',
        ]);
        // dd($request->all());
        $aiPrompt->update([
            'name' => $request->name,
            'type' => $request->type,
            'original_prompt' => $request->original_prompt,
            'updated_prompt' => $request->original_prompt,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);
    
        return redirect()->route('ai-prompts.index')
            ->with('success', 'AI Prompt updated successfully');
    }


    public function businessPrompt(){

        return view('Admin.ai-prompts.business-prompt');
    }

    

}
