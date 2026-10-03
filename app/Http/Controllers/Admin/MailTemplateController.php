<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailTemplate;
use App\Models\Language;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of mail templates
     */
    public function index()
    {
        $templates = MailTemplate::with('translations')->paginate(15);

        return view('Admin.mail-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new mail template
     */
    public function create()
    {
        $languages = Language::all();

        return view('Admin.mail-templates.create', compact('languages'));
    }

    /**
     * Store a newly created mail template
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:mail_templates,key|regex:/^[a-z0-9_]+$/',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
            'status' => 'required|in:active,inactive'
        ], [
            'key.regex' => 'Template key must contain only lowercase letters, numbers, and underscores.',
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
            'is_active' => $request->status === 'active',
            'status' => $request->status,
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
        $languages = Language::all();

        return view('Admin.mail-templates.show', compact('mailTemplate', 'languages'));
    }

    /**
     * Show the form for editing the specified mail template
     */
    public function edit($id)
    {
        $mailTemplate = MailTemplate::findOrFail($id);
        $languages = Language::all();

        return view('Admin.mail-templates.edit', compact('mailTemplate', 'languages'));
    }

    /**
     * Update the specified mail template
     */
    public function update(Request $request, $id)
    {
        $mailTemplate = MailTemplate::findOrFail($id);
        $langId = $request->get('lang_id', 1);

        $request->validate([
            'key' => $langId == 1 ? 'required|string|regex:/^[a-z0-9_]+$/|unique:mail_templates,key,' . $mailTemplate->id : 'nullable',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'variables.*' => 'string|regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
            'lang_id' => 'required|integer',
            'status' => 'nullable|in:active,inactive'
        ], [
            'key.regex' => 'Template key must contain only lowercase letters, numbers, and underscores.',
            'variables.*.regex' => 'Variable names must be valid (letters, numbers, underscores only).'
        ]);

        // Update key, variables, and status only for default language (lang_id = 1)
        if ($langId == 1) {
            // Clean and filter variables
            $variables = array_filter(
                array_map('trim', $request->variables ?? []),
                function($var) { return !empty($var); }
            );

            $updateData = [
                'key' => $request->key,
                'variables' => array_values($variables),
            ];

            if ($request->has('status')) {
                $updateData['is_active'] = ($request->status === 'active');
                $updateData['status'] = $request->status;
            }

            $mailTemplate->update($updateData);
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
        $mailTemplate->translations()->delete();
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

            $htmlBody = view('emails.layouts.master', [
                'subject' => $rendered['subject'],
                'content' => $rendered['body'],
                'showPreferences' => false
            ])->render();

            return response()->json([
                'success' => true,
                'subject' => $rendered['subject'],
                'body' => $rendered['body'],
                'html_body' => $htmlBody
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
     * Send test email
     */
    public function sendTestEmail(Request $request, MailTemplate $mailTemplate)
    {
        $request->validate([
            'email' => 'required|email',
            'lang_id' => 'nullable|integer',
        ]);

        $langId = $request->get('lang_id', 1);
        $result = MailService::sendTestTemplate($mailTemplate, $request->email, $langId);

        return response()->json($result, $result['success'] ? 200 : 400);
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
            'user_name' => 'John Doe',
            'email' => 'john@example.com',
            'otp' => '849201',
            'password' => 'newpassword123',
            'business_name' => 'Bluehost',
            'rating' => '4.8',
            'review_url' => url('/'),
            'admin_review_url' => url('/admin-dashboard/reviews'),
            'login_url' => url('/login'),
            'dashboard_url' => url('/user-dashboard'),
            'ticket_id' => 'TICK-90812',
            'subject' => 'Website Inquiry',
            'message' => 'I would like to inquire about listing my business.',
            'reply_message' => 'Thank you for contacting us. We have reviewed your request and updated your listing.',
            'vendor_name' => 'John Doe',
            'admin_vendor_url' => url('/admin-dashboard/vendor-registration-requests'),
            'status' => 'Approved',
            'reason' => 'Submission did not meet verification criteria.',
            'support_url' => url('/en-us/contact'),
            'app_name' => config('app.name', 'Localio'),
            'app_url' => url('/'),
        ];

        $lowerVariable = strtolower($variable);

        if (isset($sampleValues[$lowerVariable])) {
            return $sampleValues[$lowerVariable];
        }

        foreach ($sampleValues as $key => $value) {
            if (Str::contains($lowerVariable, $key) || Str::contains($key, $lowerVariable)) {
                return $value;
            }
        }

        return '[' . ucfirst(str_replace('_', ' ', $variable)) . ']';
    }
}
