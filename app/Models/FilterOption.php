<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterOption extends Model
{
    use HasFactory;
    protected $fillable = ['filter_id', 'name', 'min_value',
    'max_value',
    'unit','default_toggle','default_range',
    'on_label','is_default',
    'off_label','filter_type_id'];

    public function translations()
    {
        return $this->hasMany(FilterOptionTranslation::class);
    }
    public function filter()
{
    return $this->belongsTo(Filter::class);
}
public function filterType()
{
    return $this->belongsTo(FilterType::class);
}

}
