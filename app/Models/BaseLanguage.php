<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseLanguage extends Model
{
    use HasFactory;

    protected $table = 'base_languages';

    protected $fillable = [
        'name',
        'code',
        'language_tag',
        'is_master',
        'status',
    ];

    protected $casts = [
        'is_master' => 'boolean',
        'status' => 'integer',
    ];

    /**
     * Get the site languages (countries) that reference this base language.
     */
    public function languages()
    {
        return $this->hasMany(Language::class, 'base_language_id');
    }
}
