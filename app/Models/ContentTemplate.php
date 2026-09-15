<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentTemplate extends Model
{
    use HasFactory;

    protected $table = 'content_templates';

    protected $fillable = [
        'section_group',
        'field_key',
        'option_number',
        'headline',
        'subtext',
        'lang_id',
        'is_active',
    ];

    protected $casts = [
        'option_number' => 'integer',
        'lang_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(ContentTemplateAssignment::class, 'template_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}