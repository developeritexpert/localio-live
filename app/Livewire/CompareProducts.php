<?php
namespace App\Livewire;

use Livewire\Component;
use App\Services\CompareService;

class CompareProducts extends Component
{
    public $comparedProducts = [];
    public $errorMessage = '';
    public $item;
    public $isDisabled = false;
    public $isInCompare = false;
    protected $listeners = ['toggleCompareProduct' => 'refreshComparedProducts'];

    public function mount($item)
    {
        $this->item = $item;
        $this->refreshComparedProducts();
    }
    public function refreshComparedProducts()
    {
        $this->comparedProducts = app(CompareService::class)->getComparedProducts();
        $this->isInCompare = in_array((int)$this->item->id, $this->comparedProducts);
        $this->isDisabled = count($this->comparedProducts) >= 2 && !$this->isInCompare;
    }
    public function toggleCompare($productId)
    {
        $productId = (int) $productId;
        $compareService = app(CompareService::class);

        $result = $compareService->toggleProductComparison($productId);

        if ($result['error']) {
            $this->isInCompare = in_array($productId, $result['compared_products']);
            $this->dispatch('show-toastr-error', message: $result['error']);
        } else {
            $this->comparedProducts = $result['compared_products'];
            $this->isInCompare = in_array($productId, $this->comparedProducts);
            $this->errorMessage = '';

            // Dispatch event to notify all components
            $this->dispatch('toggleCompareProduct');
        }
        $this->isDisabled = count($this->comparedProducts) >= 2 && !$this->isInCompare;
    }

    public function render()
    {
        return view('livewire.compare-products');
    }
}
