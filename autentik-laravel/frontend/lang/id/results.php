<?php

return [
    'title' => 'Hasil Verifikasi',
    'subtitle' => 'Berikut adalah analisis lengkap verifikasi sertifikat Anda',
    'overallStatus' => 'Status Keseluruhan',
    'verified' => 'Terverifikasi',
    'notVerified' => 'Tidak Terverifikasi',
    'suspicious' => 'Mencurigakan',

    'textMatching' => 'Kecocokan Teks',
    'textMatchingDesc' => 'Analisis kemiripan teks pada sertifikat',

    'metadata' => 'Verifikasi Metadata',
    'metadataDesc' => 'Pemeriksaan metadata dokumen',

    'gamma' => 'Analisis Gamma',
    'gammaDesc' => 'Analisis teknis gambar',

    'googleSearch' => 'Hasil Pencarian Google',
    'googleSearchDesc' => 'Hasil pencarian yang terkait dengan sertifikat',

    'matchScore' => 'Skor Kecocokan',
    'confidence' => 'Tingkat Keyakinan',
    'backToForm' => 'Verifikasi Lagi',
    'downloadReport' => 'Unduh Laporan',
    'aiSummary' => 'Ringkasan AI',
    'aiSummaryDesc' => 'Gambaran singkat berbasis AI dari temuan verifikasi',
    'breadcrumbVerify' => 'Verifikasi',
    'relevance' => 'Relevansi',
    'status' => [
        'match' => 'COCOK',
        'valid' => 'VALID',
        'good' => 'BAIK',
        'excellent' => 'SANGAT BAIK',
        'partial' => 'SEBAGIAN',
        'warning' => 'PERINGATAN',
        'invalid' => 'TIDAK VALID',
        'poor' => 'BURUK',
        'low' => 'RENDAH',
        'mismatch' => 'TIDAK COCOK',
        'found' => 'DITEMUKAN',
        'high' => 'TINGGI',
    ],
    'fields' => [
        'recipientName' => 'Nama Penerima',
        'institutionName' => 'Nama Institusi',
        'issueDate' => 'Tanggal Penerbitan',
        'certificateNumber' => 'Nomor Sertifikat',
    ],
    'meta' => [
        'fileFormat' => 'Format File',
        'creatorSoftware' => 'Software Pembuat',
        'createdAt' => 'Tanggal Pembuatan',
        'modifiedAt' => 'Tanggal Modifikasi',
        'digitalSignature' => 'Tanda Tangan Digital',
        'encryption' => 'Enkripsi',
    ],
    'gammaParams' => [
        'compressionArtifacts' => 'Artefak Kompresi',
        'copyMoveDetection' => 'Deteksi Copy-Move',
        'errorLevelAnalysis' => 'Analisis Tingkat Error',
        'noisePattern' => 'Pola Noise',
        'jpegQuality' => 'Kualitas JPEG',
    ],
    'summaries' => [
        'textMatching' => 'Teks pada sertifikat memiliki tingkat kemiripan sangat tinggi dengan data referensi.',
        'metadata' => 'Metadata dokumen konsisten dan tidak menunjukkan tanda-tanda manipulasi.',
        'gamma' => 'Analisis teknis menunjukkan gambar asli tanpa manipulasi signifikan.',
        'google' => 'Ditemukan referensi terkait di beberapa sumber terpercaya online.',
    ],
];
