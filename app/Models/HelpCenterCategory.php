<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCenterCategory extends Model
{
    use HasFactory;

    protected $fillable = ['help_center_content_id', 'image', 'title', 'description'];

    public function content(){
       return $this->belongsTo(HelpCenterContent::class ,'help_center_content_id');
    }
}
