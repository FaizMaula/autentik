<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Certificate;
use App\Models\CertificateStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class EventController extends Controller
{
    /**
     * Display a listing of events uploaded by admin.
     */
    public function index()
    {
        $events = Event::with(['participants', 'uploadedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for uploading a new event Excel file.
     */
    public function create()
    {
        return view('admin.events.upload');
    }

    /**
     * Store a newly created event from form + Excel upload.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_name_en' => 'nullable|string|max:255',
            'organizer' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'duration_type' => 'required|in:single,multi',
            'event_date' => 'required_if:duration_type,single|nullable|date',
            'start_date' => 'required_if:duration_type,multi|nullable|date',
            'end_date' => 'required_if:duration_type,multi|nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:1000',
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            // Create event from form data
            $event = Event::create([
                'uploaded_by' => Auth::id(),
                'event_name' => $request->event_name,
                'event_name_en' => $request->event_name_en,
                'organizer' => $request->organizer,
                'event_date' => $request->duration_type === 'single' ? $request->event_date : null,
                'start_date' => $request->duration_type === 'multi' ? $request->start_date : null,
                'end_date' => $request->duration_type === 'multi' ? $request->end_date : null,
                'academic_year' => $request->academic_year,
                'description' => $request->description,
                'original_filename' => $request->file('excel_file')->getClientOriginalName(),
            ]);

            // Parse Excel file for participants only
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // First row is header
            $headerRow = array_shift($rows);

            if (!$headerRow) {
                throw new \Exception(__('admin.excelNoHeader'));
            }

            // Map headers to column indices
            $columnMap = $this->mapParticipantColumns($headerRow);

            $participantsData = [];
            foreach ($rows as $row) {
                // Skip empty rows
                if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                    continue;
                }

                $nim = $this->getColumnValue($row, $columnMap, 'nim');
                $name = $this->getColumnValue($row, $columnMap, 'name');

                // Skip if no NIM or name
                if (empty($nim) && empty($name)) {
                    continue;
                }

                $participantsData[] = [
                    'event_id' => $event->id,
                    'nim' => $nim ?? '',
                    'participant_name' => $name ?? '',
                    'email' => null,
                    'faculty' => $this->getColumnValue($row, $columnMap, 'faculty'),
                    'study_program' => $this->getColumnValue($row, $columnMap, 'study_program'),
                    'attendance_status' => 'present',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert participants
            if (!empty($participantsData)) {
                EventParticipant::insert($participantsData);
            }

            DB::commit();

            return redirect()->route('admin.events.index')
                ->with('success', __('admin.uploadSuccess', ['count' => count($participantsData)]));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', __('admin.uploadError') . ': ' . $e->getMessage());
        }
    }

    /**
     * Map participant column headers to indices (simplified template).
     */
    private function mapParticipantColumns(array $headerRow): array
    {
        $map = [];
        $aliases = [
            'no' => ['no', 'nomor', 'number', '#'],
            'nim' => ['nim', 'noinduk', 'nomorinduk', 'studentid', 'idmahasiswa'],
            'name' => ['nama', 'namapeserta', 'participantname', 'namamahasiswa', 'name'],
            'faculty' => ['fakultas', 'faculty'],
            'study_program' => ['jurusan', 'prodi', 'programstudi', 'studyprogram', 'major'],
        ];

        foreach ($headerRow as $index => $header) {
            if (!$header) continue;

            // Fix: lowercase first, then remove non-alphanumeric characters
            $normalizedHeader = preg_replace('/[^a-z0-9]/', '', strtolower(trim($header)));

            foreach ($aliases as $key => $possibleNames) {
                foreach ($possibleNames as $alias) {
                    $normalizedAlias = preg_replace('/[^a-z0-9]/', '', strtolower($alias));

                    if ($normalizedHeader === $normalizedAlias) {
                        $map[$key] = $index;
                        break 2;
                    }
                }
            }
        }
        return $map;
    }

    /**
     * Display the specified event with its participants.
     */
    public function show(Event $event)
    {
        $event->load('participants', 'uploadedBy');
        return view('admin.events.show', compact('event'));
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')
            ->with('success', __('admin.deleteSuccess'));
    }

    /**
     * Display all verification history (admin only).
     */
    public function allHistory()
    {
        $certificates = Certificate::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.history.index', compact('certificates'));
    }

    /**
     * Show certificate result for admin (can view any user's certificate).
     */
    public function showCertificateResult($id)
    {
        $certificate = Certificate::with('user')->findOrFail($id);

        $ocr = $certificate->ocrResults()->latest()->first();
        $analysis = $certificate->analysisResults()->latest()->first();

        // Fetch participant data for internal certificates
        $participantData = null;
        if ($certificate->certificate_type === 'internal' && $certificate->nim) {
            // Try exact match first, then fuzzy match
            $event = Event::where('event_name', $certificate->nama_kegiatan)->first();
            
            // If not found, try case-insensitive LIKE search
            if (!$event) {
                $event = Event::whereRaw('LOWER(event_name) = ?', [strtolower($certificate->nama_kegiatan)])->first();
            }
            
            // If still not found, search by participant NIM across all events
            if (!$event && $certificate->nim) {
                $participant = EventParticipant::where('nim', $certificate->nim)->first();
                if ($participant) {
                    $event = $participant->event;
                }
            }
            
            if ($event) {
                $participant = $event->participants()
                    ->where('nim', $certificate->nim)
                    ->first();
                if ($participant) {
                    $participantData = [
                        'name' => $participant->participant_name,
                        'nim' => $participant->nim,
                        'faculty' => $participant->faculty,
                        'study_program' => $participant->study_program,
                        'event_name' => $event->event_name,
                    ];
                }
            }
        }

        return view('results', [
            'certificate'    => $certificate,
            'certificate_type' => $certificate->certificate_type ?? 'external',
            'match_scores'   => $analysis->match_scores ?? [],
            'final_score'    => $certificate->final_score,
            'verifikasi_ai'  => $this->translateAiResponse(
                                    $analysis->verifikasi_ai ?? null
                                ),
            'ocr_text'       => $ocr->ocr_text ?? '',
            'google_results' => $analysis->google_results ?? [],
            'font_results'   => $analysis->font_results ?? [],
            'ocr_details'    => $ocr->ocr_details ?? [],
            'isAdmin'        => true,
            'internal_verified' => $certificate->internal_verified ?? false,
            'internal_verification_notes' => $certificate->internal_verification_notes ?? null,
            'internal_matched_event_name' => $certificate->nama_kegiatan ?? null,
            'internal_participant_data' => $participantData,
        ]);
    }

    /**
     * Translate AI response to current locale.
     */
    private function translateAiResponse($response)
    {
        if (empty($response)) {
            return null;
        }

        // If it's already translated or in current locale, return as is
        if (is_string($response)) {
            return $response;
        }

        // If it's an array with locale keys
        if (is_array($response)) {
            $locale = app()->getLocale();
            return $response[$locale] ?? $response['en'] ?? $response['id'] ?? json_encode($response);
        }

        return $response;
    }

    /**
     * Download Excel template for participant upload (simplified).
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1: Participant Headers (simplified)
        $participantHeaders = ['No', 'NIM', 'Nama', 'Fakultas', 'Jurusan'];
        $sheet->fromArray($participantHeaders, null, 'A1');

        // Row 2+: Sample participant data
        $sampleData = [
            [1, '123456789', 'John Doe', 'Fakultas Teknik', 'Teknik Informatika'],
            [2, '987654321', 'Jane Smith', 'Fakultas Ekonomi', 'Manajemen'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');
        
        // Style participant headers (Row 1)
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1:E1')->getFill()->getStartColor()->setRGB('B62A2D');
        $sheet->getStyle('A1:E1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create the file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'template_peserta_kegiatan.xlsx';
        
        $tempPath = storage_path('app/temp/' . $filename);
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Get value from row based on column map.
     */
    private function getColumnValue(array $row, array $columnMap, string $key)
    {
        if (!isset($columnMap[$key])) {
            return null;
        }
        
        $value = $row[$columnMap[$key]] ?? null;
        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Parse date from Excel cell value.
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // If it's already a valid date string
        if (is_string($value)) {
            $value = trim($value);
            if (empty($value)) {
                return null;
            }
            
            // Try parsing common date formats
            $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d'];
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $value);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try strtotime as fallback
            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        }

        // If it's a numeric Excel date serial
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        return null;
    }

    /**
     * Get a temporary signed URL for viewing the certificate file.
     */
    public function getCertificateFileUrl($id)
    {
        $certificate = Certificate::findOrFail($id);

        if (empty($certificate->berkas)) {
            return response()->json([
                'success' => false,
                'message' => __('results.noFileUploaded'),
            ], 404);
        }

        $url = $certificate->getTemporaryFileUrl(60); // 1 hour expiry

        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => __('results.fileUrlError'),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'url' => $url,
            'file_type' => $certificate->file_type,
            'filename' => basename($certificate->berkas),
        ]);
    }

    /**
     * Proxy endpoint to serve certificate file directly (bypasses CORS/CSP issues)
     */
    public function getCertificateFileProxy($id)
    {
        $certificate = Certificate::findOrFail($id);

        if (empty($certificate->berkas)) {
            abort(404, __('results.noFileUploaded'));
        }

        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('r2');
            
            if (!$disk->exists($certificate->berkas)) {
                abort(404, __('results.fileNotFound'));
            }

            $fileContent = $disk->get($certificate->berkas);
            $mimeType = $disk->mimeType($certificate->berkas) ?: 'application/octet-stream';
            $filename = basename($certificate->berkas);

            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Cache-Control', 'private, max-age=3600');
        } catch (\Exception $e) {
            \Log::error('Failed to proxy certificate file: ' . $e->getMessage());
            abort(500, __('results.fileUrlError'));
        }
    }

    /**
     * Update certificate verification status (admin only).
     * Only allowed for certificates with 'suspicious' status.
     */
    public function updateCertificateStatus(Request $request, $id)
    {
        $request->validate([
            'new_status' => 'required|in:verified,not_verified',
            'notes' => 'nullable|string|max:1000',
        ]);

        $certificate = Certificate::findOrFail($id);

        // Only allow updating suspicious certificates
        $currentStatus = $certificate->overall_status;
        if ($currentStatus !== 'suspicious') {
            return response()->json([
                'success' => false,
                'message' => __('results.statusUpdateNotAllowed'),
            ], 403);
        }

        // Determine new score based on new status
        $oldScore = $certificate->final_score;
        $newScore = $request->new_status === 'verified' ? 80 : 40; // 80 for verified, 40 for not_verified

        // Create log entry
        CertificateStatusLog::create([
            'certificate_id' => $certificate->id,
            'admin_id' => Auth::id(),
            'old_status' => $currentStatus,
            'new_status' => $request->new_status,
            'old_score' => $oldScore,
            'new_score' => $newScore,
            'notes' => $request->notes,
        ]);

        // Update certificate score
        $certificate->update([
            'final_score' => $newScore,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('results.statusUpdateSuccess'),
            'new_status' => $request->new_status,
            'new_score' => $newScore,
            'status_label' => $request->new_status === 'verified' 
                ? __('results.verified') 
                : __('results.notVerified'),
        ]);
    }
}
