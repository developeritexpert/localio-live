<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;
    protected $fillable = [
        'policy_id',
        'title',
        'description',
        'status',
    ];

    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }
    public function translations()
    {
        return $this->hasMany(RuleTranslation::class);
    }

}
