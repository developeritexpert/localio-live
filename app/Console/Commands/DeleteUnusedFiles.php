<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UnusedFilesDeletedNotification;
use Carbon\Carbon;

class DeleteUnusedFiles extends Command
{
    protected $signature = 'files:delete-trash';
    protected $description = 'Delete unused files that have been in the trash_files table for more than 30 days and send a notification to admin';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Calculate the cutoff date (30 days ago)
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Fetch files older than 30 days from the trash_files table
        $filesToDelete = DB::table('trash_files')
            ->where('soft_deleted_at', '<', $thirtyDaysAgo)
            ->get();

        if ($filesToDelete->isEmpty()) {
            $this->info('No files in trash_files table to delete that are older than 30 days.');
            return;
        }

        $deletedFiles = collect();
        $deletedCount = 0;

        foreach ($filesToDelete as $file) {
            // Ensure the relative path doesn't accidentally include a full path
            $relativePath = trim($file->dir_path, '/') . '/' . $file->file_name;

            // Generate full path correctly
            $filePath = public_path($relativePath);

            try {
                if (file_exists($filePath)) {
                    unlink($filePath);  // Delete the file
                    $this->info("Permanently deleted: $filePath");

                    $deletedFiles->push($relativePath);
                    $deletedCount++;
                } else {
                    $this->warn("File not found in storage: $filePath");
                }

                // Remove the record from trash_files table regardless of file existence
                DB::table('trash_files')->where('id', $file->id)->delete();

            } catch (\Exception $e) {
                $this->error("Error deleting file $filePath: " . $e->getMessage());
            }
        }

        if ($deletedCount > 0) {
            $this->info("$deletedCount unused files have been permanently deleted.");
        } else {
            $this->info('No files were deleted.');
        }
    }
}
