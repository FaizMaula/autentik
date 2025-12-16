<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class CertificateController extends Controller
{
    public function create()
    {
        return view('form');
    }

    public function store(Request $request)
    {
        // Allow long processing time (OCR + AI)
        set_time_limit(300);

        // === 1. Validasi input ===
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_akademik' => 'nullable|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'required|date',
            'nama_kegiatan' => 'required|string|max:255',
            'nama_kegiatan_inggris' => 'nullable|string|max:255',
            'berkas' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // === 2. Simpan file ke storage ===
        $file = $request->file('berkas');
        $path = $file->store('certificates', 'public');
        $absolutePath = storage_path("app/public/" . $path);

        if (!file_exists($absolutePath)) {
            return back()->withErrors([
                'berkas' => 'File gagal disimpan di server.'
            ])->withInput();
        }

        // === 3. Kirim ke FastAPI ===
        try {
            $response = Http::timeout(180)
                ->retry(3, 1500)
                ->asMultipart()
                ->attach(
                    'berkas',
                    file_get_contents($absolutePath),
                    $file->getClientOriginalName()
                )
                ->post('http://127.0.0.1:8001/certificate/verify', [
                    'nama' => $request->nama,
                    'tahun_akademik' => $request->tahun_akademik,
                    'penyelenggara' => $request->penyelenggara,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'nama_kegiatan' => $request->nama_kegiatan,
                    'nama_kegiatan_inggris' => $request->nama_kegiatan_inggris,
                ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'berkas' => 'Gagal terhubung ke layanan OCR/AI. ' . $e->getMessage()
            ])->withInput();
        }

        // === 4. Jika FastAPI gagal ===
        if (!$response->ok()) {
            return back()->withErrors([
                'berkas' => 'OCR/AI gagal (HTTP ' . $response->status() . ').'
            ])->withInput();
        }

        // === 5. Ambil hasil FastAPI ===
        $fastApiResult = $response->json();
        // dd($fastApiResult);


        return view('results', [
            'match_scores'   => $fastApiResult['match_scores'] ?? [],
            'final_score'    => $fastApiResult['final_score'] ?? 0,
            'verifikasi_ai'  => $fastApiResult['verifikasi_ai'] 
                                ?? 'Verifikasi AI tidak tersedia.',
            'ocr_text'       => $fastApiResult['ocr_text'] ?? '',
            'google_results' => $fastApiResult['google_results'] ?? [],
        ]);
    }
}

        


    //     // === 5. Ambil hasil OCR ===
    //     $ocr = $response->json();

    //     dd($ocr);

    //     // Pastikan key yang wajib ada
    //     if (!isset($ocr["ocr_text"]) || !isset($ocr["final_score"])) {
    //         return back()->withErrors([
    //             'berkas' => 'Respons dari OCR tidak lengkap.'
    //         ]);
    //     }

    //     // === 6. Simpan data ke database ===
    //     $certificate = Certificate::create([
    //         'nama' => $request->nama,
    //         'tahun_akademik' => $request->tahun_akademik,
    //         'penyelenggara' => $request->penyelenggara,
    //         'tanggal_mulai' => $request->tanggal_mulai,
    //         'tanggal_selesai' => $request->tanggal_selesai,
    //         'nama_kegiatan' => $request->nama_kegiatan,
    //         'nama_kegiatan_inggris' => $request->nama_kegiatan_inggris,
    //         'berkas' => $path,

    //         // Data dari FastAPI
    //         'ocr_text' => $ocr["ocr_text"],
    //         'match_scores' => json_encode($ocr["match_scores"] ?? []),
    //         'verifikasi_ai' => $ocr["verifikasi_ai"] ?? null,
    //         'final_score' => $ocr["final_score"],
    //     ]);

        

    //     // === 7. Redirect ke halaman hasil ===
    //     return redirect()->route('result.show', $certificate->id);
    // }
    // public function showResult($id)
    // {
    //     $certificate = Certificate::findOrFail($id);

    //     return view('results', [
    //         'certificate' => $certificate
    //     ]);
    // }

    // public function apiResult($id)
    // {
    //     $certificate = Certificate::find($id);

    //     if (!$certificate) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Data certificate tidak ditemukan'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $certificate
    //     ]);
    // }

    // public function apiAllResults()
    // {
    //     $results = Certificate::orderBy('created_at', 'desc')->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $results
    //     ]);
//     }

    
// }