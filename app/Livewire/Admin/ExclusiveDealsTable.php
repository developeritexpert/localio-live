<?php

namespace App\Livewire\Admin;

use App\Models\Country;
use App\Models\CountryLanguage;
use Livewire\Component;
use App\Models\ExclusiveDeal;
use Livewire\WithPagination;

class ExclusiveDealsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    protected $listeners = ['dealAdded' => '$refresh', 'dealUpdated' => '$refresh'];

    public function deleteDeal($dealId)
    {
        $deal = ExclusiveDeal::findOrFail($dealId);
        $deal->delete();

        session()->flash('success', 'Deal deleted successfully!');
    }

    public function render()
    {
        
        $lang_id=getCurrentLanguageID();
        $country_id=CountryLanguage::where('language_id',$lang_id)->pluck('country_id');
        $country_code=Country::where('id',$country_id)->pluck('country_code');
        $deals = ExclusiveDeal::query()
            ->with([
                'appliesTo' => function ($morphTo) use ($lang_id) {
                    $morphTo->morphWith([
                        \App\Models\Product::class => ['translations' => function ($query) use ($lang_id) {
                            $query->where('lang_id', $lang_id);
                        }],
                        \App\Models\Category::class => ['translations' => function ($query) use ($lang_id) {
                            $query->where('lang_id', $lang_id);
                        }],
                    ]);
                },
            ])
            ->when($this->search, function ($query) use ($lang_id) {
                $query->whereHas('appliesTo', function ($q) use ($lang_id) {
                    $q->whereHas('translations', function ($q2) use ($lang_id) {
                        $q2->where('lang_id', $lang_id)
                            ->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            })->where('country_code',$country_code)
            ->latest()
            ->paginate(10);
        return view('livewire.admin.exclusive-deals-table', [
            'deals' => $deals
        ]);
    }
}
