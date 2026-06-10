<?php

namespace App\Livewire\Admin;

use App\Models\AiPromptAttach;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\BusinessCategoryTopic;
use Laravel\Prompts\Prompt;
use Livewire\Component;
use App\Services\AiService;

class BusinessAiModal extends Component
{

    public $show = false;
    public $modalId = null;
    public $modalTitle = '';
    public $pre_configured_prompt = '';
    public $prompt_name = '';
    public $loading = false;
    public $ai_output = '';
    public $selectedModel = '';
    public $businessId = null;
    public $fieldType = '';

    protected $aiService;



    protected $listeners = ['openBusinessAiModal'];

    public function boot(AiService $aiService)
    {
        $this->aiService = $aiService;
  
    }

    // public function mount()
    // {
    //     // Set default model if available
    //     $aiModelRefs = Setting::where('type', 'ai')
    //         ->whereNotNull('model_ref')
    //         ->distinct()
    //         ->pluck('model_ref');
        
    //     if ($aiModelRefs->isNotEmpty()) {
    //         $this->selectedModel = $aiModelRefs->first();
    //     }
    // }

    // public function openBusinessAiModal($data)
    // {
    //     // Debug: Log the received data
      

    //     // dd('openBusinessAiModal called with data:', $data);
        
    //     $business=BusinessTranslation::where('business_id',$data['businessId'])->first();
    //     // dd($business);
    //     $this->show = true;
    //     $this->modalTitle = $business->name ?? 'This Business';
    //     $this->fieldType = $data['fieldType'] ?? '';
    //     $this->businessId = $data['businessId'] ?? null;
   

    //     $this->modalId=$data['modalId'] ?? null;


    //     // Set prompt based on field type
    //     $this->setPromptByFieldType($this->fieldType , $this->modalId);


    //     // Debug: Log the current state
    //     // dd('Modal state after opening:', [
    //     //     'show' => $this->show,
    //     //     'modalTitle' => $this->modalTitle,
    //     //     'fieldType' => $this->fieldType,
    //     //     'prompt_name' => $this->prompt_name
    //     // ]);

    // }


    public function openBusinessAiModal($data)
    {
        $this->show = true;
        $this->fieldType = $data['fieldType'] ?? '';
        $this->modalId = $data['modalId'] ?? null;
        $this->businessId = $data['businessId'] ?? null;
    
        $businessTranslation = BusinessTranslation::where('business_id', $this->businessId)->first();
        $this->modalTitle = $businessTranslation->name ?? 'This Business';
    
        $this->pre_configured_prompt = '';
        $this->prompt_name = 'AI Prompt';
    
        // Handle special logic for detailtopic field
        if ($this->fieldType === 'detailtopic') {
            $business = Business::find($this->businessId);
    
            if ($business && $business->category_id) {
                // ✅ Fetch translated topic titles with id + title
                $topics = BusinessCategoryTopic::where('category_id', $business->category_id)
                    ->with(['translations' => function ($query) {
                        $query->where('lang_id', getCurrentLanguageID());
                    }])
                    ->get()
                    ->map(function ($topic) use ($businessTranslation) {
                        return [
                            'id' => $topic->id,
                            'title' => str_replace('{business_name}', $businessTranslation->name ?? 'This Business', $topic->translations->first()?->title ?? 'Topic ' . $topic->id),
                        ];
                    })
                    ->values()
                    ->toArray();
    
                // ✅ Format topics into prompt structure
                $formattedTopics = $this->buildTopicLines($this->modalTitle, $topics);
    
                // 🔍 Get attached prompt (if any)
                $Prompt_attach = AiPromptAttach::with('prompt')->where('resource_id', $this->modalId)->first();
    
                if ($Prompt_attach && $Prompt_attach->prompt) {
                    $this->prompt_name = $Prompt_attach->prompt->name ?? 'AI Prompt';
    
                    // ✅ Inject values
                    $this->pre_configured_prompt = str_replace(
                        ['{BusinessName}', '{TopicsList}'],
                        [$this->modalTitle, $formattedTopics],
                        $Prompt_attach->prompt->original_prompt ?? ''
                    );
                } else {
                    // Fallback
                    $this->pre_configured_prompt = "Business Name: {$this->modalTitle}\n\n{$formattedTopics}";
                }
            } else {
                $this->pre_configured_prompt = "No topics found for this business. Please add business first.";
            }
        } else {
            // For other fields
            $Prompt_attach = AiPromptAttach::with('prompt')->where('resource_id', $this->modalId)->first();
    
            if ($Prompt_attach && $Prompt_attach->prompt) {
                $this->prompt_name = $Prompt_attach->prompt->name ?? 'AI Prompt';
                $this->pre_configured_prompt = str_replace(
                    '{BusinessName}',
                    $this->modalTitle,
                    $Prompt_attach->prompt->original_prompt ?? ''
                );
            }
        }
    }
    
    
    



