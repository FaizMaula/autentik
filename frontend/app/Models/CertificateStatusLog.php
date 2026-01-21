<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'admin_id',
        'old_status',
        'new_status',
        'old_score',
        'new_score',
        'notes',
    ];

    protected $casts = [
        'old_score' => 'float',
        'new_score' => 'float',
    ];

    /**
     * Get the certificate that this log belongs to.
     */
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * Get the admin who made this status change.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get human-readable old status label.
     */
    public function getOldStatusLabelAttribute(): string
    {
        return match ($this->old_status) {
            'verified' => __('results.verified'),
            'suspicious' => __('results.suspicious'),
            'not_verified' => __('results.notVerified'),
            default => $this->old_status,
        };
    }

    /**
     * Get human-readable new status label.
     */
    public function getNewStatusLabelAttribute(): string
    {
        return match ($this->new_status) {
            'verified' => __('results.verified'),
            'not_verified' => __('results.notVerified'),
            default => $this->new_status,
        };
    }
}
