<?php

namespace App\Livewire\Admin;

use App\Models\ExclusiveDeal;
use App\Models\Product;
use App\Models\Category;
use App\Models\Country;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ExclusiveDealForm extends Component
{
    public $dealId;
    public $appliesType = 'product';
    public $appliesId;
    public $countryCode;
    public $priceType = 'base_price';
    public $discountPercent;
    public $startsAt;
    public $endsAt;
    public $status = 'active';

    public $products = [];
    public $categories = [];
    public $isEdit = false;

    protected $rules = [
        'appliesType' => 'required|in:product,category',
        'appliesId' => 'required',
        'countryCode' => 'required|string|max:2|alpha',
        'priceType' => 'required|in:base_price,standard_price,pro_price',
        'discountPercent' => 'required|numeric|min:0|max:100',
        'startsAt' => 'required|date',
        'endsAt' => 'required|date|after:startsAt',
        'status' => 'required|in:active,inactive',
    ];

    public function mount($dealId = null)
    {
        $this->loadSelectionData();

        if ($dealId) {
            $this->dealId = $dealId;
            $this->isEdit = true;
            $this->loadDeal();
        } else {
            // Set default dates for new deals
            $this->startsAt = Carbon::now()->format('Y-m-d');
            $this->endsAt = Carbon::now()->addDays(30)->format('Y-m-d');
        }
    }

    public function loadDeal()
    {
        $deal = ExclusiveDeal::findOrFail($this->dealId);

        $this->appliesType = $deal->applies_to_type;
        $this->appliesId = $deal->applies_to_id;
        $this->countryCode = $deal->country_code;
        $this->priceType = $deal->price_type;
        $this->discountPercent = $deal->discount_percent;
        $this->startsAt = Carbon::parse($deal->starts_at)->format('Y-m-d');
        $this->endsAt = Carbon::parse($deal->ends_at)->format('Y-m-d');
        $this->status = $deal->status;
    }

    public function loadSelectionData()
    {
        $lang_id = getCurrentLanguageID();
        $this->products = Product::whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->get();

        $this->categories = Category::whereHas('translations', function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id);
        })->get();
    }

    public function save()
    {
        $this->validate();

        $countryCodeUpper = strtoupper($this->countryCode);

        // Check if the entered country code exists
        $countryExists = Country::where('country_code', $countryCodeUpper)->exists();

        if (!$countryExists) {
            $suggestions = Country::where('country_code', 'LIKE', $countryCodeUpper[0] . '%')
                ->limit(3)
                ->pluck('country_code')
                ->toArray();

            $message = "Country code is invalid.";
            if (!empty($suggestions)) {
                $message .= " Did you mean: " . implode(', ', $suggestions) . "?";
            }

            $this->addError('countryCode', $message);
            return;
        }

        $dealData = [
            'applies_to_type' => $this->appliesType,
            'applies_to_id' => $this->appliesId,
            'country_code' => $countryCodeUpper,
            'price_type' => $this->priceType,
            'discount_percent' => $this->discountPercent,
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
            'status' => $this->status,
        ];

        if ($this->isEdit) {
            $deal = ExclusiveDeal::findOrFail($this->dealId);
            $deal->update($dealData);
            session()->flash('success', 'Deal updated successfully!');
        } else {
            ExclusiveDeal::create($dealData);
            session()->flash('success', 'Deal created successfully!');
        }

        $this->redirect(route('exclusive-deals.index'));
    }

    public function render()
    {
        return view('livewire.admin.exclusive-deals-form');
    }
}
