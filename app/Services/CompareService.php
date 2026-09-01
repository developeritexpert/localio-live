<?php
namespace App\Services;

class CompareService
{
    protected $maxCompareProducts = 4;

    public function getComparedProducts()
    {
        $products = session()->get('compared_products', []);
        return is_array($products) ? array_values($products) : [];
    }

    public function toggleProductComparison($productId)
    {
        $productId = (int) $productId;
        $comparedProducts = $this->getComparedProducts();

        if (in_array($productId, $comparedProducts)) {
            $comparedProducts = array_values(array_diff($comparedProducts, [$productId]));
        } else {
            if (count($comparedProducts) >= $this->maxCompareProducts) {
                return [
                    'error' => "You can compare a maximum of {$this->maxCompareProducts} products.",
                    'compared_products' => $comparedProducts
                ];
            }

            $comparedProducts[] = $productId;
            $comparedProducts = array_values($comparedProducts);
        }

        session()->put('compared_products', $comparedProducts);

        return [
            'compared_products' => $comparedProducts,
            'error' => null
        ];
    }
}