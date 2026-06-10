<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;


class AssetManager
{

    protected $basePaths = [];
    protected $publicUrl = '/storage';
    protected $placeholders = [
        'jpg' => '/images/placeholder.jpg',
        'png' => '/images/placeholder.png',
        'jpeg' => '/images/placeholder.jpg',
        'webp' => '/images/placeholder.png',
        'svg' => '/images/placeholder.png',
        'pdf' => '/images/placeholder-pdf.png',
        'doc' => '/images/placeholder-doc.png',
        'mp4' => '/images/placeholder-video.png',
        'default' => '/images/placeholder-file.png',
    ];

    public function __construct()
    {
        $this->basePaths = [
            public_path(),                          // /public
            storage_path('app/public'),             // /storage/app/public
            public_path('storage'),                 // /public/storage (symlinked)
        ];
    }

    public function get($relativePath, $default = null)
    {
        $relativePath = ltrim($relativePath, '/');
        
        foreach ($this->basePaths as $base) {
            $fullPath = "{$base}/{$relativePath}";
            if (file_exists($fullPath)) {
                // Determine if it's from public or storage
                if (str_contains($base, 'storage/app')) {
                    return "/storage/{$relativePath}";
                }
                return "/{$relativePath}";
            }
        }

        return $default ?: $this->getPlaceholder($relativePath);
    }

    protected function getPlaceholder($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return $this->placeholders[$ext] ?? $this->placeholders['default'];
    }
    
}