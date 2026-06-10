<?php
namespace App\Services;

class CompareService
{
    protected $maxCompareProducts = 2;

    public function getComparedProducts()
    {
        return session()->get('compared_products', []);
    }
public function toggleProductComparison($productId)
{
    $productId = (int) $productId;
    $comparedProducts = session()->get('compared_products', []);

    if (in_array($productId, $comparedProducts)) {
        $comparedProducts = array_diff($comparedProducts, [$productId]);
    } else {
        if (count($comparedProducts) >= $this->maxCompareProducts) {
            return [
                'error' => "You can compare a maximum of {$this->maxCompareProducts} products.",
                'compared_products' => $comparedProducts
            ];
        }

        $comparedProducts[] = $productId;
    }

    session()->put('compared_products', $comparedProducts);

    return [
        'compared_products' => $comparedProducts,
        'error' => null
    ];
}
}
