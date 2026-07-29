<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Business;

class WriteReviewSearch extends Component
{
    public $query = '';
    public $results = [];
    public $lang_id;

    public function mount()
    {
        $this->lang_id = getCurrentLanguageID();
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $this->results = Business::where('status', 1)
            ->whereHas('languages', function ($q) {
                $q->where('language_id', $this->lang_id);
            })
            ->where(function ($q) {
                $q->where('active_all_countries', 1)
                      ->orWhereHas('countries', function ($c) {
                          $c->where('country_id', getCurrentCountry());
                      });
            })
            ->whereHas('translations', function ($q) {
                $q->where('lang_id', $this->lang_id)
                  ->where('name', 'like', '%' . $this->query . '%');
            })
            ->with(['translations' => fn($q) => $q->where('lang_id', $this->lang_id)])
            ->take(8)
            ->get()
            ->map(function ($business) {
                return [
                    'id' => $business->id,
                    'name' => $business->translations->first()->name ?? 'Unnamed',
                    'slug' => $business->translations->first()->slug ?? '',
                    'icon' => $business->icon_id ?? 'front/img/logo.svg',
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.write-review-search');
    }
}
