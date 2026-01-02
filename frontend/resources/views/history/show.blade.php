@extends('layouts.app')
@section('content')
<!-- Background Wrapper Extended to include footer -->
<div class="min-h-screen pt-24 pb-0 relative overflow-hidden">
  @include('components.animated-background', ['showWatermark' => false])
  
  <div class="container mx-auto px-6 relative z-10">
    <!-- Header -->
    <div class="max-w-6xl mx-auto mb-8">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mb-4">
        <a href="/" class="hover:text-[#B62A2D] transition-colors">
          <i data-lucide="home" style="width:18px;height:18px"></i>
        </a>
        <span>/</span>
        <a href="{{ route('history.index') }}" class="hover:text-[#B62A2D] transition-colors">{{ __('history.breadcrumb') }}</a>
        <span>/</span>
        <span class="font-medium">{{ __('history.detailTitle') }}</span>
      </div>
      <h1 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ __('history.detailTitle') }}</h1>
      <p class="text-lg text-gray-700 dark:text-gray-300">{{ __('history.detailSubtitle') }}</p>
    </div>

    <!-- Verification Info Card -->
    <div class="max-w-6xl mx-auto mb-8">
      <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
        <h2 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ $history->event_name }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-start gap-3">
            <i data-lucide="user" class="text-[#B62A2D] flex-shrink-0 mt-1" style="width:20px;height:20px"></i>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('form.name') }}</p>
              <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $history->name }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <i data-lucide="building" class="text-[#B62A2D] flex-shrink-0 mt-1" style="width:20px;height:20px"></i>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('form.organizer') }}</p>
              <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $history->organizer }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <i data-lucide="calendar" class="text-[#B62A2D] flex-shrink-0 mt-1" style="width:20px;height:20px"></i>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('history.dateRange') }}</p>
              <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $history->date_range }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <i data-lucide="file" class="text-[#B62A2D] flex-shrink-0 mt-1" style="width:20px;height:20px"></i>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('history.fileName') }}</p>
              <p class="font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $history->file_name }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <i data-lucide="clock" class="text-[#B62A2D] flex-shrink-0 mt-1" style="width:20px;height:20px"></i>
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('history.verifiedAt') }}</p>
              <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $history->created_at->format('d M Y, H:i') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Disclaimer Notice -->
    <div class="max-w-6xl mx-auto mb-8">
      <div class="glass-card-strong border-l-4 border-amber-500 rounded-2xl p-8 animate-fade-in">
        <div class="flex items-start gap-6">
          <div class="flex-shrink-0 p-4 bg-amber-100/80 dark:bg-amber-900/40 backdrop-blur-sm rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600 dark:text-amber-400">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-3">
              {{ __('results.disclaimerTitle') }}
            </h3>
            <p class="text-gray-700 dark:text-gray-300 text-base leading-relaxed mb-4">
              {!! __('results.disclaimerDescription') !!}
            </p>
            <div class="flex items-center gap-2 bg-amber-50/80 dark:bg-amber-900/30 backdrop-blur-sm border border-amber-200 dark:border-amber-700 rounded-lg px-4 py-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600 dark:text-amber-400 flex-shrink-0">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
              <span class="text-amber-800 dark:text-amber-300 font-medium text-sm">{{ __('results.disclaimerWarning') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Overall Status Card -->
    <div class="max-w-6xl mx-auto mb-8">
      <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="flex items-center gap-6">
            <div id="overallStatusIcon"></div>
            <div>
              <h2 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.overallStatus') }}</h2>
              <p id="overallStatusText" class="text-3xl font-extrabold text-green-500"></p>
            </div>
          </div>
          @if($history->confidence)
          <div class="text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('results.confidence') }}</p>
            <div class="relative w-32 h-32 glow-red rounded-full">
              <svg class="transform -rotate-90 w-32 h-32">
                <circle id="confidenceBgCircle" cx="64" cy="64" r="56" stroke="rgba(34, 197, 94, 0.2)" stroke-width="12" fill="none"></circle>
                <circle id="confidenceCircle" cx="64" cy="64" r="56" stroke="#22C55E" stroke-width="12" fill="none" stroke-dasharray="0 352" stroke-linecap="round" class="transition-all duration-1000"></circle>
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span id="confidenceValue" class="text-3xl font-bold text-green-500">0%</span>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Sections filled by JS using history data -->
    <div class="max-w-6xl mx-auto space-y-6" id="resultsSections"></div>

    <!-- Action Buttons -->
    <div class="max-w-6xl mx-auto mt-8 flex flex-col sm:flex-row gap-4">
      <a href="{{ route('history.index') }}" class="flex-1 px-6 py-4 bg-[#000033] text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-[#000055] transform hover:scale-105 transition-all duration-300 shadow-lg glow-teal relative overflow-hidden group">
        <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
        <i data-lucide="arrow-left" style="width:20px;height:20px"></i>
        {{ __('history.backToHistory') }}
      </a>
      <button type="button" class="px-6 py-4 bg-[#4A7C87] text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-[#3A6C77] transform hover:scale-105 transition-all duration-300 shadow-lg glow-teal-sm relative overflow-hidden group" onclick="alert('Download feature - to be implemented')">
        <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
        <i data-lucide="download" style="width:20px;height:20px"></i>
        {{ __('results.downloadReport') }}
      </button>
    </div>

    <!-- Google Results Section (Server-side rendered) -->
    @if (!empty($google_results))
      <div class="max-w-6xl mx-auto glass-card rounded-2xl p-8 mt-10 animate-fade-in">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE]">
              Referensi Google Terkait
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Daftar halaman yang memiliki keterkaitan dengan data pada sertifikat ini.
            </p>
          </div>

          <span class="px-3 py-1 text-xs font-semibold rounded-full glass-button dark:bg-[#333334] text-[#B62A2D]">
            {{ count($google_results) }} hasil
          </span>
        </div>

        <div class="space-y-4">
          @foreach ($google_results as $index => $result)
            <div class="p-4 bg-white/50 dark:bg-[#333334]/60 backdrop-blur-sm rounded-xl border border-white/50 dark:border-gray-700 hover:border-[#B62A2D] hover:bg-white/70 dark:hover:bg-[#333334]/80 hover:shadow-md transition-all">
              <div class="flex items-start gap-3">
                <div class="mt-1 flex h-7 w-7 items-center justify-center rounded-full glass-button dark:bg-[#444445] text-xs font-semibold text-[#B62A2D]">
                  #{{ $index + 1 }}
                </div>

                <div class="flex-1">
                  <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-1">
                    Hasil #{{ $index + 1 }}
                  </p>

                  <a href="{{ $result['link'] }}" target="_blank"
                     class="text-base md:text-lg font-semibold text-[#B62A2D] hover:underline">
                    {{ $result['title'] }}
                  </a>

                  <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                    {{ $result['description'] }}
                  </p>

                  <p class="text-xs text-green-700 dark:text-green-400 mt-2 truncate">
                    {{ $result['link'] }}
                  </p>

                  <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                    Sumber: {{ parse_url($result['link'], PHP_URL_HOST) }}
                  </p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>

<!-- Footer Section - Seamlessly integrated with page background -->
@include('partials.footer')
</div>

{{-- Hide the default footer from layout since it's included above --}}
@section('hide_footer', true)

<script>
// Data from Laravel
const historyData = {
  overallStatus: '{{ $history->overall_status }}',
  confidence: {{ $history->confidence ?? 0 }},
  textMatching: @json($textMatching),
  metadata: @json($metadata),
  gamma: @json($gammaAnalysis),
  googleSearch: @json($google_results ? ['results' => array_map(function($r) { return ['title' => $r['title'], 'url' => $r['link'], 'snippet' => $r['description'], 'relevance' => 85]; }, $google_results)] : null),
};

function statusIcon(status) {
  if (status === 'verified') return '<i data-lucide="check-circle" class="text-green-500" style="width:48px;height:48px"></i>';
  if (status === 'not_verified') return '<i data-lucide="x-circle" class="text-red-500" style="width:48px;height:48px"></i>';
  if (status === 'suspicious') return '<i data-lucide="alert-triangle" class="text-yellow-500" style="width:48px;height:48px"></i>';
  return '';
}

function statusText(status) {
  if (status === 'verified') return '{{ __('results.verified') }}';
  if (status === 'not_verified') return '{{ __('results.notVerified') }}';
  if (status === 'suspicious') return '{{ __('results.suspicious') }}';
  return '';
}

function statusColor(status) {
  const greens = ['verified','match','valid','good','excellent','high','authentic','found'];
  const yellows = ['partial','warning'];
  const reds = ['not_verified','suspicious','mismatch','invalid','poor','low'];
  if (greens.includes(status)) return 'bg-green-500';
  if (yellows.includes(status)) return 'bg-yellow-500';
  if (reds.includes(status)) return 'bg-red-500';
  return 'bg-gray-500';
}

function statusLabel(code) {
  const map = {
    match: "{{ __('results.status.match') }}",
    valid: "{{ __('results.status.valid') }}",
    good: "{{ __('results.status.good') }}",
    excellent: "{{ __('results.status.excellent') }}",
    partial: "{{ __('results.status.partial') }}",
    warning: "{{ __('results.status.warning') }}",
    invalid: "{{ __('results.status.invalid') }}",
    poor: "{{ __('results.status.poor') }}",
    low: "{{ __('results.status.low') }}",
    mismatch: "{{ __('results.status.mismatch') }}",
    found: "{{ __('results.status.found') }}",
    high: "{{ __('results.status.high') }}",
  };
  return map[code] || String(code).toUpperCase();
}

// Score-based color function
function scoreColor(score) {
  if (score >= 80) return { stroke: '#22C55E', bg: 'rgba(34, 197, 94, 0.2)', text: '#22C55E' };
  if (score >= 70) return { stroke: '#EAB308', bg: 'rgba(234, 179, 8, 0.2)', text: '#EAB308' };
  return { stroke: '#EF4444', bg: 'rgba(239, 68, 68, 0.2)', text: '#EF4444' };
}

function scoreTextClass(score) {
  if (score >= 80) return 'text-green-500';
  if (score >= 70) return 'text-yellow-500';
  return 'text-red-500';
}

(function render() {
  const I18N = {
    fields: @json(__('results.fields')),
    meta: @json(__('results.meta')),
    gamma: @json(__('results.gammaParams')),
    summaries: @json(__('results.summaries')),
  };

  // Overall
  document.getElementById('overallStatusIcon').innerHTML = statusIcon(historyData.overallStatus);
  document.getElementById('overallStatusText').textContent = statusText(historyData.overallStatus);
  
  if (historyData.confidence) {
    const circumference = 352;
    const dash = (historyData.confidence / 100) * circumference;
    const circle = document.getElementById('confidenceCircle');
    const bgCircle = document.getElementById('confidenceBgCircle');
    const confColors = scoreColor(historyData.confidence);
    if (circle) {
      circle.setAttribute('stroke-dasharray', `${dash} ${circumference}`);
      circle.setAttribute('stroke', confColors.stroke);
      if (bgCircle) bgCircle.setAttribute('stroke', confColors.bg);
      const confValue = document.getElementById('confidenceValue');
      confValue.textContent = `${historyData.confidence}%`;
      confValue.style.color = confColors.text;
    }
  }

  const root = document.getElementById('resultsSections');
  if (!root) return;

  // Text Matching Section
  if (historyData.textMatching) {
    const tmScoreColor = historyData.textMatching.score ? scoreTextClass(historyData.textMatching.score) : '';
    const tm = document.createElement('div');
    tm.className = 'glass-card rounded-2xl p-8 animate-fade-in';
    tm.innerHTML = `
      <div class="flex items-start gap-4 mb-6">
        <div class="p-3 bg-[#B62A2D] bg-opacity-10 dark:bg-opacity-20 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg></div>
        <div class="flex-1">
          <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.textMatching') }}</h3>
          <p class="text-gray-600 dark:text-gray-400">{{ __('results.textMatchingDesc') }}</p>
        </div>
        ${historyData.textMatching.score ? `<div class="text-right"><p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('results.matchScore') }}</p><p class="text-3xl font-bold ${tmScoreColor}">${historyData.textMatching.score}%</p></div>` : ''}
      </div>
      <div class="space-y-3" id="tmDetails"></div>
      ${historyData.textMatching.summary ? `<p class="mt-6 text-gray-700 dark:text-gray-300 bg-blue-50/80 dark:bg-blue-900/30 backdrop-blur-sm p-4 rounded-lg border-l-4 border-[#B62A2D]">${historyData.textMatching.summary}</p>` : ''}
    `;
    root.appendChild(tm);
    
    if (historyData.textMatching.details) {
      const tmDetails = tm.querySelector('#tmDetails');
      historyData.textMatching.details.forEach(d => {
        const detailColor = scoreTextClass(d.match);
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between p-4 bg-white/50 dark:bg-[#333334]/60 backdrop-blur-sm rounded-lg';
        row.innerHTML = `
          <span class="font-medium text-gray-700 dark:text-gray-300">${I18N.fields[d.field] || d.field}</span>
          <div class="flex items-center gap-3">
            <div class="w-32 sm:w-48 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
              <div class="h-3 rounded-full ${statusColor(d.status)} transition-all duration-1000" style="width:${d.match}%"></div>
            </div>
            <span class="font-bold ${detailColor} w-12 text-right">${d.match}%</span>
          </div>
        `;
        tmDetails.appendChild(row);
      });
    }
  }

  // Metadata Section
  if (historyData.metadata && historyData.metadata.data) {
    const md = document.createElement('div');
    md.className = 'glass-card rounded-2xl p-8 animate-fade-in';
    md.innerHTML = `
      <div class="flex items-start gap-4 mb-6">
        <div class="p-3 bg-[#B62A2D] bg-opacity-10 dark:bg-opacity-20 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5V19A9 3 0 0 0 21 19V5"/><path d="M3 12A9 3 0 0 0 21 12"/></svg></div>
        <div class="flex-1">
          <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.metadata') }}</h3>
          <p class="text-gray-600 dark:text-gray-400">{{ __('results.metadataDesc') }}</p>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="mdData"></div>
      ${historyData.metadata.summary ? `<p class="mt-6 text-gray-700 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border-l-4 border-[#B62A2D]">${I18N.summaries.metadata}</p>` : ''}
    `;
    root.appendChild(md);
    
    const mdData = md.querySelector('#mdData');
    historyData.metadata.data.forEach(item => {
      const card = document.createElement('div');
      card.className = 'flex items-start justify-between p-4 bg-white/50 dark:bg-[#333334]/60 backdrop-blur-sm rounded-lg';
      card.innerHTML = `
        <div class="flex-1">
          <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">${I18N.meta[item.key] || item.key}</p>
          <p class="text-sm text-gray-600 dark:text-gray-400">${item.value}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold text-white ${statusColor(item.status)}">${statusLabel(item.status)}</span>
      `;
      mdData.appendChild(card);
    });
  }

  // Gamma Analysis Section
  if (historyData.gamma && historyData.gamma.analysis) {
    const gmScoreColor = historyData.gamma.score ? scoreTextClass(historyData.gamma.score) : '';
    const gm = document.createElement('div');
    gm.className = 'glass-card rounded-2xl p-8 animate-fade-in';
    gm.innerHTML = `
      <div class="flex items-start gap-4 mb-6">
        <div class="p-3 bg-[#B62A2D] bg-opacity-10 dark:bg-opacity-20 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
        <div class="flex-1">
          <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.gamma') }}</h3>
          <p class="text-gray-600 dark:text-gray-400">{{ __('results.gammaDesc') }}</p>
        </div>
        ${historyData.gamma.score ? `<div class="text-right"><p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('results.matchScore') }}</p><p class="text-3xl font-bold ${gmScoreColor}">${historyData.gamma.score}%</p></div>` : ''}
      </div>
      <div class="space-y-3" id="gmData"></div>
      ${historyData.gamma.summary ? `<p class="mt-6 text-gray-700 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border-l-4 border-[#B62A2D]">${I18N.summaries.gamma}</p>` : ''}
    `;
    root.appendChild(gm);
    
    const gmData = gm.querySelector('#gmData');
    historyData.gamma.analysis.forEach(item => {
      const row = document.createElement('div');
      row.className = 'flex items-center justify-between p-4 bg-gray-50 dark:bg-[#333334]/60 rounded-lg';
      row.innerHTML = `
        <div class="flex-1">
          <p class="font-semibold text-gray-700 dark:text-gray-300 mb-1">${I18N.gamma[item.parameter] || item.parameter}</p>
          <p class="text-sm text-gray-600 dark:text-gray-400">${item.value}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold text-white ${statusColor(item.status)}">${statusLabel(item.status)}</span>
      `;
      gmData.appendChild(row);
    });
  }

  // Google Search Section
  if (historyData.googleSearch && historyData.googleSearch.results && historyData.googleSearch.results.length > 0) {
    const gs = document.createElement('div');
    gs.className = 'glass-card rounded-2xl p-8 animate-fade-in';
    gs.innerHTML = `
      <div class="flex items-start gap-4 mb-6">
        <div class="p-3 bg-[#B62A2D] bg-opacity-10 dark:bg-opacity-20 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></div>
        <div class="flex-1">
          <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.googleSearch') }}</h3>
          <p class="text-gray-600 dark:text-gray-400">{{ __('results.googleSearchDesc') }}</p>
        </div>
      </div>
      <div class="space-y-4" id="gsList"></div>
      ${I18N.summaries.google ? `<p class="mt-6 text-gray-700 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border-l-4 border-[#B62A2D]">${I18N.summaries.google}</p>` : ''}
    `;
    root.appendChild(gs);
    
    const gsList = gs.querySelector('#gsList');
    historyData.googleSearch.results.forEach(r => {
      const card = document.createElement('div');
      card.className = 'p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-[#B62A2D] hover:shadow-md transition-all bg-white/50 dark:bg-[#333334]/60';
      card.innerHTML = `
        <div class="flex-1">
          <a href="${r.url}" target="_blank" class="font-semibold text-[#B62A2D] dark:text-red-400 mb-1 hover:underline">${r.title}</a>
          <p class="text-sm text-green-700 dark:text-green-400 mb-2">${r.url}</p>
          <p class="text-sm text-gray-600 dark:text-gray-400">${r.snippet}</p>
        </div>`;
      gsList.appendChild(card);
    });
  }

  if (window.lucide?.createIcons) window.lucide.createIcons();
})();
</script>
@endsection
