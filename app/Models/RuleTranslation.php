<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleTranslation extends Model
{
    use HasFactory;
    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
