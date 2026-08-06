<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Business;
use App\Models\BusinessFaq;
use App\Models\BusinessFaqTranslation;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class BusinessFaqManager extends Component
{
    public $businessId;
    public $business;

    public $languages = [];
    public $faqLangId = null; // Dropdown me selected language

    public $businessFAQs = [];

    public $faqQuestion = '';
    public $faqAnswer = '';
    public $editingFAQId = null;

    public function mount($business)
    {
        $this->businessId = $business;
        $this->business = Business::with('translations')->findOrFail($business);

        $this->languages = Language::all();

        // Default language: business ki current site language, ya list ki pehli language
        $this->faqLangId = getCurrentLanguageID() ?? optional($this->languages->first())->id;

        $this->loadBusinessFAQs();
    }

    public function render()
    {
        return view('livewire.admin.business-faq-manager');
    }

    // Dropdown change hote hi auto trigger hota hai (Livewire naming convention)
    public function updatedFaqLangId()
    {
        $this->resetFAQForm();
        $this->loadBusinessFAQs();
    }

    public function loadBusinessFAQs()
    {
        $this->businessFAQs = BusinessFaq::where('business_id', $this->businessId)
            ->with(['translation' => function ($query) {
                $query->where('lang_id', $this->faqLangId);
            }])
            ->ordered()
            ->get()
            ->map(function ($faq) {
                $translation = $faq->translation;
                return [
                    'id' => $faq->id,
                    'question' => $translation->question ?? '',
                    'answer' => $translation->answer ?? '',
                    'position' => $faq->position,
                    'status' => $faq->status
                ];
            })
            ->toArray();
    }

    public function addFAQ()
    {
        $this->validate([
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer' => 'required|string|max:2000',
        ]);

        DB::transaction(function () {
            $nextPosition = BusinessFaq::where('business_id', $this->businessId)->max('position') + 1;

            $faq = BusinessFaq::create([
                'business_id' => $this->businessId,
                'position' => $nextPosition,
                'status' => 1
            ]);

            BusinessFaqTranslation::create([
                'business_faq_id' => $faq->id,
                'lang_id' => $this->faqLangId,
                'question' => $this->faqQuestion,
                'answer' => $this->faqAnswer
            ]);
        });

        $this->resetFAQForm();
        $this->loadBusinessFAQs();

        session()->flash('message', 'FAQ added successfully!');
    }

    public function editFAQ($faqId)
    {
        $faq = BusinessFaq::with(['translation' => function ($query) {
            $query->where('lang_id', $this->faqLangId);
        }])->find($faqId);

        if (!$faq) return;

        $this->editingFAQId = $faqId;
        $this->faqQuestion = $faq->translation->question ?? '';
        $this->faqAnswer = $faq->translation->answer ?? '';
    }

    public function updateFAQ()
    {
        $this->validate([
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer' => 'required|string|max:2000',
        ]);

        if (!$this->editingFAQId) return;

        BusinessFaqTranslation::updateOrCreate(
            [
                'business_faq_id' => $this->editingFAQId,
                'lang_id' => $this->faqLangId
            ],
            [
                'question' => $this->faqQuestion,
                'answer' => $this->faqAnswer
            ]
        );

        $this->resetFAQForm();
        $this->loadBusinessFAQs();

        session()->flash('message', 'FAQ updated successfully!');
    }

    public function deleteFAQ($faqId)
    {
        DB::transaction(function () use ($faqId) {
            $faq = BusinessFaq::find($faqId);
            if (!$faq) return;

            $deletedPosition = $faq->position;
            $faq->delete();

            BusinessFaq::where('business_id', $this->businessId)
                ->where('position', '>', $deletedPosition)
                ->decrement('position');
        });

        $this->loadBusinessFAQs();
        session()->flash('message', 'FAQ deleted successfully!');
    }

    public function reorderFAQs($orderedIds)
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $faqId) {
                BusinessFaq::where('id', $faqId)
                    ->where('business_id', $this->businessId)
                    ->update(['position' => $index + 1]);
            }
        });

        $this->loadBusinessFAQs();
        session()->flash('message', 'FAQ order updated successfully!');
    }

    public function toggleFAQStatus($faqId)
    {
        $faq = BusinessFaq::find($faqId);
        if (!$faq) return;

        $faq->update(['status' => !$faq->status]);
        $this->loadBusinessFAQs();

        $status = $faq->status ? 'activated' : 'deactivated';
        session()->flash('message', "FAQ {$status} successfully!");
    }

    public function cancelFAQEdit()
    {
        $this->resetFAQForm();
    }

    private function resetFAQForm()
    {
        $this->faqQuestion = '';
        $this->faqAnswer = '';
        $this->editingFAQId = null;
        $this->resetValidation(['faqQuestion', 'faqAnswer']);
    }
}