<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    // public function uploadMedia(UploadedFile $file, string $directory = 'public', $access="public"): Media
    // {

    //     if (!$file->isValid()) {
    //         throw new \Exception('Invalid file upload.');
    //     }

    //     $directoryPath = public_path($directory);

    //     if (!file_exists($directoryPath)) {
    //         mkdir($directoryPath, 0775, true);
    //     }

    //     $fileName = time() . '_' . $file->getClientOriginalName();
    //     $file->move($directoryPath, $fileName);

    //     $mimeType = $file->getClientMimeType();
    //     if ($mimeType === 'application/octet-stream') {
    //         $finfo = new \finfo(FILEINFO_MIME_TYPE);
    //         $mimeType = $finfo->file($directoryPath . '/' . $fileName);
    //     }

    //     $fileSize = file_exists($file->getPathname()) ? $file->getSize() : 0;

    //     return Media::create([
    //         'dir_path' => $directory,
    //         'file_name' => $fileName,
    //         'file_type' => $mimeType,
    //         'file_size' => $fileSize,
    //     ]);
    // }

    public function uploadMedia(UploadedFile $file, string $directory = "tickets", bool $public = true)
{
    if (!$file->isValid()) {
        throw new \Exception('Invalid file upload.');
    }

    $fileName = time() . '_' . Str::random(6) . '_' . $file->getClientOriginalName();
    $destinationPath = public_path($directory);

    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0775, true);
    }

    // Copy temp Livewire file manually to public directory
    $filePath = $file->getRealPath(); // full path in livewire-tmp
    $targetPath = $destinationPath . '/' . $fileName;

    if (!@copy($filePath, $targetPath)) {
        throw new \Exception('Failed to copy file to public directory.');
    }

    return Media::create([
        'dir_path' => $directory,
        'file_name' => $fileName,
        'file_type' => $file->getClientMimeType(),
        'file_path' => $directory . '/' . $fileName,
        'file_size' => filesize($targetPath),
        'status' => 1,
    ]);
}


    public function getMediaById(int $id): ?Media
    {
        return Media::find($id);
    }

    public function deleteMedia(int $id): bool
    {
        $media = Media::find($id);
        if ($media) {
            Storage::disk('public')->delete($media->dir_path . '/' . $media->file_name);
            return $media->delete();
        }
        return false;
    }

    public function getMediaUrl(Media $media): string
    {
        return Storage::disk('public')->url($media->dir_path . '/' . $media->file_name);
    }
    public function deleteMediaByPath($filePath)
    {
        if (\Storage::exists($filePath)) {
            \Storage::delete($filePath);
            return true;
        }
        return false;
    }
}


?>
