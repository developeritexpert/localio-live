<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConfiguration;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use App\Services\AiModelService;

class AiConfigurationController extends Controller
{
public function generate(Request $request)
{
    $request->validate([
        'prompt_template' => 'required|string',
        'variables' => 'nullable|array',
    ]);

    $service = new AiModelService();
    $response = $service->generateResponse($request->prompt_template, $request->variables ?? []);

    return response()->json([
        'response' => $response
    ]);
}

    public function index()
    {
        // $configs = AiConfiguration::all();
        // return view('Admin.ai-configuration.index', compact('configs'));
        $data = WebSetting::where('type', 'ai')->get();
        // dd($data);
        return view('Admin.ai-configuration.create', ['data' => $data]);
    }

    // public function create()
    // {
    //     $data = WebSetting::where('type', 'ai')->get();
    //     // dd($data);
    //     return view('Admin.ai-configuration.create', ['data' => $data]);

    // }

    public function store(Request $request)
    {
        $request->validate([
            'model_name' => 'required|string|max:255',
            'api_key' => 'required|string',
            'is_default' => 'required|boolean',
        ]);

        if ($request->is_default) {
            AiConfiguration::where('is_default', true)->update(['is_default' => false]);
        }

        AiConfiguration::create([
            'model_name' => $request->model_name,
            'api_key' => $request->api_key,
            'is_default' => $request->is_default,
        ]);

        return redirect()->route('ai-configurations.index')->with('success', 'AI Configuration added.');
    }

    public function update(Request $request, AiConfiguration $aiConfiguration)
    {
        $request->validate([
            'model_name' => 'required|string|max:255',
            'api_key' => 'required|string',
            'is_default' => 'required|boolean',
        ]);

        if ($request->is_default) {
            AiConfiguration::where('is_default', true)->where('id', '!=', $aiConfiguration->id)->update(['is_default' => false]);
        }

        $aiConfiguration->update([
            'model_name' => $request->model_name,
            'api_key' => $request->api_key,
            'is_default' => $request->is_default,
        ]);
        return redirect()->route('ai-configurations.index')->with('success', 'AI Configuration updated.');
    }

    public function destroy(AiConfiguration $aiConfiguration)
    {
        $aiConfiguration->delete();
        return back()->with('success', 'Configuration deleted.');
    }
}
