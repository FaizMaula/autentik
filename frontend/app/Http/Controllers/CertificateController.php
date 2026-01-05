<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Jobs\VerifyCertificateJob; // Job baru

class CertificateController extends Controller
{
    private function translateAiResponse($text)
    {
        if (!$text) return __('results.aiNotAvailable');

        $translations = [
            'Tidak ada hasil pencarian relevan.' => __('results.noSearchResults'),
            'Verifikasi AI tidak tersedia.' => __('results.aiNotAvailable'),
        ];

        if (strpos($text, 'Verifikasi AI sementara tidak tersedia karena batas kuota') !== false) {
            return __('results.aiQuotaExceeded');
        }

        return $translations[$text] ?? $text;
    }

    public function create()
    {
        $events = Event::orderBy('event_name', 'asc')
            ->get(['id', 'event_name', 'event_name_en', 'organizer', 'start_date', 'end_date']);
        return view('form', compact('events'));
    }

    public function store(Request $request)
    {
        $isInternal = $request->certificate_type === 'internal';

        $rules = [
            'nama' => 'required|string|max:255',
            'tahun_akademik' => 'nullable|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'required|date',
            'nama_kegiatan' => 'required|string|max:255',
            'nama_kegiatan_inggris' => 'nullable|string|max:255',
            'certificate_type' => 'required|in:internal,external',
        ];

        if ($isInternal) {
            $rules['nim'] = 'required|string|max:50';
        } else {
            $rules['berkas'] = 'required|mimes:jpg,jpeg,png,pdf|max:5120';
        }

        $validated = $request->validate($rules);

        if ($isInternal) {
            $verificationResult = $this->verifyInternalCertificate(
                $request->nim,
                $request->nama,
                $request->nama_kegiatan,
                $request->penyelenggara
            );
        
            // Dummy object (TIDAK disimpan ke DB)
            $certificate = new Certificate([
                'certificate_type' => 'internal',
                'nim' => $request->nim,
                'nama' => $request->nama,
                'tahun_akademik' => $request->tahun_akademik,
                'penyelenggara' => $request->penyelenggara,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'nama_kegiatan' => $request->nama_kegiatan,
                'nama_kegiatan_inggris' => $request->nama_kegiatan_inggris,
                'final_score' => $verificationResult['verified'] ? 100 : 0,
                'is_verified' => $verificationResult['verified'],
                'internal_verified' => $verificationResult['verified'],
                'internal_verification_notes' => $verificationResult['notes'],
            ]);
        
            return view('results', [
                'certificate' => $certificate,
                'certificate_type' => 'internal',
                'internal_verified' => $verificationResult['verified'],
                'internal_verification_notes' => $verificationResult['notes'],
                'internal_matched_event_name' => $verificationResult['matched_event_name'] ?? null,
                'internal_participant_data' => $verificationResult['participant_data'] ?? null,
                'match_scores' => [],
                'final_score' => $verificationResult['verified'] ? 100 : 0,
                'verifikasi_ai' => null,
                'ocr_text' => '',
                'google_results' => [],
                'font_results' => [],
                'ocr_details' => [],
            ]);
        }

        // EXTERNAL CERTIFICATE - DISPATCH JOB
        $file = $request->file('berkas');
        $path = $file->store('certificates', 'r2');

        $certificate = Certificate::create([
            'user_id' => Auth::id(),
            'certificate_type' => 'external',
            'nim' => null,
            'internal_verified' => false,
            'internal_verification_notes' => null,
            'nama' => $request->nama,
            'tahun_akademik' => $request->tahun_akademik,
            'penyelenggara' => $request->penyelenggara,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'nama_kegiatan' => $request->nama_kegiatan,
            'nama_kegiatan_inggris' => $request->nama_kegiatan_inggris,
            'berkas' => $path,
            'final_score' => 0,
            'is_verified' => false,
        ]);

        VerifyCertificateJob::dispatch($certificate->id);

        return redirect()
            ->route('certificate.history')
            ->with('status', 'Certificate uploaded. Verification is in progress.');
    }

    public function history()
    {
        $histories = Certificate::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('history', compact('histories'));
    }

