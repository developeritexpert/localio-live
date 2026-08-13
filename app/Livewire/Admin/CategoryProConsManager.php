<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\CategoryProCon;
use Illuminate\Support\Facades\Log;

class CategoryProConsManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $selectedCategoryFilter = '';
    public $selectedTypeFilter = '';
    public $search = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSelectedCategoryFilter() { $this->resetPage(); }
    public function updatingSelectedTypeFilter() { $this->resetPage(); }

    // Form fields for single Add/Edit
    public $editingId = null;
    public $category_id = '';
    public $type = 'pro';
    public $text = '';
    public $status = 1;
    public $showModal = false;

    // JSON text paste
    public $jsonCategoryId = '';
    public $jsonText = '';
    public $showJsonModal = false;
    public $jsonUploadMessage = '';
    public $jsonUploadError = '';

    protected $rules = [
        'category_id' => 'required|exists:categories,id',
        'type'        => 'required|in:pro,con',
        'text'        => 'required|string|max:255',
        'status'      => 'required|boolean',
    ];

    public function openAddModal()
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->category_id = $this->selectedCategoryFilter ?: '';
        $this->type = 'pro';
        $this->text = '';
        $this->status = 1;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $item = CategoryProCon::findOrFail($id);
        $this->editingId = $item->id;
        $this->category_id = $item->category_id;
        $this->type = $item->type;
        $this->text = $item->text;
        $this->status = (int)$item->status;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save()
    {
        $this->validate();

        CategoryProCon::updateOrCreate(
            ['id' => $this->editingId],
            [
                'category_id' => $this->category_id,
                'type'        => $this->type,
                'text'        => trim($this->text),
                'status'      => $this->status,
            ]
        );

        session()->flash('message', $this->editingId ? 'Pro/Con updated successfully.' : 'Pro/Con added successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        CategoryProCon::findOrFail($id)->delete();
        session()->flash('message', 'Pro/Con deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $item = CategoryProCon::findOrFail($id);
        $item->status = !$item->status;
        $item->save();
    }

    public function openJsonModal()
    {
        $this->reset(['jsonText', 'jsonUploadMessage', 'jsonUploadError']);
        $this->jsonCategoryId = $this->selectedCategoryFilter ?: '';
        $this->showJsonModal = true;
    }

    public function closeJsonModal()
    {
        $this->showJsonModal = false;
        $this->reset(['jsonText', 'jsonCategoryId', 'jsonUploadMessage', 'jsonUploadError']);
    }

    public function importJsonText()
    {
        $this->validate([
            'jsonCategoryId' => 'required|exists:categories,id',
            'jsonText'       => 'required|string',
        ], [
            'jsonCategoryId.required' => 'Please select a Target Category.',
            'jsonText.required'       => 'Please paste JSON content in the text area.',
        ]);

        $this->jsonUploadError = '';
        $this->jsonUploadMessage = '';

        try {
            $content = trim($this->jsonText);
            // Remove markdown code fence e.g. ```json ... ```
            $content = preg_replace('/^```(?:json)?/i', '', $content);
            $content = preg_replace('/```$/', '', trim($content));
            $content = preg_replace('/^json\s*/i', '', trim($content));

            $data = json_decode($content, true);

            if (!is_array($data)) {
                $this->jsonUploadError = 'Invalid JSON format. Please ensure your input is a valid JSON array of objects.';
                return;
            }

            $imported = 0;
            $skipped = 0;

            foreach ($data as $item) {
                if (!is_array($item)) continue;

                $text = trim($item['text'] ?? $item['name'] ?? '');
                $type = strtolower(trim($item['type'] ?? 'pro'));
                if (!in_array($type, ['pro', 'con'])) {
                    $type = 'pro';
                }

                $catId = $this->jsonCategoryId ?: ($item['category_id'] ?? null);

                // Fallback: search category by name/title if category_id not provided
                if (!$catId && !empty($item['category_name'])) {
                    $cat = Category::whereHas('categoryTranslations', function ($q) use ($item) {
                        $q->where('name', 'like', trim($item['category_name']))
                          ->orWhere('title', 'like', trim($item['category_name']));
                    })->first();
                    if ($cat) {
                        $catId = $cat->id;
                    }
                }

                if (!$catId || empty($text)) {
                    $skipped++;
                    continue;
                }

                CategoryProCon::create([
                    'category_id' => $catId,
                    'type'        => $type,
                    'text'        => $text,
                    'status'      => isset($item['status']) ? (bool)$item['status'] : true,
                ]);

                $imported++;
            }

            $this->jsonUploadMessage = "Successfully imported {$imported} Pros & Cons." . ($skipped > 0 ? " ({$skipped} skipped due to missing category or text)" : "");
            $this->reset('jsonText');
        } catch (\Exception $e) {
            Log::error('Error importing Pros & Cons JSON: ' . $e->getMessage());
            $this->jsonUploadError = 'An error occurred while parsing JSON: ' . $e->getMessage();
        }
    }

    public function render()
    {
        $langId = getCurrentLanguageID();

        $categories = Category::onlyParents()->with(['categoryTranslations' => function($q) use ($langId) {
            $q->where('lang_id', $langId);
        }])->get();

        $query = CategoryProCon::with(['category.categoryTranslations' => function($q) use ($langId) {
            $q->where('lang_id', $langId);
        }]);

        if ($this->selectedCategoryFilter) {
            $query->where('category_id', $this->selectedCategoryFilter);
        }

        if ($this->selectedTypeFilter) {
            $query->where('type', $this->selectedTypeFilter);
        }

        if (!empty($this->search)) {
            $query->where('text', 'like', '%' . trim($this->search) . '%');
        }

        $items = $query->orderBy('category_id')->orderBy('type')->orderBy('id', 'desc')->paginate(20);

        return view('livewire.admin.category-pro-cons-manager', [
            'categories' => $categories,
            'items'      => $items,
        ]);
    }
}
