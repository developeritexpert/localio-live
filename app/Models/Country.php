<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name','code','status'];
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_countries')
            ->withPivot('status')
            ->withTimestamps();
    }
}
