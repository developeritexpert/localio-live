<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticContentKey extends Model
{
    use HasFactory;
    protected $table = 'static_content_keys';
    protected $fillable = ['key', 'default_value', 'name', 'field_type'];

    public function translations()
    {
        return $this->hasMany(StaticContentTranslation::class);
    }

    public function getTranslation($langId)
{
    if ($langId == 1) {
        return $this->default_value;
    }

    return optional($this->translations->firstWhere('lang_id', $langId))->value ?? $this->default_value;
}

}
