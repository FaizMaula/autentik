<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Certificate;
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
     * Store a newly created event from Excel upload.
     */
    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            // Parse Excel file
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // // First row should contain event info
            // $eventHeaderRow = array_shift($rows); // Row 1 (judul kolom event)
            // $eventInfoRow   = array_shift($rows); // Row 2 (isi event)
            // $headerRow      = array_shift($rows); // Row 3 (header peserta)
            // $eventHeaderRow = array_shift($rows); // row 1
            // $eventInfoRow   = array_shift($rows); // row 2

            // 1. Buang header event
            array_shift($rows);

            // 2. Ambil data event
            $eventInfoRow = array_shift($rows);

            // 3. Ambil header peserta
            $headerRow = array_shift($rows);

            // $headerRow = null;

            // cari baris yang mengandung "nim"
            // foreach ($rows as $index => $row) {
            //     foreach ($row as $cell) {
            //         if (is_string($cell) && stripos($cell, 'nim') !== false) {
            //             $headerRow = $row;
            //             unset($rows[$index]); // hapus header dari data
            //             $rows = array_values($rows);
            //             break 2;
            //         }
            //     }
            // }

            if (!$headerRow) {
                throw new \Exception('Header peserta tidak ditemukan di file Excel.');
            }
            // $headerRow = array_values(array_filter($headerRow, fn($v) => $v !== null && $v !== ''));
            // Extract event info from first row
            $eventName = $eventInfoRow[0] ?? null;
            $eventNameEn = $eventInfoRow[1] ?? null;
            $organizer = $eventInfoRow[2] ?? null;
            $eventDate = $this->parseDate($eventInfoRow[3] ?? null);
            $startDate = $this->parseDate($eventInfoRow[4] ?? null);
            $endDate = $this->parseDate($eventInfoRow[5] ?? null);
            $academicYear = $eventInfoRow[6] ?? null;
            $description = $eventInfoRow[7] ?? null;

            // Validate required fields
            if (empty($eventName) || empty($organizer)) {
                throw new \Exception(__('admin.excelMissingEventInfo'));
            }

            $event = Event::create([
                'uploaded_by' => Auth::id(),
                'event_name' => $eventName,
                'event_name_en' => $eventNameEn,
                'organizer' => $organizer,
                'event_date' => $eventDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'academic_year' => $academicYear,
                'description' => $description,
                'original_filename' => $file->getClientOriginalName(),
            ]);
            
            // Map headers to column indices
            
            $columnMap = $this->mapColumns($headerRow);

            // if (!isset($columnMap['nim']) || !isset($columnMap['name'])) {
            //     throw new \Exception('Kolom NIM dan Nama Peserta wajib ada.');
            // }
            
            $participantsData = [];
            foreach ($rows as $row) {
                $row = array_values(array_filter($row, fn($v) => $v !== null && $v !== ''));
                if (empty(array_filter($row))) {
                    continue;
                }

                $participantsData[] = [
                    'event_id' => $event->id,
                    'nim' => $this->getColumnValue($row, $columnMap, 'nim') ?? '',
                    'participant_name' => $this->getColumnValue($row, $columnMap, 'name') ?? '',
                    'email' => $this->getColumnValue($row, $columnMap, 'email'),
                    'faculty' => $this->getColumnValue($row, $columnMap, 'faculty'),
                    'study_program' => $this->getColumnValue($row, $columnMap, 'study_program'),
                    'attendance_status' => $this->getColumnValue($row, $columnMap, 'status') ?? 'present',
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

        return view('results', [
            'certificate'    => $certificate,
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
     * Download Excel template for participant upload.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1: Event Info Headers
        $eventHeaders = ['Nama Kegiatan *', 'Nama Kegiatan (EN)', 'Penyelenggara *', 'Tanggal Kegiatan', 'Tanggal Mulai', 'Tanggal Selesai', 'Tahun Akademik', 'Deskripsi'];
        $sheet->fromArray($eventHeaders, null, 'A1');
        
        // Row 2: Sample Event Info
        $sampleEventInfo = ['Seminar Teknologi 2024', 'Technology Seminar 2024', 'Fakultas Teknik', '2024-12-15', '2024-12-15', '2024-12-15', '2024/2025', 'Deskripsi kegiatan'];
        $sheet->fromArray($sampleEventInfo, null, 'A2');

        // Row 3: Participant Headers
        $participantHeaders = ['NIM', 'Nama Peserta', 'Email', 'Fakultas', 'Program Studi', 'Status Kehadiran'];
        $sheet->fromArray($participantHeaders, null, 'A3');

        // Row 4+: Sample participant data
        $sampleData = [
            ['123456789', 'John Doe', 'john@example.com', 'Fakultas Teknik', 'Teknik Informatika', 'Hadir'],
            ['987654321', 'Jane Smith', 'jane@example.com', 'Fakultas Ekonomi', 'Manajemen', 'Hadir'],
        ];
        $sheet->fromArray($sampleData, null, 'A3');

        // Style event info headers (Row 1)
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1:H1')->getFill()->getStartColor()->setRGB('4A7C87');
        $sheet->getStyle('A1:H1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Style participant headers (Row 3)
        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle('A3:F3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A3:F3')->getFill()->getStartColor()->setRGB('B62A2D');
        $sheet->getStyle('A3:F3')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Auto-size columns
        foreach (range('A', 'H') as $col) {
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
     * Map column headers to indices.
     */
    private function mapColumns(array $headerRow): array
    {
        $map = [];
        $aliases = [
            'nim' => ['nim', 'noinduk', 'nomorinduk', 'studentid', 'idmahasiswa', 'NIM'],
            'name' => ['nama', 'namapeserta', 'participantname', 'namamahasiswa','Nama Peserta'],
            'email' => ['email', 'emailpeserta', 'surel', 'Email'],
            'faculty' => ['fakultas', 'faculty', 'Fakultas'],
            'study_program' => ['prodi', 'programstudi', 'studyprogram', 'jurusan', 'Program Studi'],
            'status' => ['status', 'kehadiran', 'statuskehadiran', 'attendance','Status Kehadiran'],
        ];

        foreach ($headerRow as $index => $header) {
            if (!$header) continue;

            // NORMALISASI SUPER AMAN
            $normalizedHeader = strtolower(
                preg_replace('/[^a-z0-9]/', '', $header)
            );

            foreach ($aliases as $key => $possibleNames) {
                foreach ($possibleNames as $alias) {
                    $normalizedAlias = strtolower(
                        preg_replace('/[^a-z0-9]/', '', $alias)
                    );

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
}
