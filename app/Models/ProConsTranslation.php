<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProConsTranslation extends Model
{
    protected $table = 'pro_cons_translations';
    protected $fillable = ['pro_cons_id','lan_id', 'name', 'description','type', 'created_at', 'updated_at'];
    use HasFactory;
    public function proCons()
    {
        return $this->belongsTo(ProCons::class, 'pro_cons_id');
    }
}
