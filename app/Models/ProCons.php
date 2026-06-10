<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProCons extends Model
{
    protected $table = 'pro_cons';
    protected $fillable = ['product_id', 'name','description', 'type', 'created_at', 'updated_at'];
    public function translations()
    {
        return $this->hasMany(ProConsTranslation::class, 'pro_cons_id');
    }

    // Get translation for a specific language
    // public function translation($languageId)
    // {
    //     return $this->hasOne(ProConsTranslation::class, 'pro_cons_id')->where('lang_id', $languageId);
    // }
    public function translation()
    {
        return $this->hasOne(ProConsTranslation::class, 'pro_cons_id','id');
    }
}
