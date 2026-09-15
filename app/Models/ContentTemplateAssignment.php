<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentTemplateAssignment extends Model
{
    use HasFactory;

    protected $table = 'content_template_assignments';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'lang_id',
        'field_key',
        'template_id',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'lang_id' => 'integer',
        'template_id' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(ContentTemplate::class, 'template_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'entity_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'entity_id');
    }
}