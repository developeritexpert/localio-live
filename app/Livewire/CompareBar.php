<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\CompareService;

class CompareBar extends Component
{
    public $comparedProducts = [];
    public $errorMessage = '';

    protected $listeners = ['toggleCompareProduct' => 'refreshComparedProducts'];

    public function mount()
    {
        $this->refreshComparedProducts();
    }

    public function refreshComparedProducts()
    {
        $this->comparedProducts = app(CompareService::class)->getComparedProducts();
    }

    public function goToComparison()
    {
        return redirect()->route('product-comparison');
    }

    public function render()
    {
        return view('livewire.compare-bar');
    }
}
