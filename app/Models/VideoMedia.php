<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoMedia extends Model
{
    use HasFactory;
    protected $table = 'video_media';

    protected $fillable = ['dir_path', 'file_name', 'file_type', 'file_size', 'language_id'];

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
