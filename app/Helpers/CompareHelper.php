<?php
namespace App\Helpers;

class CompareHelper
{
    public static function get()
    {
        return session()->get('compared_products', []);
    }

    public static function put(array $products)
    {
        session()->put('compared_products', $products);
    }

    public static function add($productId, $max = 3)
    {
        $products = self::get();
        if (!in_array($productId, $products) && count($products) < $max) {
            $products[] = $productId;
            self::put($products);
        }
        return $products;
    }

    public static function remove($productId)
    {
        $products = array_filter(self::get(), fn($id) => $id != $productId);
        self::put($products);
        return $products;
    }
}
