<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class StudentDocument extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'expiry_date'  => 'date',
        'verified_at'  => 'date',
        'is_verified'  => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ── Scopes ──

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    // ── Helpers ──

    public static function documentTypes(): array
    {
        return [
            'b_form'              => 'B-Form / CNIC',
            'birth_certificate'   => 'Birth Certificate',
            'leaving_certificate' => 'Leaving Certificate',
            'character_certificate' => 'Character Certificate',
            'photo'               => 'Passport Photo',
            'medical_report'      => 'Medical Report',
            'previous_result'     => 'Previous Result Card',
            'father_cnic'         => 'Father CNIC',
            'mother_cnic'         => 'Mother CNIC',
            'guardian_cnic'       => 'Guardian CNIC',
            'other'               => 'Other',
        ];
    }
}
