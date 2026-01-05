<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Certificate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyCertificateJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;
    public $backoff = [15, 30, 60]; // detik

    public function __construct(public int $certificateId) {}

    public function handle()
    {
        $certificate = Certificate::find($this->certificateId);

        // Certificate dihapus → stop job
        if (!$certificate) {
            Log::warning('Certificate not found', [
                'certificate_id' => $this->certificateId
            ]);
            return;
        }

        $absolutePath = storage_path('app/public/' . $certificate->berkas);

        // ⚠️ Error sementara → retry otomatis
        if (!file_exists($absolutePath)) {
            Log::warning('Certificate file missing, retrying', [
                'path' => $absolutePath,
                'attempt' => $this->attempts()
            ]);

            $this->release(30); // retry 30 detik lagi
            return;
        }

        $apiUrl = config('services.fastapi.url');
        if (!$apiUrl) {
            // ❌ Bug konfigurasi → FAIL keras
            throw new \RuntimeException('FASTAPI_URL tidak dikonfigurasi');
        }

        $response = Http::timeout(300)
            ->asMultipart()
            ->attach(
                'berkas',
                file_get_contents($absolutePath),
                basename($absolutePath)
            )
            ->post($apiUrl . '/certificate/verify', [
                'nama' => $certificate->nama,
                'tahun_akademik' => $certificate->tahun_akademik,
                'penyelenggara' => $certificate->penyelenggara,
                'tanggal_mulai' => $certificate->tanggal_mulai,
                'tanggal_selesai' => $certificate->tanggal_selesai,
                'nama_kegiatan' => $certificate->nama_kegiatan,
                'nama_kegiatan_inggris' => $certificate->nama_kegiatan_inggris,
            ]);

        // ⚠️ FastAPI down / error → retry
        if (!$response->ok()) {
            Log::warning('FastAPI error, retrying', [
                'status' => $response->status(),
                'attempt' => $this->attempts()
            ]);

            $this->release(60);
            return;
        }

        $result = $response->json();

        // === SIMPAN HASIL ===
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
