<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uploaded_by',
        'event_name',
        'event_name_en',
        'organizer',
        'event_date',
        'start_date',
        'end_date',
        'academic_year',
        'description',
        'original_filename',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the admin who uploaded this event.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the participants for this event.
     */
    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    /**
     * Check if a participant with given NIM exists in this event.
     */
    public function hasParticipant(string $nim): bool
    {
        return $this->participants()->where('nim', $nim)->exists();
    }

    /**
     * Get participant by NIM.
     */
    public function getParticipant(string $nim)
    {
        return $this->participants()->where('nim', $nim)->first();
    }
}
