<?php

namespace App\Http\Controllers;

use App\Models\DatabaseBackup;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Display a listing of database backups.
     */
    public function index()
    {
        $backups = DatabaseBackup::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.backups.index', compact('backups'));
    }

    /**
     * Generate a new database backup.
     */
    public function generate()
    {
        try {
            $dbPath = database_path('database.sqlite');
            if (!file_exists($dbPath)) {
                return back()->with('error', 'Source database file not found.');
            }

            // Create target directory if it doesn't exist
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $fileName = 'backup-' . date('Y-m-d-H-i-s') . '.sqlite';
            $filePath = 'backups/' . $fileName;
            $fullPath = storage_path('app/' . $filePath);

            if (copy($dbPath, $fullPath)) {
                $fileSizeVal = filesize($fullPath);
                $fileSize = $this->formatBytes($fileSizeVal);

                $user = Auth::user();

                $backup = DatabaseBackup::create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'user_id' => $user ? $user->id : null,
                    'user_name' => $user ? $user->name : 'System/Guest',
                ]);

                // Track activity
                AuditLog::log('Backup System', 'Backup Generate', "Generated database backup: {$fileName}");

                return back()->with('success', 'Database backup generated successfully.');
            }

            return back()->with('error', 'Failed to copy database file.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while generating backup: ' . $e->getMessage());
        }
    }

    /**
     * Download the backup file.
     */
    public function download($id)
    {
        $backup = DatabaseBackup::findOrFail($id);
        $fullPath = storage_path('app/' . $backup->file_path);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'Backup file does not exist on disk.');
        }

        // Track activity
        AuditLog::log('Backup System', 'Download Action', "Downloaded database backup: {$backup->file_name}");

        return response()->download($fullPath, $backup->file_name);
    }

    /**
     * Remove the backup file and record.
     */
    public function destroy($id)
    {
        try {
            $backup = DatabaseBackup::findOrFail($id);
            $fullPath = storage_path('app/' . $backup->file_path);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $fileName = $backup->file_name;
            $backup->delete();

            // Track activity
            AuditLog::log('Backup System', 'Delete Record', "Deleted database backup: {$fileName}");

            return back()->with('success', 'Backup deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting backup: ' . $e->getMessage());
        }
    }

    /**
     * Helper to format file sizes.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