    public function setPromptByFieldType($fieldType , $modalId)
    {

        // dd($fieldType , $modalId);

        $Prompt_attach =AiPromptAttach::with('prompt')->where('resource_id' ,$modalId)->first();
        // dd($Prompt_attach);
        if(isset($Prompt_attach) && $Prompt_attach->prompt ){

            $this->prompt_name = $Prompt_attach->prompt->name ?? '';

            // $prompt = str_replace('{BusinessName}', $this->modalTitle , $this->pre_configured_prompt);

            $this->pre_configured_prompt =  str_replace('{BusinessName}', $this->modalTitle , $Prompt_attach->prompt->original_prompt ?? '');
        }

    }

    public function regenerateResponse()
    {
        $this->sendPrompt();
    }

    public function sendPrompt()
    {
        $this->loading = true;
    
        try {
            // Step 1: Start with stored prompt
            $prompt = $this->pre_configured_prompt;
    
            // Step 2: Handle dynamic topics for 'detailtopic' field
            if ($this->fieldType === 'detailtopic') {
                $business = Business::find($this->businessId);
    
                if ($business && $business->category_id) {
                    // 🔹 Fetch translated topic titles
                    $topics = BusinessCategoryTopic::where('category_id', $business->category_id)
                    ->with(['translations' => function ($q) {
                        $q->where('lang_id', getCurrentLanguageID());
                    }])
                    ->get()
                    ->map(function ($topic) {
                        return [
                            'id' => $topic->id,
                            'title' => $topic->translations->first()?->title ?? 'Topic ' . $topic->id,
                        ];
                    })
                    ->values()
                    ->toArray();
                
    
                    // 🔹 Generate the topic list string
                    $topicsList = $this->buildTopicLines($this->modalTitle, $topics);
    
                    // 🔹 Replace both placeholders in stored prompt
                    $prompt = str_replace(
                        ['{BusinessName}', '{TopicsList}'],
                        [$this->modalTitle, $topicsList],
                        $this->pre_configured_prompt
                    );
    
                    // Update state (if user wants to edit the final prompt)
                    $this->pre_configured_prompt = $prompt;
                } else {
                    $prompt = "No topics found for this business.";
                    $this->pre_configured_prompt = $prompt;
                }
            } else {
                // Fallback for other prompts — replace just {BusinessName}
                $prompt = str_replace('{BusinessName}', $this->modalTitle, $prompt);
            }
    
            // Step 3: Send to AI service
            $responseText = $this->aiService->getGeminiAiResponse($prompt);
            $this->ai_output = $responseText;
    
        } catch (\Exception $e) {
            $this->ai_output = "Error generating AI response: " . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function buildTopicLines($businessName, $topics)
    {
        $topicLines = [];
    
        foreach ($topics as $topic) {
            $title = str_replace('{business_name}', $businessName, $topic['title']);
            $topicLines[] = "---\n- TopicID: {$topic['id']}\n- Topic: {$title}";
        }
    
        return implode("\n", $topicLines);
    }
    
    

    public function confirmAndSave()
    {
        
        // Dispatch event to parent component with the AI output
        $this->dispatch('aiContentGenerated', [
            'fieldType' => $this->fieldType,
            'content' => $this->ai_output,
            'businessId' => $this->businessId
        ]);
        
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->show = false;
        $this->reset(['ai_output', 'loading', 'modalTitle', 'fieldType', 'businessId']);
    }

    public function render()
    {
        // $aiModelRefs = Setting::where('type', 'ai')
        // ->whereNotNull('model_ref')
        // ->distinct()
        // ->pluck('model_ref');

        return view('livewire.admin.business-ai-modal');
    }
}
