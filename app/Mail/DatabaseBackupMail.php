<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $zipPath;
    public string $dbName;
    public string $timestamp;
    public float  $fileSize;

    public function __construct(string $zipPath, string $dbName, string $timestamp, float $fileSize)
    {
        $this->zipPath  = $zipPath;
        $this->dbName   = $dbName;
        $this->timestamp = $timestamp;
        $this->fileSize = $fileSize;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Database Backup — {$this->dbName} — {$this->timestamp}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.database-backup',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->zipPath)
                ->as("{$this->dbName}_{$this->timestamp}.zip")
                ->withMime('application/zip'),
        ];
    }
}
