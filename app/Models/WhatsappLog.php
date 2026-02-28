<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class WhatsappLog extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'school_id', 'to', 'recipient_name', 'recipient_type',
        'recipient_id', 'message_type', 'message', 'status',
        'api_response', 'provider',
    ];

    public function scopeSent($q)   { return $q->where('status', 'sent'); }
    public function scopeFailed($q) { return $q->where('status', 'failed'); }
}
