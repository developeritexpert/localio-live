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
        'bcp47_language_id',
        'language_tag',
        'is_master',
        'status',
    ];

    protected $casts = [
        'is_master' => 'boolean',
        'status'    => 'integer',
    ];

    /**
     * The BCP 47 Language assigned to this base language entry.
     */
    public function bcp47Language()
    {
        return $this->belongsTo(Bcp47Language::class, 'bcp47_language_id');
    }

    /**
     * Get the site languages (countries) that reference this base language.
     */
    public function languages()
    {
        return $this->hasMany(Language::class, 'base_language_id');
    }

    /**
     * Backward-compatible accessor: $baseLanguage->code returns the BCP 47 code string.
     */
    public function getCodeAttribute(): ?string
    {
        return $this->bcp47Language?->code;
    }
}
