@extends('layouts.app')
@section('content')

@php
  $status = $final_score >= 75 ? 'verified' : 'notVerified';
  $icon = $status == 'verified'
    ? '<i data-lucide="check-circle" class="text-green-500" style="width:48px;height:48px"></i>'
    : '<i data-lucide="x-circle" class="text-red-500" style="width:48px;height:48px"></i>';
  $statusLabel = $status == 'verified' ? 'Terverifikasi' : 'Tidak Terverifikasi';
  $dash = ($final_score / 100) * 352;
@endphp

<div class="min-h-screen bg-gradient-to-br from-[#C5D3D8] via-[#B8C8CE] to-[#A8B8BE] pt-24 pb-12">
  <div class="container mx-auto px-6">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-700 mb-6">
      <a href="/" class="hover:text-[#4A7C87] transition-colors">
        <i data-lucide="home" style="width:18px;height:18px"></i>
      </a>
      <span>/</span>
      <span class="font-medium">{{ __('results.title') }}</span>
    </div>

    <!-- Overall Status Card -->
    <div class="relative max-w-6xl mx-auto mb-8">
      <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="flex items-center gap-6">
            <div>{!! $icon !!}</div>
            <div>
              <h2 class="text-2xl font-bold text-[#0F0F10] mb-2">Status Sertifikat</h2>
              <p class="text-3xl font-extrabold text-[#4A7C87]">{{ $statusLabel }}</p>
            </div>
          </div>

          <div class="text-center">
            <p class="text-sm text-gray-600 mb-2">Skor Kecocokan</p>
            <div class="relative w-32 h-32 mx-auto">
              <svg class="transform -rotate-90 w-32 h-32">
                <circle cx="64" cy="64" r="56" stroke="#E5E7EB" stroke-width="12" fill="none"></circle>
                <circle cx="64" cy="64" r="56"
                  stroke="#4A7C87" stroke-width="12" fill="none"
                  stroke-dasharray="{{ $dash }} 352"
                  stroke-linecap="round"
                  class="transition-all duration-1000"></circle>
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-2xl font-bold text-[#4A7C87]">{{ round($final_score, 2) }}%</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>


    <!-- Dynamic Sections -->
    {{-- -- Match Score Detail -- --}}
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 space-y-6">
      <h3 class="text-2xl font-bold text-[#0F0F10] mb-2">Kecocokan Data</h3>

      @foreach ($match_scores as $field => $score)
        @php
          $status = $score >= 90 ? 'verified' : ($score >= 75 ? 'partial' : 'notVerified');
          $bgColor = $status == 'verified' ? 'bg-green-500' : ($status == 'partial' ? 'bg-yellow-500' : 'bg-red-500');
        @endphp
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <span class="font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $field)) }}</span>
          <div class="flex items-center gap-3">
            <div class="w-48 bg-gray-200 rounded-full h-3">
              @php
                $widthStyle = 'width: ' . round($score, 2) . '%;';
              @endphp
              <div class="h-3 rounded-full {{ $bgColor }}" style="{{ $widthStyle }}"></div>
            </div>
            <span class="font-bold text-[#4A7C87]">{{ round($score, 2) }}%</span>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Verifikasi AI --}}
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 mt-6">
      <h3 class="text-2xl font-bold text-[#0F0F10] mb-2">Hasil Verifikasi AI</h3>
      <p class="text-gray-700 bg-blue-50 p-4 rounded-lg border-l-4 border-[#4A7C87]">
        {!! nl2br(e($verifikasi_ai)) !!}
      </p>
    </div>


    {{-- Top 5 Google Search Results --}}

    @if (!empty($google_results))
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 mt-6">
      <h3 class="text-2xl font-bold text-[#0F0F10] mb-4">
        Referensi Google Terkait
      </h3>

      <div class="space-y-4">
        @foreach ($google_results as $index => $result)
          <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-[#4A7C87]">
            <p class="text-sm text-gray-500 mb-1">
              Hasil #{{ $index + 1 }}
            </p>

            <a href="{{ $result['link'] }}" target="_blank"
              class="text-lg font-semibold text-[#4A7C87] hover:underline">
              {{ $result['title'] }}
            </a>

            <p class="text-gray-700 text-sm mt-1">
              {{ $result['description'] }}
            </p>

            <p class="text-xs text-gray-500 mt-2 break-all">
              {{ $result['link'] }}
            </p>
          </div>
        @endforeach
      </div>
    </div>
    @endif


    <!-- {{-- OCR Text --}}
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 mt-6">
      <h3 class="text-2xl font-bold text-[#0F0F10] mb-2">Teks Hasil OCR</h3>
      <pre class="text-sm text-gray-800 whitespace-pre-wrap bg-gray-100 p-4 rounded-lg">{{ $ocr_text }}</pre>
    </div> -->


    <!-- Buttons -->
    <div class="max-w-6xl mx-auto mt-8 flex flex-col sm:flex-row gap-4">
      <a href="/form" 
         class="flex-1 px-6 py-4 bg-[#000033] text-white rounded-lg font-semibold text-lg 
         flex items-center justify-center gap-2 hover:bg-[#000055] transform hover:scale-105 
         transition-all duration-300 shadow-lg">
        <i data-lucide="arrow-left" style="width:20px;height:20px"></i>
        {{ __('results.backToForm') }}
      </a>

      <button type="button" 
        class="px-6 py-4 bg-[#4A7C87] text-white rounded-lg font-semibold text-lg 
        flex items-center justify-center gap-2 hover:bg-[#3A6C77] transform hover:scale-105 
        transition-all duration-300 shadow-lg"
        onclick="downloadReport()">
        <i data-lucide="download" style="width:20px;height:20px"></i>
        {{ __('results.downloadReport') }}
      </button>
    </div>

  </div>
</div>

<script>

// function getIdFromUrl() {
//   const parts = window.location.pathname.split('/');
//   return parts[parts.length - 1]; // ambil 39
// }

// async function fetchResults() {
//   try {
//     const id = getIdFromUrl();
//     const response = await fetch(`/api/results/${id}`);
//     if (!response.ok) throw new Error('Failed to fetch results');
//     const data = await response.json();
//     renderResults(data);
//   } catch (err) {
//     alert("Gagal memuat hasil: " + err.message);
//   }
// }


// function statusIcon(status) {
//   const icons = {
//     'verified':   '<i data-lucide="check-circle" class="text-green-500" style="width:48px;height:48px"></i>',
//     'notVerified':'<i data-lucide="x-circle" class="text-red-500" style="width:48px;height:48px"></i>',
//     'suspicious': '<i data-lucide="alert-triangle" class="text-yellow-500" style="width:48px;height:48px"></i>',
//   };
//   return icons[status] || '';
// }

// function statusText(status) {
//   const texts = {
//     'verified': "{{ __('results.verified') }}",
//     'notVerified': "{{ __('results.notVerified') }}",
//     'suspicious': "{{ __('results.suspicious') }}",
//   };
//   return texts[status] || '';
// }

function statusColor(status){
  const greens = ['verified','match','valid','good','excellent','high','authentic','found'];
  const yellows = ['partial','warning'];
  const reds = ['notVerified','suspicious','mismatch','invalid','poor','low'];
  
  if(greens.includes(status)) return 'bg-green-500';
  if(yellows.includes(status)) return 'bg-yellow-500';
  if(reds.includes(status)) return 'bg-red-500';
  return 'bg-gray-500';
}

// function renderResults(results){
  
//   // Overall
//   document.getElementById('overallStatusIcon').innerHTML = statusIcon(results.overallStatus);
//   document.getElementById('overallStatusText').textContent = statusText(results.overallStatus);

//   const circumference = 352;
//   const dash = (results.confidence / 100) * circumference;
//   document.getElementById('confidenceCircle').setAttribute('stroke-dasharray', `${dash} ${circumference}`);
//   document.getElementById('confidenceValue').textContent = `${results.confidence}%`;

//   const root = document.getElementById('resultsSections');

//   // TEXT MATCHING
//   if(results.textMatching){
//     const tm = document.createElement('div');
//     tm.className = 'bg-white rounded-2xl shadow-xl p-8';

//     tm.innerHTML = `
//       <div class="flex items-start gap-4 mb-6">
//         <div class="p-3 bg-[#4A7C87] bg-opacity-10 rounded-lg">
//           <i data-lucide="file-text" class="text-[#4A7C87]" style="width:32px;height:32px"></i>
//         </div>
//         <div class="flex-1">
//           <h3 class="text-2xl font-bold text-[#0F0F10] mb-2">
//             {{ __('results.textMatching') }}
//           </h3>
//         </div>
//         <div class="text-right">
//           <p class="text-sm text-gray-600">{{ __('results.matchScore') }}</p>
//           <p class="text-3xl font-bold text-[#4A7C87]">${results.textMatching.score}%</p>
//         </div>
//       </div>

//       <div id="tmDetails" class="space-y-3"></div>

//       <p class="mt-6 text-gray-700 bg-blue-50 p-4 rounded-lg border-l-4 border-[#4A7C87]">
//         ${results.textMatching.summary}
//       </p>
//     `;

//     root.appendChild(tm);

//     const tmDetails = tm.querySelector('#tmDetails');
//     (results.textMatching.details || []).forEach(d => {
//       tmDetails.innerHTML += `
//         <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
//           <span class="font-medium text-gray-700">${d.field}</span>
//           <div class="flex items-center gap-3">
//             <div class="w-48 bg-gray-200 rounded-full h-3">
//               <div class="h-3 rounded-full ${statusColor(d.status)}" style="width:${d.match}%"></div>
//             </div>
//             <span class="font-bold text-[#4A7C87]">${d.match}%</span>
//           </div>
//         </div>
//       `;
//     });
//   }

//   if(window.lucide?.createIcons) window.lucide.createIcons();
// }

// fetchResults();

  if (window.lucide?.createIcons) window.lucide.createIcons();

  function downloadReport() {
    alert('Download feature belum diimplementasi.');
  }
</script>

@endsection