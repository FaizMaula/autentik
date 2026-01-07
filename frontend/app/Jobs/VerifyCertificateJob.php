<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class VerifyCertificateJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 5;
    public $backoff = [30, 60, 120];
    public $timeout = 360;

    public function __construct(public int $certificateId) {}

    public function handle()
    {
        $certificate = Certificate::find($this->certificateId);

        if (!$certificate) {
            Log::warning('Certificate not found, job stopped', [
                'certificate_id' => $this->certificateId
            ]);
            return;
        }

        // ✅ Ambil file dari R2
        if (!Storage::disk('r2')->exists($certificate->berkas)) {
            throw new \RuntimeException('File not found in R2: ' . $certificate->berkas);
        }

        $fileContents = Storage::disk('r2')->get($certificate->berkas);
        $filename = basename($certificate->berkas);

        $apiUrl = config('services.fastapi.url');
        if (!$apiUrl) {
            throw new \RuntimeException('FASTAPI_URL not configured');
        }

        $response = Http::timeout(300)
            ->asMultipart()
            ->attach('berkas', $fileContents, $filename)
            ->post($apiUrl . '/certificate/verify', [
                'nama' => $certificate->nama,
                'tahun_akademik' => $certificate->tahun_akademik,
                'penyelenggara' => $certificate->penyelenggara,
                'tanggal_mulai' => $certificate->tanggal_mulai,
                'tanggal_selesai' => $certificate->tanggal_selesai,
                'nama_kegiatan' => $certificate->nama_kegiatan,
                'nama_kegiatan_inggris' => $certificate->nama_kegiatan_inggris,
                'lang' => $certificate->language ?? 'id',
            ]);

        if (!$response->ok()) {
            throw new \RuntimeException(
                'FastAPI error: ' . $response->status() . ' - ' . $response->body()
            );
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

    public function failed(Throwable $e)
    {
        Log::error('VerifyCertificateJob permanently failed', [
            'certificate_id' => $this->certificateId,
            'error' => $e->getMessage(),
        ]);
    }
}
