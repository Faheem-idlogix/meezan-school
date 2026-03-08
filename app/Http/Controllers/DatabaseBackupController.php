<?php

namespace App\Http\Controllers;

use App\Mail\DatabaseBackupMail;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
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
        $dbUser    = config('database.connections.mysql.username');
        $dbPass    = config('database.connections.mysql.password');
        $dbHost    = config('database.connections.mysql.host');
        $dbPort    = config('database.connections.mysql.port', 3306);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = storage_path('app/backups');
        $sqlFile   = "{$backupDir}/{$dbName}_{$timestamp}.sql";
        $zipFile   = "{$backupDir}/{$dbName}_{$timestamp}.zip";

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Find mysqldump
        $mysqldump = $this->findMysqldump();
        if (!$mysqldump) {
            return redirect()->route('backup.index')
                ->with('error', 'mysqldump not found. Make sure MySQL tools are installed (XAMPP/Laragon/WAMP).');
        }

        // Run mysqldump
        $passFlag = $dbPass ? "-p\"{$dbPass}\"" : '';
        $command  = sprintf(
            '"%s" --host="%s" --port=%s --user="%s" %s "%s" > "%s" 2>&1',
            $mysqldump, $dbHost, $dbPort, $dbUser, $passFlag, $dbName, $sqlFile
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            return redirect()->route('backup.index')
                ->with('error', 'mysqldump failed: ' . implode(' ', $output));
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

    private function findMysqldump(): ?string
    {
        $which = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump 2>nul' : 'which mysqldump 2>/dev/null';
        exec($which, $output, $exitCode);
        if ($exitCode === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        $paths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MariaDB 10.11\\bin\\mysqldump.exe',
        ];

        foreach (glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe') as $p) {
            $paths[] = $p;
        }
        foreach (glob('C:\\wamp64\\bin\\mysql\\*\\bin\\mysqldump.exe') as $p) {
            $paths[] = $p;
        }

        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }

        return null;
    }
}
