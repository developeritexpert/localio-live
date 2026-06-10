<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;
    protected $table = 'policies';

    protected $fillable = [
        'lang_id',
    ];

    public function translations()
    {
        return $this->hasMany(PolicyTranslation::class, 'policy_id');
    }

    public function rule()
    {
        return $this->hasMany(Rule::class);
    }

}
