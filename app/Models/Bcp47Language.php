<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bcp47Language extends Model
{
    use HasFactory;

    protected $table = 'bcp47_languages';

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Get all base languages (country assignments) using this BCP 47 code.
     */
    public function baseLanguages()
    {
        return $this->hasMany(BaseLanguage::class, 'bcp47_language_id');
    }
}
