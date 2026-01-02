<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'nim',
        'participant_name',
        'email',
        'faculty',
        'study_program',
        'attendance_status',
    ];

    /**
     * Get the event that this participant belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Find participant by NIM across all events.
     */
    public static function findByNim(string $nim)
    {
        return static::where('nim', $nim)->get();
    }

    /**
     * Find participant by NIM in a specific event.
     */
    public static function findByNimAndEvent(string $nim, int $eventId)
    {
        return static::where('nim', $nim)
            ->where('event_id', $eventId)
            ->first();
    }

    /**
     * Search for participant by NIM and event name (fuzzy match).
     */
    public static function verifyParticipation(string $nim, string $eventName)
    {
        return static::where('nim', $nim)
            ->whereHas('event', function ($query) use ($eventName) {
                $query->where('event_name', 'LIKE', '%' . $eventName . '%')
                    ->orWhere('event_name_en', 'LIKE', '%' . $eventName . '%');
            })
            ->with('event')
            ->get();
    }
}
