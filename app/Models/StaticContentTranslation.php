<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticContentTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['static_content_key_id', 'lang_id', 'value'];

    public function staticContent()
    {
        return $this->belongsTo(StaticContentKey::class);
    }
    public function key()
{
    return $this->belongsTo(StaticContentKey::class, 'static_content_key_id');
}
}
