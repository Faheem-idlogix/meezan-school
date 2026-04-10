<?php

namespace App\Http\Controllers;

use App\Mail\DatabaseBackupMail;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DatabaseBackupController extends Controller
{
    // ── Main Backup Page ─────────────────────────────────────────────
    public function index()
    {
        $backupDir = storage_path('app/backups');
        $backups   = [];

        if (is_dir($backupDir)) {
            $files = glob("{$backupDir}/*.zip");
            rsort($files); // newest first

            foreach ($files as $file) {
                $backups[] = [
                    'filename'   => basename($file),
                    'size'       => round(filesize($file) / 1024, 2),
                    'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                    'age'        => \Carbon\Carbon::createFromTimestamp(filemtime($file))->diffForHumans(),
                ];
            }
        }

        // Mail settings from DB
        $settings = Setting::all()->keyBy('key');

        return view('admin.pages.database_backup.index', compact('backups', 'settings'));
    }

    // ── Create New Backup ────────────────────────────────────────────
    public function create()
    {
        $dbName    = config('database.connections.mysql.database');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = storage_path('app/backups');
        $sqlFile   = "{$backupDir}/{$dbName}_{$timestamp}.sql";
        $zipFile   = "{$backupDir}/{$dbName}_{$timestamp}.zip";

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        try {
            $this->dumpDatabaseToFile($sqlFile);
        } catch (\Exception $e) {
            @unlink($sqlFile);
            return redirect()->route('backup.index')
                ->with('error', 'Database dump failed: ' . $e->getMessage());
        }

        // Create zip
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($sqlFile, basename($sqlFile));
            $zip->close();
            @unlink($sqlFile);
        } else {
            @unlink($sqlFile);
            return redirect()->route('backup.index')
                ->with('error', 'Failed to create zip file.');
        }

        $fileSize = round(filesize($zipFile) / 1024, 2);

        try {
            ActivityLog::log('backup', "Database backup created: {$dbName}_{$timestamp}.zip ({$fileSize} KB)");
        } catch (\Exception $e) {
            // Don't fail if logging fails
        }

        return redirect()->route('backup.index')
            ->with('success', "Backup created successfully — {$dbName}_{$timestamp}.zip ({$fileSize} KB)");
    }

    // ── Download Backup ──────────────────────────────────────────────
    public function download(string $filename)
    {
        $filename = basename($filename); // prevent directory traversal
        $path     = storage_path("app/backups/{$filename}");

        if (!file_exists($path) || pathinfo($filename, PATHINFO_EXTENSION) !== 'zip') {
            return redirect()->route('backup.index')->with('error', 'Backup file not found.');
        }

        return response()->download($path);
    }

    // ── Email Backup ─────────────────────────────────────────────────
    public function email(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'email'    => 'required|email',
        ]);

        $filename = basename($request->filename);
        $path     = storage_path("app/backups/{$filename}");

        if (!file_exists($path) || pathinfo($filename, PATHINFO_EXTENSION) !== 'zip') {
            return redirect()->route('backup.index')->with('error', 'Backup file not found.');
        }

        // Apply dynamic SMTP from database settings (if configured)
        $this->applyDynamicSmtp();

        $dbName    = config('database.connections.mysql.database');
        $timestamp = str_replace([$dbName . '_', '.zip'], '', $filename);
        $fileSize  = round(filesize($path) / 1024, 2);

        try {
            Mail::to($request->email)->send(new DatabaseBackupMail($path, $dbName, $timestamp, $fileSize));

            return redirect()->route('backup.index')
                ->with('success', "Backup emailed successfully to {$request->email}");
        } catch (\Exception $e) {
            return redirect()->route('backup.index')
                ->with('error', 'Email failed: ' . $e->getMessage());
        }
    }

    // ── Delete Backup ────────────────────────────────────────────────
    public function destroy(string $filename)
    {
        $filename = basename($filename);
        $path     = storage_path("app/backups/{$filename}");

        if (file_exists($path) && pathinfo($filename, PATHINFO_EXTENSION) === 'zip') {
            @unlink($path);
            return redirect()->route('backup.index')->with('success', "Backup {$filename} deleted.");
        }

        return redirect()->route('backup.index')->with('error', 'Backup file not found.');
    }

    // ── Save Mail/SMTP Settings ──────────────────────────────────────
    public function saveMailSettings(Request $request)
    {
        $request->validate([
            'mail_host'       => 'required|string|max:255',
            'mail_port'       => 'required|integer|min:1|max:65535',
            'mail_username'   => 'required|string|max:255',
            'mail_password'   => 'required|string|max:255',
            'mail_encryption' => 'required|in:tls,ssl,none',
            'mail_from'       => 'required|email|max:255',
            'mail_from_name'  => 'nullable|string|max:255',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::set($key, $value);
        }

        flush_settings_cache();

        return redirect()->route('backup.index')
            ->with('success', 'Mail/SMTP settings saved successfully. Emails will now use these settings.');
    }

    // ── Test SMTP Connection ─────────────────────────────────────────
    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $this->applyDynamicSmtp();

        try {
            Mail::raw(
                "This is a test email from " . setting('school_name', 'Meezan School') . " backup system.\n\nIf you received this, your SMTP settings are working correctly!\n\nTime: " . now()->format('d M Y, h:i A'),
                function ($message) use ($request) {
                    $message->to($request->test_email)
                            ->subject('SMTP Test — ' . setting('school_name', 'Meezan School'));
                }
            );

            return redirect()->route('backup.index')
                ->with('success', "Test email sent to {$request->test_email}. Check your inbox!");
        } catch (\Exception $e) {
            return redirect()->route('backup.index')
                ->with('error', 'SMTP test failed: ' . $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════════

    private function applyDynamicSmtp(): void
    {
        $host = Setting::get('mail_host');
        if (!$host) return; // Use default .env config

        config([
            'mail.mailers.smtp.host'       => $host,
            'mail.mailers.smtp.port'       => (int) Setting::get('mail_port', 587),
            'mail.mailers.smtp.username'   => Setting::get('mail_username'),
            'mail.mailers.smtp.password'   => Setting::get('mail_password'),
            'mail.mailers.smtp.encryption' => Setting::get('mail_encryption', 'tls') === 'none' ? null : Setting::get('mail_encryption', 'tls'),
            'mail.from.address'            => Setting::get('mail_from', config('mail.from.address')),
            'mail.from.name'               => Setting::get('mail_from_name', config('mail.from.name')),
        ]);
    }

    private function dumpDatabaseToFile(string $filePath): void
    {
        $pdo = \DB::connection()->getPdo();
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
        $dbName = config('database.connections.mysql.database');

        $handle = fopen($filePath, 'w');
        if (!$handle) {
            throw new \RuntimeException('Cannot create backup file.');
        }

        fwrite($handle, "-- Database Backup: {$dbName}\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- --------------------------------------------------------\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // DROP + CREATE
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createStmt['Create Table'] . ";\n\n");

            // DATA — fetch in chunks to avoid memory issues
            $rowCount = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            $chunkSize = 500;

            for ($offset = 0; $offset < $rowCount; $offset += $chunkSize) {
                $rows = $pdo->query("SELECT * FROM `{$table}` LIMIT {$chunkSize} OFFSET {$offset}")
                            ->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $values = array_map(function ($value) use ($pdo) {
                        if ($value === null) return 'NULL';
                        return $pdo->quote($value);
                    }, $row);

                    $columns = implode('`, `', array_keys($row));
                    fwrite($handle, "INSERT INTO `{$table}` (`{$columns}`) VALUES (" . implode(', ', $values) . ");\n");
                }
            }

            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }
}
