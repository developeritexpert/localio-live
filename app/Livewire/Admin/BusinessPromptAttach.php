<?php

namespace App\Livewire\Admin;

use App\Models\AiPrompt;
use Livewire\Component;
use App\Models\AiPromptAttach;

class BusinessPromptAttach extends Component
{
    
    public $businessPromptAttach = [];
    public $prompts;
    public $selectedPrompts = [];


    public function mount(){
        $this->businessPromptAttach = AiPromptAttach::all(); 
        $this->prompts=AiPrompt::all();

        // Initialize selectedPrompts from existing attachments
        foreach ($this->businessPromptAttach as $attach) {
            $this->selectedPrompts[$attach->id] = $attach->prompt_id;
        }
    }

    public function getSelectedPromptName($promptAttachId)
    {
        $selectedId = $this->selectedPrompts[$promptAttachId] ?? null;
    
        return $selectedId
            ? $this->prompts->firstWhere('id', $selectedId)?->name
            : null;
    }
    




    public function savePromptSelection($resource_id,$prompts_id){

        // dd($resource_id,$prompts_id);
        $attached_prompt_id = AiPromptAttach::where('resource_id', $resource_id)->first();

        if ($attached_prompt_id) {
            $attached_prompt_id->update(['prompt_id' => $prompts_id]);

            $this->dispatch('show-toast', 
            type: 'success',
            message: 'Sucessfully changed the Prompt !'
        );
        }
        else{
            $this->dispatch('show-toast', 
            type: 'error',
            message: 'Error in changing the Prompt'
        );

        }
        
    }


    public function render()
    {
        return view('livewire.admin.business-prompt-attach');
    }
}
