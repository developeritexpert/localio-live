<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConfiguration;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use App\Services\AiModelService;

class AiConfigurationController extends Controller
{
    public function index()
    {
        $openai   = WebSetting::where('type', 'ai_openai')->get();
        $anthropic = WebSetting::where('type', 'ai_anthropic')->get();
        $legacy   = WebSetting::where('type', 'ai')->get();

        return view('Admin.ai-configuration.create', compact('openai', 'anthropic', 'legacy'));
    }

    public function create()
    {
        return $this->index();
    }

    public function saveOpenAI(Request $request)
    {
        $settings = WebSetting::where('type', 'ai_openai')->get();
        foreach ($settings as $setting) {
            if ($request->has($setting->key)) {
                $setting->value = $request->input($setting->key);
                $setting->save();
            }
        }
        return redirect()->route('ai-configurations.index')->with('success', 'OpenAI settings saved successfully.');
    }

    public function saveAnthropic(Request $request)
    {
        $settings = WebSetting::where('type', 'ai_anthropic')->get();
        foreach ($settings as $setting) {
            if ($request->has($setting->key)) {
                $setting->value = $request->input($setting->key);
                $setting->save();
            }
        }
        return redirect()->route('ai-configurations.index')->with('success', 'Anthropic settings saved successfully.');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt_template' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        $service = new AiModelService();
        $response = $service->generateResponse($request->prompt_template, $request->variables ?? []);

        return response()->json(['response' => $response]);
    }

    public function store(Request $request)
    {
        return redirect()->route('ai-configurations.index');
    }

    public function update(Request $request, AiConfiguration $aiConfiguration)
    {
        return redirect()->route('ai-configurations.index');
    }

    public function destroy(AiConfiguration $aiConfiguration)
    {
        $aiConfiguration->delete();
        return back()->with('success', 'Configuration deleted.');
    }
}
