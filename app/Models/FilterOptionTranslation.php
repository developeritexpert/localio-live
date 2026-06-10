<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterOptionTranslation extends Model
{
    use HasFactory;
    protected $fillable = ['filter_option_id', 'language_id', 'name','min_value',
    'max_value',
    'unit','default_toggle','default_range',
    'on_label','is_default',
    'off_label'];
    public function option()
    {
        return $this->belongsTo(FilterOption::class, 'filter_option_id');
    }
    public function filterType()
    {
        return $this->belongsTo(FilterType::class);
    }
}
