<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeContent extends Model
{
    use HasFactory;
    protected $table = 'home_contents'; // Table name

    protected $fillable = [
        'meta_key',
        'meta_value',
        'lang_code',
        'type',
        'lang_id'
    ];

    /**
     * Define a relationship with HomeContentTranslation.
     */
    public function media()
    {
        return $this->hasMany(HomeContentMedia::class);
    }
}
