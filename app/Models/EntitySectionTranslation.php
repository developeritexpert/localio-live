<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntitySectionTranslation extends Model
{
    use HasFactory;

    const STATUS_MISSING   = 'missing';
    const STATUS_WAITING   = 'waiting';
    const STATUS_BATCHED   = 'batched';
    const STATUS_COMPLETED = 'completed';
    const STATUS_OUTDATED  = 'outdated';

    protected $table = 'entity_section_translations';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'section_group',
        'field_key',
        'source_headline',
        'source_subtext',
        'source_hash',
        'lang_id',
        'translated_headline',
        'translated_subtext',
        'status',
        'batch_id',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'lang_id'   => 'integer',
        'batch_id'  => 'integer',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }

    public function batch()
    {
        return $this->belongsTo(TranslationBatch::class, 'batch_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'entity_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'entity_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'entity_id');
    }

    public static function computeHash(?string $headline, ?string $subtext): string
    {
        return hash('sha256', trim((string)$headline) . '||' . trim((string)$subtext));
    }
}
