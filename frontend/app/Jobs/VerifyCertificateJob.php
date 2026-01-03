<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Certificate;
use Illuminate\Support\Facades\Http;
class VerifyCertificateJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public int $certificateId) {}

    public function handle()
    {
        $certificate = Certificate::findOrFail($this->certificateId);

        $absolutePath = storage_path('app/public/' . $certificate->berkas);

        if (!file_exists($absolutePath)) {
            throw new \Exception("File tidak ditemukan di kontainer Worker: " . $absolutePath);
        }
    
        $apiUrl = config('services.fastapi.url');
        if (!$apiUrl) {
            throw new \Exception("Konfigurasi FASTAPI_URL tidak ditemukan di config/services.php");
        }

        $response = Http::timeout(300)
            ->asMultipart()
            ->attach(
                'berkas',
                file_get_contents($absolutePath),
                basename($absolutePath)
            )
            ->post(config('services.fastapi.url') . '/certificate/verify', [
                'nama' => $certificate->nama,
                'tahun_akademik' => $certificate->tahun_akademik,
                'penyelenggara' => $certificate->penyelenggara,
                'tanggal_mulai' => $certificate->tanggal_mulai,
                'tanggal_selesai' => $certificate->tanggal_selesai,
                'nama_kegiatan' => $certificate->nama_kegiatan,
                'nama_kegiatan_inggris' => $certificate->nama_kegiatan_inggris,
            ]);

        if (!$response->ok()) {
            throw new \Exception("Gagal menghubungi FastAPI: " . $response->body());
        }

        $result = $response->json();

        $certificate->update([
            'final_score' => $result['final_score'] ?? 0,
            'is_verified' => ($result['final_score'] ?? 0) >= 75,
        ]);

        $certificate->ocrResults()->create([
            'ocr_engine' => 'trocr',
            'ocr_text' => $result['ocr_text'] ?? null,
            'ocr_details' => $result['ocr_details'] ?? [],
        ]);

        $certificate->analysisResults()->create([
            'google_results' => $result['google_results'] ?? [],
            'font_results' => $result['font_results'] ?? [],
            'match_scores' => $result['match_scores'] ?? [],
            'verifikasi_ai' => $result['verifikasi_ai'] ?? null,
            'analysis_version' => 'v1.0',
        ]);
    }
}

