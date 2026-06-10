<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsTranslation extends Model
{
    protected $table = "terms_translations";
    protected $fillable = [
        'lang_id','terms_id','title','description','key','status'
    ];
    use HasFactory;
}
