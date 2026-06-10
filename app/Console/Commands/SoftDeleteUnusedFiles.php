<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SoftDeleteUnusedFiles extends Command
{
    protected $signature = 'files:soft-delete';
    protected $description = 'Soft delete unused files by checking references in product_media and categories tables and move them to trash_files';

    public function handle()
    {
        $this->info("Starting the unused file cleanup process...");

        // Get all files stored in the media table
        $allMediaFiles = DB::table('media')->get();
        $usedMediaIds = DB::table('product_media')->pluck('media_id')->toArray();

        // Get paths from categories table (image and category_icon columns)
        $categoryPaths = DB::table('categories')
            ->select('image', 'category_icon')
            ->get()
            ->flatMap(function ($record) {
                return [$record->image, $record->category_icon];
            })->filter()->toArray(); // Filter out null values

        $softDeletedCount = 0;

        foreach ($allMediaFiles as $file) {
            $filePath = $file->dir_path . '/' . $file->file_name;

            // Check if the file is used in product_media or categories table
            if (in_array($file->id, $usedMediaIds) || in_array($filePath, $categoryPaths)) {
                $this->info("File in use, skipping: $filePath");
           
                continue;
            }

            // If file is unused, soft-delete it by moving to trash_files
            try {
                DB::beginTransaction();

                // Insert the unused file into trash_files table
                DB::table('trash_files')->insert([
                    'dir_path' => $file->dir_path,
                    'file_name' => $file->file_name,
                    'file_type' => $file->file_type,
                    'file_size' => $file->file_size,
                    'soft_deleted_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Delete the file record from the media table
                DB::table('media')->where('id', $file->id)->delete();

                DB::commit();

                $this->info("Soft deleted and moved to trash_files: $filePath");
                $softDeletedCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error soft deleting file $filePath: " . $e->getMessage());
            }
        }

        // Output final summary
        if ($softDeletedCount > 0) {
            $this->info("$softDeletedCount unused files have been soft deleted, moved to trash_files, and removed from the media table.");
        } else {
            $this->info('No unused files were found. No files were soft deleted.');
        }
    }
}
