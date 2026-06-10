<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessChangeRequest extends Model
{

    protected $fillable = [
        'business_id',
        'field',
        'type',
        'column',
        'value',
        'lang_id',
        'requested_by',
        'status',
        'action_type'
    ];

    public function business(){
        return $this->belongsTo(Business::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'requested_by');
    }
    use HasFactory;
}
