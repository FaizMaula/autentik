<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;

// class ResultController extends Controller
// {
//     /**
//      * Handle form submission, send to FastAPI, save result in session, redirect to results page
//      */
//     public function handleForm(Request $request)
//     {
//         $validated = $request->validate([
//             'nama' => 'required|string',
//             'tahun_akademik' => 'nullable|string',
//             'penyelenggara' => 'required|string',
//             'tanggal_mulai' => 'required|date',
//             'tanggal_selesai' => 'required|date',
//             'nama_kegiatan' => 'required|string',
//             'nama_kegiatan_inggris' => 'nullable|string',
//             'berkas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
//             'confirmData' => 'required|accepted',
//         ]);

//         // Simpan file jika ada
//         if ($request->hasFile('berkas')) {
//             $filePath = $request->file('berkas')->store('uploads', 'public');
//             $validated['berkas_path'] = $filePath;
//         }

//         try {
//             // Kirim data ke FastAPI
//             $fastApiResponse = Http::timeout(10)->post('http://localhost:8001/certificate/verified', $validated);

//             if ($fastApiResponse->failed()) {
//                 return back()->with('error', 'Gagal memverifikasi di FastAPI.');
//             }

//             $resultData = $fastApiResponse->json();

//             // Simpan hasil di session
//             session(['ml_result' => $resultData]);

//             // Redirect ke halaman result
//             return redirect()->route('results.show');

//         } catch (\Exception $e) {
//             return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
//         }
//     }

//     /**
//      * Show the results page (Blade)
//      */
//     public function show()
//     {
//         // Session menyimpan hasil FastAPI
//         $resultData = session('ml_result', null);

//         if (!$resultData) {
//             return redirect('/form')->with('error', 'Tidak ada hasil verifikasi.');
//         }

//         return view('results', ['resultData' => $resultData]);
//     }

//     /**
//      * API endpoint untuk fetch hasil via JS
//      */
//     public function apiResults()
//     {
//         $resultData = session('ml_result', null);

//         if (!$resultData) {
//             return response()->json(['error' => 'No results found'], 404);
//         }

//         return response()->json($resultData);
//     }
// }
