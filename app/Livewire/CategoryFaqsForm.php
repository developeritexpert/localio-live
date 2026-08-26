<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CategoryTranslation;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class CategoryFaqsForm extends Component
{
    public $categoryId;
    public $lang_id;
    
    // JSON Upload
    public $json_data;
    
    // Category Management
    public $newCategoryName;
    public $faqCategories = [];
    
    // FAQ Management
    public $faqCategoryId;
    public $faqQuestion;
    public $faqAnswer;
    public $editingFAQIndex = null;
    public $faqs = [];

    public function mount($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->lang_id = Language::where('lang_code', getCurrentLocale())->value('id');
        $this->loadData();
    }

    public function loadData()
    {
        $translation = CategoryTranslation::where('category_id', $this->categoryId)
            ->where('lang_id', $this->lang_id)
            ->first();

        if ($translation) {
            $this->faqCategories = !empty($translation->faq_categories) ? json_decode($translation->faq_categories, true) : [];
            $this->faqs = !empty($translation->faqs) ? json_decode($translation->faqs, true) : [];
        } else {
            $this->faqCategories = [];
            $this->faqs = [];
        }
    }

    public function addFaqCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:100',
        ]);

        $name = trim($this->newCategoryName);
        
        if (!in_array($name, $this->faqCategories)) {
            $this->faqCategories[] = $name;
            $this->saveData();
            session()->flash('message', 'FAQ Category added successfully!');
        } else {
            session()->flash('error', 'Category already exists.');
        }

        $this->newCategoryName = '';
    }

    public function deleteFaqCategory($index)
    {
        if (isset($this->faqCategories[$index])) {
            $catName = $this->faqCategories[$index];
            
            // Remove category assignment from FAQs
            foreach ($this->faqs as &$faq) {
                if (isset($faq['category']) && $faq['category'] === $catName) {
                    $faq['category'] = 'General';
                }
            }
            
            unset($this->faqCategories[$index]);
            $this->faqCategories = array_values($this->faqCategories);
            $this->saveData();
            session()->flash('message', 'FAQ Category deleted successfully!');
        }
    }

    public function resetFAQForm()
    {
        $this->faqCategoryId = '';
        $this->faqQuestion = '';
        $this->faqAnswer = '';
        $this->editingFAQIndex = null;
        // Tell frontend to reset CKEditor if applicable
        $this->dispatch('reset-ckeditor');
    }

    public function addFAQ()
    {
        $this->validate([
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer' => 'required|string|max:5000',
        ]);

        $this->faqs[] = [
            'category' => !empty($this->faqCategoryId) ? $this->faqCategoryId : 'General',
            'question' => $this->faqQuestion,
            'answer' => $this->faqAnswer
        ];

        $this->saveData();
        $this->resetFAQForm();
        session()->flash('message', 'FAQ added successfully!');
    }

    public function editFAQ($index)
    {
        if (isset($this->faqs[$index])) {
            $this->editingFAQIndex = $index;
            $this->faqCategoryId = $this->faqs[$index]['category'] ?? 'General';
            $this->faqQuestion = $this->faqs[$index]['question'] ?? '';
            $this->faqAnswer = $this->faqs[$index]['answer'] ?? '';
            $this->dispatch('set-ckeditor', ['data' => $this->faqAnswer]);
        }
    }

    public function updateFAQ()
    {
        $this->validate([
            'faqQuestion' => 'required|string|max:500',
            'faqAnswer' => 'required|string|max:5000',
        ]);

        if ($this->editingFAQIndex !== null && isset($this->faqs[$this->editingFAQIndex])) {
            $this->faqs[$this->editingFAQIndex] = [
                'category' => !empty($this->faqCategoryId) ? $this->faqCategoryId : 'General',
                'question' => $this->faqQuestion,
                'answer' => $this->faqAnswer
            ];

            $this->saveData();
            $this->resetFAQForm();
            session()->flash('message', 'FAQ updated successfully!');
        }
    }

    public function deleteFAQ($index)
    {
        if (isset($this->faqs[$index])) {
            unset($this->faqs[$index]);
            $this->faqs = array_values($this->faqs); // reindex
            $this->saveData();
            session()->flash('message', 'FAQ deleted successfully!');
        }
    }

    public function uploadJsonFaqs()
    {
        if (empty($this->json_data)) {
            session()->flash('error', 'Please paste JSON data first.');
            return;
        }

        $decoded = json_decode($this->json_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            session()->flash('error', 'Invalid JSON format: ' . json_last_error_msg());
            return;
        }

        if (!is_array($decoded)) {
            session()->flash('error', 'JSON must be an array of FAQs.');
            return;
        }

        $importedCount = 0;
        foreach ($decoded as $item) {
            if (!empty($item['question']) || !empty($item['answer'])) {
                $cat = $item['category'] ?? 'General';
                
                // Add category if not exists
                if ($cat !== 'General' && !in_array($cat, $this->faqCategories)) {
                    $this->faqCategories[] = $cat;
                }

                $this->faqs[] = [
                    'category' => $cat,
                    'question' => $item['question'] ?? '',
                    'answer' => $item['answer'] ?? ''
                ];
                $importedCount++;
            }
        }

        if ($importedCount > 0) {
            $this->saveData();
            $this->json_data = '';
            session()->flash('message', "Successfully imported $importedCount FAQs!");
        } else {
            session()->flash('error', 'No valid FAQs found in JSON.');
        }
    }

    private function saveData()
    {
        $translation = CategoryTranslation::firstOrCreate(
            ['category_id' => $this->categoryId, 'lang_id' => $this->lang_id]
        );
        
        $translation->faq_categories = !empty($this->faqCategories) ? json_encode($this->faqCategories) : null;
        $translation->faqs = !empty($this->faqs) ? json_encode($this->faqs) : null;
        $translation->save();
        
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.category-faqs-form');
    }
}
