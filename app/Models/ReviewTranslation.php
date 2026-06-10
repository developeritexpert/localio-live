<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['reviews_id', 'title', 'description', 'language_id', 'pros', 'cons'];

    public function review()
    {
        return $this->belongsTo(Review::class, 'reviews_id');
    }
}
