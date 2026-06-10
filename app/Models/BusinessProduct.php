<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProduct extends Model
{
    use HasFactory;
    protected $table = 'business_product';
    protected $fillable = ['business_id', 'product_id', 'created_at', 'updated_at'];

    public function product(){
        return $this->hasMany(Product::class, 'product_id', 'id');
    }
}
