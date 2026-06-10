<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PageTileTranslation extends Model
{
    use HasFactory;
    protected $table = 'page_tiles_translations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'page_tile_id',
        'title',
        'description',
        'image',
        'status',
        'img',
        'small_img',
    ];
    public function pageTile()
    {
        return $this->belongsTo(PageTile::class, 'page_tile_id', 'id');
    }
}
