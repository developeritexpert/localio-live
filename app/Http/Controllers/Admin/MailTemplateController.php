<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MailTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = MailTemplate::with('translations')->paginate(10);
        return view('Admin.mail-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new mail template
     */
    public function create()
    {
        $languages = Language::where('status', true)->get();
        return view('Admin.mail-templates.create', compact('languages'));
    }

    /**
     * Store a newly created mail template
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:mail_templates,key|regex:/^[a-z_]+$/',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
            'status' => 'required|in:active,inactive'
        ], [
            'key.regex' => 'Template key must contain only lowercase letters and underscores.',
            'variables.*.regex' => 'Variable names must be valid (letters, numbers, underscores only).'
        ]);

        // Clean and filter variables
        $variables = array_filter(
            array_map('trim', $request->variables ?? []),
            function($var) { return !empty($var); }
        );

        $template = MailTemplate::create([
            'key' => $request->key,
            'subject' => $request->subject,
            'body' => $this->processEditorContent($request->body),
            'variables' => array_values($variables),
            'status' => $request->status
        ]);

        return redirect()->route('mail-templates.index')
            ->with('success', 'Mail template created successfully.');
    }

    /**
     * Display the specified mail template
     */
    public function show(MailTemplate $mailTemplate)
    {
        $mailTemplate->load('translations');
        $languages = Language::where('status', true)->get();
        return view('Admin.mail-templates.show', compact('mailTemplate', 'languages'));
    }

    /**
     * Show the form for editing the specified mail template
     */
    public function edit($id,Request $request)
    {
        $mailTemplate = MailTemplate::findOrFail($id);
        $langId = request()->get('lang_id', getCurrentLanguageID() ?: 1);
        $languages = Language::where('status', true)->get();
        $translation = $mailTemplate->getTranslation($langId);

        return view('Admin.mail-templates.edit', compact('mailTemplate', 'languages', 'langId', 'translation'));
    }
    /**
     * Update the specified mail template
     */
    public function update(Request $request, $id)
    {
        $mailTemplate = MailTemplate::findOrFail($id);
        $langId = $request->get('lang_id', 1);

        $request->validate([
            'key' => ['required', 'string', 'regex:/^[a-z_]+$/', Rule::unique('mail_templates')->ignore($mailTemplate->id)],
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
            'lang_id' => 'required|integer'
        ], [
            'key.regex' => 'Template key must contain only lowercase letters and underscores.',
            'variables.*.regex' => 'Variable names must be valid (letters, numbers, underscores only).'
        ]);

        // Update key, variables, and status only for default language (lang_id = 1)
        if ($langId == 1) {
            // Clean and filter variables
            $variables = array_filter(
                array_map('trim', $request->variables ?? []),
                function($var) { return !empty($var); }
            );

            $mailTemplate->update([
                'key' => $request->key,
                'variables' => array_values($variables),
            ]);
        }

        // Update translation (including default language)
        $mailTemplate->updateTranslation(
            $langId,
            $request->subject,
            $this->processEditorContent($request->body)
        );

        return redirect()->back()
            ->with('success', 'Mail template updated successfully.');
    }

    /**
     * Remove the specified mail template
     */
    public function destroy($id)
    {
        $mailTemplate = MailTemplate::findOrFail($id);
        $mailTemplate->delete();

        return redirect()->route('mail-templates.index')
            ->with('success', 'Mail template deleted successfully.');
    }

    /**
     * Preview mail template
     */
    public function preview(Request $request, MailTemplate $mailTemplate)
{
    $langId = $request->get('lang_id', 1);
    $sampleData = $request->get('sample_data', []);

    // Parse sample data if it's JSON string
    if (is_string($sampleData)) {
        $sampleData = json_decode($sampleData, true) ?? [];
    }

    // If no sample data provided, create sample data from variables
    if (empty($sampleData) && $mailTemplate->variables) {
        foreach ($mailTemplate->variables as $variable) {
            $sampleData[$variable] = $this->generateSampleValue($variable);
        }
    }
    try {
        $rendered = $mailTemplate->renderTemplate($langId, $sampleData);

        return response()->json([
            'success' => true,
            'subject' => $rendered['subject'],
            'body' => $rendered['body'],
            'html_body' => $this->wrapInEmailTemplate($rendered['body'], $rendered['subject'])
        ]);
    } catch (\Exception $e) {
        \Log::error('Mail template preview error', [
            'template_id' => $mailTemplate->id,
            'lang_id' => $langId,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Error rendering template: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Get translation for specific language
     */
    public function getTranslation(Request $request, MailTemplate $mailTemplate)
    {
        $langId = $request->get('lang_id', 1);
        $translation = $mailTemplate->getTranslation($langId);

        return response()->json([
            'success' => true,
            'translation' => $translation
        ]);
    }

    /**
     * Validate template syntax
     */
    public function validateTemplate(Request $request)
    {
        $body = $request->get('body', '');
        $variables = $request->get('variables', []);

        $errors = [];
        $warnings = [];

        // Check for unclosed variables
        preg_match_all('/\{\{\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/', $body, $matches);
        $usedVariables = $matches[1];

        // Check for undefined variables
        foreach ($usedVariables as $variable) {
            if (!in_array($variable, $variables)) {
                $warnings[] = "Variable '$variable' is used but not defined in variables list.";
            }
        }

        // Check for unused variables
        foreach ($variables as $variable) {
            if (!in_array($variable, $usedVariables)) {
                $warnings[] = "Variable '$variable' is defined but not used in template.";
            }
        }

        // Check for malformed variables
        preg_match_all('/\{\{[^}]*\}\}/', $body, $allMatches);
        foreach ($allMatches[0] as $match) {
            if (!preg_match('/\{\{\s*\$[a-zA-Z_][a-zA-Z0-9_]*\s*\}\}/', $match)) {
                $errors[] = "Malformed variable syntax: $match";
            }
        }

        return response()->json([
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings
        ]);
    }

    /**
     * Process CKEditor content
     */
    private function processEditorContent($content)
    {
        // Remove any script tags for security
        $content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);

        // Clean up unnecessary whitespace but preserve formatting
        $content = trim($content);

        return $content;
    }

    /**
     * Generate sample value for variable
     */
    private function generateSampleValue($variable)
    {
        $sampleValues = [
            'name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'otp' => '123456',
            'password' => 'newpassword123',
            'token' => 'abc123xyz789',
            'username' => 'johndoe',
            'phone' => '+1234567890',
            'company' => 'Example Company',
            'url' => 'https://example.com',
            'link' => 'https://example.com/reset',
            'code' => 'VERIFY123',
            'amount' => '$100.00',
            'order_number' => 'ORD-12345',
            'order_date' => now()->format('Y-m-d'),
            'total_amount' => '$250.00',
            'customer_name' => 'Jane Smith',
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
            'verification_code' => '654321',
            'reset_link' => 'https://example.com/password/reset/token123'
        ];

        $lowerVariable = strtolower($variable);

        // Check for exact match first
        if (isset($sampleValues[$lowerVariable])) {
            return $sampleValues[$lowerVariable];
        }

        // Check for partial matches
        foreach ($sampleValues as $key => $value) {
            if (Str::contains($lowerVariable, $key) || Str::contains($key, $lowerVariable)) {
                return $value;
            }
        }

        // Default fallback
        return '[' . ucfirst(str_replace('_', ' ', $variable)) . ']';
    }

    /**
     * Wrap content in email template
     */
    private function wrapInEmailTemplate($body, $subject = '')
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .email-container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .email-header { border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 30px; }
        .email-content { margin-bottom: 30px; }
        .email-footer { border-top: 1px solid #f0f0f0; padding-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>' . htmlspecialchars($subject) . '</h2>
        </div>
        <div class="email-content">
            ' . $body . '
        </div>
        <div class="email-footer">
            <p>This is a preview of your email template.</p>
        </div>
    </div>
</body>
</html>';
    }
}
