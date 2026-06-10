<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeContentMedia extends Model
{
    use HasFactory;

    protected $table = 'home_content_media'; 

    protected $fillable = [
        'home_content_id',
        'file_path', 
        'file_name', 
    ];

    public function homeContent()
    {
        return $this->belongsTo(HomeContent::class);
    }
}
