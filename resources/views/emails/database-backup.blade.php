<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4154f1; color: #fff; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td { padding: 8px 12px; border-bottom: 1px solid #dee2e6; }
        td:first-child { font-weight: bold; color: #555; width: 140px; }
        .badge { display: inline-block; padding: 4px 12px; background: #198754; color: #fff; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">Database Backup</h2>
        <p style="margin:5px 0 0;opacity:.8">{{ setting('school_name', 'School') }} Management System</p>
    </div>
    <div class="content">
        <p>A scheduled database backup has been created and is attached to this email.</p>

        <table>
            <tr><td>Database</td><td>{{ $dbName }}</td></tr>
            <tr><td>Timestamp</td><td>{{ $timestamp }}</td></tr>
            <tr><td>File Size</td><td>{{ $fileSize }} KB</td></tr>
            <tr><td>Status</td><td><span class="badge">Success</span></td></tr>
            <tr><td>Server</td><td>{{ config('app.url') }}</td></tr>
        </table>

        <p><strong>Note:</strong> This backup is automatically generated. Please store it in a safe location. Old backups are automatically cleaned up after 7 days.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ setting('school_name', 'School') }} &mdash; Automated Backup System
    </div>
</body>
</html>