    public function showResult($id)
    {
        $certificate = Certificate::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $ocr = $certificate->ocrResults()->latest()->first();
        $analysis = $certificate->analysisResults()->latest()->first();
        
        // Ambil data ocr_details asli
        $ocrDetails = $ocr->ocr_details ?? [];

        // --- LOGIKA VALIDASI FONT (OPTIMIZED) ---
        // 1. Cari font dari sertifikat lain dengan nama kegiatan yang sama
        $existingFonts = [];
        $otherCertificates = Certificate::where('nama_kegiatan', $certificate->nama_kegiatan)
            ->where('id', '!=', $certificate->id) // Jangan bandingkan dengan diri sendiri
            ->whereHas('ocrResults')
            ->with('ocrResults')
            ->get();

        foreach ($otherCertificates as $otherCert) {
            foreach ($otherCert->ocrResults as $otherOcr) {
                $otherDetails = $otherOcr->ocr_details ?? [];
                foreach ($otherDetails as $detail) {
                    if (!empty($detail['font']['class'])) {
                        $existingFonts[] = $detail['font']['class'];
                    }
                }
            }
        }
        $existingFonts = array_unique($existingFonts); // Hilangkan duplikasi

        // 2. Lakukan perbandingan font pada sertifikat saat ini
        foreach ($ocrDetails as &$item) {
            if (!empty($item['font']['class'])) {
                $detectedFont = $item['font']['class'];

                if (!empty($existingFonts)) {
                    if (in_array($detectedFont, $existingFonts)) {
                        $item['font']['status'] = 'match';
                    } else {
                        $item['font']['status'] = 'mismatch';
                    }
                    // Tambahkan referensi font yang ditemukan di database
                    $item['font']['reference_font'] = implode(', ', $existingFonts); 
                } else {
                    // Jika tidak ada sertifikat lain untuk kegiatan ini di DB
                    $item['font']['status'] = 'new';
                }
            }
        }
        // --- END LOGIKA VALIDASI FONT ---

        return view('results', [
            'certificate' => $certificate,
            'certificate_type' => $certificate->certificate_type ?? 'external',
            'match_scores' => $analysis->match_scores ?? [],
            'final_score' => $certificate->final_score,
            'verifikasi_ai' => $this->translateAiResponse($analysis->verifikasi_ai ?? null),
            'ocr_text' => $ocr->ocr_text ?? '',
            'google_results' => $analysis->google_results ?? [],
            'font_results' => $analysis->font_results ?? [],
            'ocr_details' => $ocrDetails, // Kirim ocrDetails yang sudah diperbarui statusnya
            'internal_verified' => $certificate->internal_verified ?? false,
            'internal_verification_notes' => $certificate->internal_verification_notes ?? null,
            'internal_matched_event_name' => $certificate->nama_kegiatan ?? null,
            'internal_participant_data' => null,
        ]);
    }

    private function verifyInternalCertificate(string $nim, string $nama, string $eventName, string $organizer): array
    {
        $notes = [];
        $verified = false;
        $eventNotFound = false;
        $matchedEventName = null;
        $participantData = null;

        // Search for events matching the event name (exact match for dropdown selection)
        $event = Event::where('event_name', $eventName)->first();

        if (!$event) {
            $notes[] = __('results.internalEventNotFound');
            return [
                'verified' => false, 
                'notes' => implode("\n", $notes),
                'event_not_found' => true,
                'matched_event_name' => null,
                'participant_data' => null
            ];
        }

        $matchedEventName = $event->event_name;
        $notes[] = __('results.internalEventFound', ['event' => $event->event_name]);

        // Check if participant with NIM exists in this event
        $participant = $event->participants()
            ->where('nim', $nim)
            ->first();

        if ($participant) {
            $notes[] = __('results.internalNimFound', ['nim' => $nim]);
            
            // Store participant data for display
            $participantData = [
                'name' => $participant->participant_name,
                'nim' => $participant->nim,
                'email' => $participant->email,
                'faculty' => $participant->faculty,
                'study_program' => $participant->study_program,
                'attendance_status' => $participant->attendance_status,
                'event_name' => $event->event_name,
                'event_organizer' => $event->organizer,
                'event_start_date' => $event->start_date,
                'event_end_date' => $event->end_date,
            ];

            // Check if name matches (fuzzy)
            $nameSimilarity = similar_text(
                strtolower($nama),
                strtolower($participant->participant_name),
                $percent
            );

            if ($percent >= 80) {
                $notes[] = __('results.internalNameMatch', ['percent' => round($percent)]);
                $verified = true;
            } else {
                $notes[] = __('results.internalNameMismatch', [
                    'expected' => $participant->participant_name,
                    'percent' => round($percent)
                ]);
            }

            // Check organizer match
            if (stripos($event->organizer, $organizer) !== false || 
                stripos($organizer, $event->organizer) !== false) {
                $notes[] = __('results.internalOrganizerMatch');
            } else {
                $notes[] = __('results.internalOrganizerMismatch', ['expected' => $event->organizer]);
            }
        } else {
            $notes[] = __('results.internalNimNotFound', ['nim' => $nim]);
        }

        return [
            'verified' => $verified,
            'notes' => implode("\n", $notes),
            'event_not_found' => false,
            'matched_event_name' => $matchedEventName,
            'participant_data' => $participantData
        ];
    }
}
