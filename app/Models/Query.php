<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $fillable = [
        'user_id',
        'query_type',
        'name',
        'email',
        'message',
        'attachment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}