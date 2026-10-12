<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProCon extends Model
{
    use HasFactory;

    protected $table = 'category_pro_cons';

    protected $fillable = [
        'category_id',
        'type',
        'text',
        'description',
        'aliases',
        'status',
    ];

    protected $casts = [
        'aliases' => 'array',
        'status'  => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reviews()
    {
        return $this->belongsToMany(Review::class, 'review_pro_cons', 'category_pro_con_id', 'review_id');
    }
}