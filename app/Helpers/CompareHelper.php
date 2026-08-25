<?php
namespace App\Helpers;

class CompareHelper
{
    public static function get()
    {
        $products = session()->get('compared_products', []);
        return is_array($products) ? array_values($products) : [];
    }

    public static function put(array $products)
    {
        session()->put('compared_products', array_values($products));
    }

    public static function add($productId, $max = 3)
    {
        $products = self::get();
        if (!in_array($productId, $products) && count($products) < $max) {
            $products[] = (int) $productId;
            self::put($products);
        }
        return $products;
    }

    public static function remove($productId)
    {
        $products = array_values(array_filter(self::get(), fn($id) => (int)$id !== (int)$productId));
        self::put($products);
        return $products;
    }
}