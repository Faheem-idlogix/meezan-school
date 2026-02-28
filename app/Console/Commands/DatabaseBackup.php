<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\DatabaseBackupMail;
use App\Models\ActivityLog;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup
                            {--email= : Email address to send backup to}
                            {--keep=7 : Number of days to keep old backups}';

    protected $description = 'Create a zip backup of the database and optionally email it';

    public function handle(): int
    {
        $this->info('Starting database backup...');

        $dbName     = config('database.connections.mysql.database');
        $dbUser     = config('database.connections.mysql.username');
        $dbPass     = config('database.connections.mysql.password');
        $dbHost     = config('database.connections.mysql.host');
        $dbPort     = config('database.connections.mysql.port', 3306);

        $timestamp  = now()->format('Y-m-d_H-i-s');
        $backupDir  = storage_path('app/backups');
        $sqlFile    = "{$backupDir}/{$dbName}_{$timestamp}.sql";
        $zipFile    = "{$backupDir}/{$dbName}_{$timestamp}.zip";

        // Ensure backup directory exists
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Find mysqldump binary
        $mysqldump = $this->findMysqldump();
        if (!$mysqldump) {
            $this->error('mysqldump not found. Please install MySQL tools or add to PATH.');
            return self::FAILURE;
        }

        // Build mysqldump command
        $passFlag = $dbPass ? "-p\"{$dbPass}\"" : '';
        $command  = sprintf(
            '"%s" --host="%s" --port=%s --user="%s" %s "%s" > "%s" 2>&1',
            $mysqldump, $dbHost, $dbPort, $dbUser, $passFlag, $dbName, $sqlFile
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $error = implode("\n", $output);
            $this->error("mysqldump failed: {$error}");
            return self::FAILURE;
        }

        $this->info("SQL dump created: {$sqlFile}");

        // Create zip
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($sqlFile, basename($sqlFile));
            $zip->close();
            unlink($sqlFile); // Remove raw SQL, keep only zip
            $this->info("Zip created: {$zipFile}");
        } else {
            $this->error('Failed to create zip file.');
            return self::FAILURE;
        }

        // Get file size
        $fileSize = round(filesize($zipFile) / 1024, 2);
        $this->info("Backup size: {$fileSize} KB");

        // Email the backup
        $email = $this->option('email') ?: config('mail.backup_recipient', config('mail.from.address'));
        if ($email && $email !== 'null') {
            try {
                Mail::to($email)->send(new DatabaseBackupMail($zipFile, $dbName, $timestamp, $fileSize));
                $this->info("Backup emailed to: {$email}");
            } catch (\Exception $e) {
                $this->warn("Email failed: {$e->getMessage()}");
                $this->warn("Backup still saved locally at: {$zipFile}");
            }
        }

        // Clean up old backups
        $keepDays = (int) $this->option('keep');
        $this->cleanOldBackups($backupDir, $keepDays);

        // Log the activity
        try {
            ActivityLog::log('backup', "Database backup created: {$dbName}_{$timestamp}.zip ({$fileSize} KB)");
        } catch (\Exception $e) {
            // Don't fail if logging fails
        }

        $this->info('Database backup completed successfully!');
        return self::SUCCESS;
    }

    private function cleanOldBackups(string $dir, int $keepDays): void
    {
        $cutoff = now()->subDays($keepDays)->timestamp;
        $files  = glob("{$dir}/*.zip");

        $deleted = 0;
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} old backup(s) older than {$keepDays} days.");
        }
    }

    private function findMysqldump(): ?string
    {
        // Check if it's in PATH
        $which = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump 2>nul' : 'which mysqldump 2>/dev/null';
        exec($which, $output, $exitCode);
        if ($exitCode === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        // Common Windows locations
        $paths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MariaDB 10.11\\bin\\mysqldump.exe',
        ];

        // Also search laragon wildcard
        foreach (glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe') as $p) {
            $paths[] = $p;
        }
        foreach (glob('C:\\wamp64\\bin\\mysql\\*\\bin\\mysqldump.exe') as $p) {
            $paths[] = $p;
        }

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
