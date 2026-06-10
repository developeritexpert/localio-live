<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media';
    protected $fillable = ['dir_path', 'file_name', 'file_type', 'file_size'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->dir_path . '/' . $this->file_name);
    }
}
