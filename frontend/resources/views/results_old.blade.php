@php
  // Generate status based on final_score
  if ($final_score >= 75) {
    $status = 'verified';
    $statusLabel = __('results.verified');
  } elseif ($final_score >= 50) {
    $status = 'suspicious';
    $statusLabel = __('results.suspicious');
  } else {
    $status = 'notVerified';
    $statusLabel = __('results.notVerified');
  }
  
  // Count metrics
  $textMatchCount = count($match_scores ?? []);
  $ocrCount = count($ocr_details ?? []);
  $googleCount = count($google_results ?? []);
@endphp

@extends('layouts.app')
@section('content')
<!-- Background Wrapper -->
<div class="min-h-screen pt-24 pb-0 relative overflow-hidden bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-[#1a1a1b] dark:via-[#222223] dark:to-[#2a2a2b]">
  @include('components.animated-background', ['showWatermark' => false])

  <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mb-4">
        <a href="/" class="hover:text-[#B62A2D] dark:hover:text-[#ff4444] transition-colors">
          <i data-lucide="home" style="width:18px;height:18px"></i>
        </a>
        <span>/</span>
        <a href="/form" class="hover:text-[#B62A2D] dark:hover:text-[#ff4444] transition-colors">{{ __('results.breadcrumbVerify') }}</a>
        <span>/</span>
        <span class="font-medium">{{ __('results.title') }}</span>
      </div>
      
      <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
        <div class="lg:w-auto lg:flex-shrink-0">
          <h1 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ __('results.title') }}</h1>
          <p class="text-lg text-gray-700 dark:text-gray-300">{{ __('results.subtitle') }}</p>
        </div>
        
        <div class="lg:flex-1">
          <div class="glass-card-strong border-l-4 border-amber-500 dark:border-amber-600 rounded-xl p-5 animate-fade-in bg-white/95 dark:bg-[#2a2a2b]/95 backdrop-blur-xl shadow-lg dark:shadow-2xl">
            <div class="flex items-start gap-4">
              <div class="flex-shrink-0 p-3 bg-amber-100/80 dark:bg-amber-900/40 backdrop-blur-sm rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600 dark:text-amber-400">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.disclaimerTitle') }}</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{!! __('results.disclaimerDescription') !!}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content with Slide Navigation -->
    {{-- -- Match Score Detail -- --}}
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 space-y-6">
      <h3 class="text-2xl font-bold text-[#0F0F10] mb-2">{{ __('results.dataMatching') }}</h3>
      @foreach ($match_scores as $field => $score)
        @php
          $status = $score >= 90 ? 'verified' : ($score >= 75 ? 'partial' : 'notVerified');
          $bgColor = $status == 'verified' ? 'bg-green-500' : ($status == 'partial' ? 'bg-yellow-500' : 'bg-red-500');
        @endphp
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <span class="font-medium text-gray-700">{{ __('results.' . $field) }}</span>
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
      <div class="mb-4">
        <h3 class="text-2xl font-bold text-[#0F0F10]">{{ __('results.aiVerification') }}</h3>
        <p class="text-sm text-gray-600 mt-1">{{ __('results.aiVerificationDesc') }}</p>
      </div>
      <p class="text-gray-700 bg-blue-50 p-4 rounded-lg border-l-4 border-[#4A7C87]">
        {!! nl2br(e($verifikasi_ai)) !!}
      </p>
    </div>

    {{-- OCR & Font Recognition --}}
  @if (!empty($ocr_details))
  <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 mt-10">

    <!-- Header + Toggle -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h3 class="text-2xl font-bold text-[#0F0F10]">
          OCR & Font Recognition
        </h3>
        <p class="text-sm text-gray-600">
          Detail hasil pembacaan teks dan identifikasi font
        </p>
      </div>

      <button
        onclick="toggleOCR()"
        class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-full
              bg-[#4A7C87]/10 text-[#4A7C87]
              hover:bg-[#4A7C87]/20 transition">
        <i data-lucide="eye" id="ocrIcon"></i>
        <span id="ocrToggleText">Show</span>
      </button>
    </div>

    <div id="ocrContent" class="space-y-6 hidden">
      @foreach ($ocr_details as $index => $item)
        @if (empty($item['font']) || ($item['accuracy'] ?? 0) < 0.6)
            @continue
        @endif  

        <div class="border rounded-xl p-6 bg-gray-50">

          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-semibold text-gray-600">
              Segment #{{ $index + 1 }}
            </span>

            <span class="px-3 py-1 text-xs font-semibold rounded-full
              {{ ($item['accuracy'] ?? 0) == 100 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
              Accuracy {{ $item['accuracy'] ?? 0 }}%
            </span>
          </div>

          <!-- OCR Text -->
          <div class="bg-white p-4 rounded-lg border mb-4">
            <p class="text-xs text-gray-500 mb-1">TrOCR</p>
            <p class="text-lg font-semibold text-[#4A7C87]">
              {{ $item['trocr'] ?? '-' }}
            </p>
          </div>


          <!-- OCR Confidence -->
          <div class="mb-4">
            <p class="text-sm text-gray-600 mb-1">OCR Confidence</p>
            <div class="w-full bg-gray-200 rounded-full h-3"> 
              <div class="h-3 rounded-full bg-[#4A7C87]"
                style="width: {{ round(($item['accuracy'] ?? 0), 2) }}%">
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
              {{ round(($item['accuracy'] ?? 0), 2) }}%
            </p>
          </div>
          @php
            $fontConf = ($item['font']['confidence'] ?? 0) * 100;

            if ($fontConf >= 80) {
                $confLabel = 'High';
                $confColor = 'bg-green-100 text-green-700';
                $confIcon  = 'check-circle';
            } elseif ($fontConf >= 60) {
                $confLabel = 'Medium';
                $confColor = 'bg-yellow-100 text-yellow-700';
                $confIcon  = 'alert-circle';
            } else {
                $confLabel = 'Low';
                $confColor = 'bg-red-100 text-red-700';
                $confIcon  = 'alert-triangle';
            }
          @endphp
          <!-- Font Result -->
          @if (!empty($item['font']))
          @php
            $fontStatus = $item['font']['status'] ?? 'unknown';

            $fontColor = match($fontStatus) {
                'match'    => 'text-green-600',
                'mismatch' => 'text-red-600',
                'new'      => 'text-blue-600',
                default    => 'text-gray-600'
            };

            $badgeColor = match($fontStatus) {
                'match'    => 'bg-green-100 text-green-700',
                'mismatch' => 'bg-red-100 text-red-700',
                'new'      => 'bg-blue-100 text-blue-700',
                default    => 'bg-gray-100 text-gray-700'
            };
          @endphp

          <div class="bg-white border rounded-lg p-4">
            <p class="text-sm font-semibold text-gray-700 mb-2">
              Detected Font
            </p>
            @if (!empty($item['font']['reference_font']))
              <div class="mt-3 p-3 rounded-lg bg-gray-50 border">
                <p class="text-xs text-gray-500 mb-1">
                  Reference Font (previous certificate)
                </p>

                <p class="text-sm font-semibold
                  {{ $fontStatus === 'match' ? 'text-green-700' : 'text-red-700' }}">
                  {{ $item['font']['reference_font'] }}
                </p>

                <p class="text-xs text-gray-500 mt-1">
                  Comparison result:
                  <span class="font-semibold capitalize">
                    {{ str_replace('_', ' ', $fontStatus) }}
                  </span>
                </p>
              </div>
            @endif


            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div>
                <p class="text-lg font-bold {{ $fontColor }}">
                  {{ $item['font']['class'] ?? '-' }}
                </p>

                <span class="inline-block mt-1 px-3 py-1 text-xs rounded-full {{ $badgeColor }}">
                  {{ ucfirst($fontStatus) }}
                </span>

                <p class="text-xs text-gray-500 mt-1">
                  Google Font:
                  {{ isset($item['font']['google_font'])
                      ? implode(' ', $item['font']['google_font'])
                      : '-' }}
                </p>
              </div>

              @php
                $fontConf = round(($item['font']['confidence'] ?? 0) * 100, 2);
              @endphp

              <div class="flex items-center gap-3 px-4 py-3 rounded-lg
                {{ $fontConf < 60
                    ? 'bg-red-50 border border-red-200'
                    : ($fontConf < 80
                        ? 'bg-yellow-50 border border-yellow-200'
                        : 'bg-green-50 border border-green-200') }}">
                
                <i data-lucide="{{ $fontConf < 60 ? 'alert-triangle' : ($fontConf < 80 ? 'alert-circle' : 'check-circle') }}"
                  class="{{ $fontConf < 60 ? 'text-red-600' : ($fontConf < 80 ? 'text-yellow-600' : 'text-green-600') }}"
                  style="width:20px;height:20px"></i>

                <div>
                  <p class="font-semibold
                    {{ $fontConf < 60 ? 'text-red-700' : ($fontConf < 80 ? 'text-yellow-700' : 'text-green-700') }}">
                    Font Confidence {{ $fontConf }}%
                  </p>
                  <p class="text-xs text-gray-600">
                    {{ $fontConf < 60
                        ? 'Low confidence — font identification may be unreliable'
                        : ($fontConf < 80
                            ? 'Medium confidence — review recommended'
                            : 'High confidence — font confidently identified') }}
                  </p>
                </div>
              </div>

            </div>
          </div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
    
  </div>
  @endif

  

    {{-- Top 5 Google Search Results --}}
    <!-- Google Results Section (Server-side rendered) -->
    @if (!empty($google_results))
      <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-8 mt-10">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-2xl font-bold text-[#0F0F10]">
              {{ __('results.googleReferences') }}
            </h3>
            <p class="text-sm text-gray-600">
              {{ __('results.googleReferencesDesc') }}
            </p>
          </div>

          <span class="px-3 py-1 text-xs font-semibold rounded-full bg-[#4A7C87]/10 text-[#4A7C87]">
            {{ count($google_results) }} {{ __('results.results') }}
          </span>
        </div>

        <div class="space-y-4">
          @foreach ($google_results as $index => $result)
          <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-[#4A7C87] hover:bg-white hover:shadow-md transition-all">
            <div class="flex items-start gap-3">
              
              <div class="mt-1 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-[#4A7C87]/10 text-xs font-semibold text-[#4A7C87]">
                #{{ $index + 1 }}
              </div>

              <div class="flex-1 min-w-0"> 
                
                <p class="text-[11px] text-gray-500 mb-1">
                  {{ __('results.result') }} #{{ $index + 1 }}
                </p>

                <a href="{{ $result['link'] }}" target="_blank"
                  class="block text-base md:text-lg font-semibold text-[#4A7C87] hover:underline break-words leading-tight">
                  {{ $result['title'] }}
                </a>

                <p class="text-sm text-gray-700 mt-1 break-words">
                  {{ $result['description'] }}
                </p>

                <p class="text-xs text-green-700 mt-2 truncate">
                  {{ $result['link'] }}
                </p>

                <p class="text-[11px] text-gray-500 mt-1">
                  {{ __('results.source') }}: {{ parse_url($result['link'], PHP_URL_HOST) }}
                </p>
              </div>
            </div>
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

function statusColor(status){
  const greens = ['verified','match','valid','good','excellent','high','authentic','found'];
  const yellows = ['partial','warning'];
  const reds = ['notVerified','suspicious','mismatch','invalid','poor','low'];
  
  if(greens.includes(status)) return 'bg-green-500';
  if(yellows.includes(status)) return 'bg-yellow-500';
  if(reds.includes(status)) return 'bg-red-500';
  return 'bg-gray-500';
}


  if (window.lucide?.createIcons) window.lucide.createIcons();

  
  function downloadReport() {
    // Memanggil print dialog browser
    window.print();
  }

  // Optional: menambahkan shortcut keyboard, misal Ctrl + P
  document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && event.key === 'p') {
      event.preventDefault(); // mencegah print default browser
      downloadReport();
    }
  });
</script>

<script>
  function toggleOCR() {
    const content = document.getElementById('ocrContent');
    const text = document.getElementById('ocrToggleText');
    const icon = document.getElementById('ocrIcon');

    const isHidden = content.classList.contains('hidden');

    content.classList.toggle('hidden');

    if (isHidden) {
      text.innerText = 'Hide';
      icon.setAttribute('data-lucide', 'eye-off');
    } else {
      text.innerText = 'Show';
      icon.setAttribute('data-lucide', 'eye');
    }

    // refresh lucide icon
    if (window.lucide?.createIcons) window.lucide.createIcons();
  }
</script>


@endsection