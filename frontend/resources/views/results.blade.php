@php
  // ==========================================
  // 1. PHP LOGIC
  // ==========================================
  
  // Determine if this is internal or external certificate
  $isInternal = ($certificate_type ?? 'external') === 'internal';
  
  // For Internal: Use internal_verified status
  // For External: Use final_score based status
  if ($isInternal) {
    if ($internal_verified ?? false) {
      $status = 'verified';
      $statusLabel = __('results.internalVerified');
      $statusColor = 'text-green-500';
      $statusBg = 'bg-green-500';
      $statusIcon = 'check-circle';
    } else {
      $status = 'notVerified';
      $statusLabel = __('results.internalNotVerified');
      $statusColor = 'text-red-500';
      $statusBg = 'bg-red-500';
      $statusIcon = 'x-circle';
    }
  } else {
    // External: Generate status based on final_score
    if ($final_score >= 75) {
      $status = 'verified';
      $statusLabel = __('results.verified');
      $statusColor = 'text-green-500';
      $statusBg = 'bg-green-500';
      $statusIcon = 'check-circle';
    } elseif ($final_score >= 50) {
      $status = 'suspicious';
      $statusLabel = __('results.suspicious');
      $statusColor = 'text-yellow-500';
      $statusBg = 'bg-yellow-500';
      $statusIcon = 'alert-triangle';
    } else {
      $status = 'notVerified';
      $statusLabel = __('results.notVerified');
      $statusColor = 'text-red-500';
      $statusBg = 'bg-red-500';
      $statusIcon = 'x-circle';
    }
  }
   
  // Count metrics (only for external)
  $textMatchCount = count($match_scores ?? []);
  $ocrCount = count($ocr_details ?? []);
  $googleCount = count($google_results ?? []);
   
  // Calculate average text score safely
  $avgTextScore = 0;
  if (is_array($match_scores) && count($match_scores) > 0) {
    $avgTextScore = array_sum($match_scores) / count($match_scores);
  }
   
  // Ensure arrays exist
  $google_results = $google_results ?? [];
  $match_scores = $match_scores ?? [];
  $ocr_details = $ocr_details ?? [];

  // Circle Animation Calculation for Blade
  $circumference = 352;
  $dashOffset = $circumference - (($final_score / 100) * $circumference);
@endphp

@extends('layouts.app')
@section('content')

<div class="min-h-screen pt-24 pb-0 relative overflow-hidden bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
  @include('components.animated-background', ['showWatermark' => false])

  <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
    <div class="mb-8">
      <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mb-4">
        @if(isset($isAdmin) && $isAdmin)
          <a href="{{ route('admin.dashboard') }}" class="hover:text-[#B62A2D] transition-colors">
            <i data-lucide="home" style="width:18px;height:18px"></i>
          </a>
          <span>/</span>
          <a href="{{ route('admin.history.all') }}" class="hover:text-[#B62A2D] transition-colors">{{ __('admin.allHistoryTitle') }}</a>
        @else
          <a href="/" class="hover:text-[#B62A2D] transition-colors">
            <i data-lucide="home" style="width:18px;height:18px"></i>
          </a>
          <span>/</span>
          <a href="/form" class="hover:text-[#B62A2D] transition-colors">{{ __('results.breadcrumbVerify') }}</a>
        @endif
        <span>/</span>
        <span class="font-medium">{{ __('results.title') }}</span>
      </div>
      
      <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
        <div class="lg:w-auto lg:flex-shrink-0">
          <h1 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ __('results.title') }}</h1>
          <p class="text-lg text-gray-700 dark:text-gray-300">{{ __('results.subtitle') }}</p>
        </div>
        
        <div class="lg:flex-1">
          <div class="glass-card-strong border-l-4 border-amber-500 rounded-xl p-5 animate-fade-in">
            <div class="flex items-start gap-4">
              <div class="flex-shrink-0 p-3 bg-amber-100/80 dark:bg-amber-900/40 backdrop-blur-sm rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600 dark:text-amber-400">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">
                  {{ __('results.disclaimerTitle') }}
                </h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                  {!! __('results.disclaimerDescription') !!}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="relative">
      <div class="flex gap-4">
        
        {{-- Left Navigation Widget - For both Internal and External certificates --}}
        <div class="hidden md:block flex-shrink-0">
          <div class="fixed left-4 lg:left-6 top-1/2 -translate-y-1/2 z-20">
            <div class="glass-card-strong rounded-2xl p-3 space-y-2" id="resultsNav">
              
              @if($isInternal)
              {{-- Internal Navigation Buttons (2 sections only) --}}
              <button type="button" data-slide-target="internal-status" class="results-nav-btn active w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full {{ $internal_verified ? 'bg-green-100 dark:bg-green-900/40' : 'bg-red-100 dark:bg-red-900/40' }} flex items-center justify-center group-hover:bg-opacity-80 transition-colors">
                  <i data-lucide="{{ $statusIcon }}" class="w-6 h-6 {{ $statusColor }}"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  {{ __('results.overallStatus') }}
                </div>
              </button>
              
              <button type="button" data-slide-target="internal-detail" class="results-nav-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full bg-[#B62A2D]/10 dark:bg-[#B62A2D]/20 flex items-center justify-center group-hover:bg-[#B62A2D]/20 dark:group-hover:bg-[#B62A2D]/30 transition-colors">
                  <i data-lucide="clipboard-check" class="w-6 h-6 text-[#B62A2D]"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  {{ __('results.verificationDetails') }}
                </div>
              </button>
              
              @else
              {{-- External Navigation Buttons --}}
              <button type="button" data-slide-target="overall" class="results-nav-btn active w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                  <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  {{ __('results.overallStatus') }}
                </div>
              </button>
              
              <button type="button" data-slide-target="ai" class="results-nav-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full bg-[#B62A2D]/10 dark:bg-[#B62A2D]/20 flex items-center justify-center group-hover:bg-[#B62A2D]/20 dark:group-hover:bg-[#B62A2D]/30 transition-colors">
                  <i data-lucide="sparkles" class="w-6 h-6 text-[#B62A2D]"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  {{ __('results.aiSummary') }}
                </div>
              </button>
              
              <button type="button" data-slide-target="text" class="results-nav-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full bg-[#B62A2D]/10 dark:bg-[#B62A2D]/20 flex items-center justify-center group-hover:bg-[#B62A2D]/20 dark:group-hover:bg-[#B62A2D]/30 transition-colors">
                  <i data-lucide="file-text" class="w-6 h-6 text-[#B62A2D]"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  {{ __('results.textMatching') }}
                </div>
              </button>
              
              <button type="button" data-slide-target="ocr" class="results-nav-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full bg-[#B62A2D]/10 dark:bg-[#B62A2D]/20 flex items-center justify-center group-hover:bg-[#B62A2D]/20 dark:group-hover:bg-[#B62A2D]/30 transition-colors">
                  <i data-lucide="eye" class="w-6 h-6 text-[#B62A2D]"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  OCR & Fonts
                </div>
              </button>
              
              <button type="button" data-slide-target="google" class="results-nav-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full bg-[#B62A2D]/10 dark:bg-[#B62A2D]/20 flex items-center justify-center group-hover:bg-[#B62A2D]/20 dark:group-hover:bg-[#B62A2D]/30 transition-colors">
                  <i data-lucide="search" class="w-6 h-6 text-[#B62A2D]"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  {{ __('results.googleSearch') }}
                </div>
              </button>
              
              {{-- View Certificate File Button (External only) --}}
              @if(!empty($certificate->berkas))
              <button type="button" id="viewCertificateBtn" class="results-nav-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative">
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                  <i data-lucide="file-scan" class="w-6 h-6 text-blue-500"></i>
                </div>
                <div class="absolute left-full ml-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                  {{ __('results.viewCertificate') }}
                </div>
              </button>
              @endif
              @endif
            </div>
          </div>
        </div>

        {{-- Mobile Navigation - For both Internal and External --}}
        <div class="md:hidden fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-full px-4">
          <div class="glass-card-strong rounded-full px-3 py-2.5 flex items-center justify-center gap-2 sm:gap-3 shadow-2xl overflow-x-auto" id="resultsNavMobile">
            @if($isInternal)
            {{-- Internal Mobile Navigation (2 sections only) --}}
            <button type="button" data-slide-target="internal-status" class="results-nav-btn-mobile active w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 flex-shrink-0">
              <i data-lucide="{{ $statusIcon }}" class="w-4 h-4 sm:w-5 sm:h-5 {{ $statusColor }}"></i>
            </button>
            <button type="button" data-slide-target="internal-detail" class="results-nav-btn-mobile w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 flex-shrink-0">
              <i data-lucide="clipboard-check" class="w-4 h-4 sm:w-5 sm:h-5 text-[#B62A2D]"></i>
            </button>
            @else
            {{-- External Mobile Navigation --}}
            <button type="button" data-slide-target="overall" class="results-nav-btn-mobile active w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 flex-shrink-0">
              <i data-lucide="check-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-green-500"></i>
            </button>
            <button type="button" data-slide-target="ai" class="results-nav-btn-mobile w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 flex-shrink-0">
              <i data-lucide="sparkles" class="w-4 h-4 sm:w-5 sm:h-5 text-[#B62A2D]"></i>
            </button>
            <button type="button" data-slide-target="text" class="results-nav-btn-mobile w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 flex-shrink-0">
              <i data-lucide="file-text" class="w-4 h-4 sm:w-5 sm:h-5 text-[#B62A2D]"></i>
            </button>
            <button type="button" data-slide-target="ocr" class="results-nav-btn-mobile w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 flex-shrink-0">
              <i data-lucide="eye" class="w-4 h-4 sm:w-5 sm:h-5 text-[#B62A2D]"></i>
            </button>
            <button type="button" data-slide-target="google" class="results-nav-btn-mobile w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 flex-shrink-0">
              <i data-lucide="search" class="w-4 h-4 sm:w-5 sm:h-5 text-[#B62A2D]"></i>
            </button>
            {{-- View Certificate File Button (Mobile, External only) --}}
            @if(!empty($certificate->berkas))
            <button type="button" id="viewCertificateBtnMobile" class="results-nav-btn-mobile w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300 bg-blue-100 dark:bg-blue-900/40 flex-shrink-0">
              <i data-lucide="file-scan" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500"></i>
            </button>
            @endif
            @endif
          </div>
        </div>

        <div class="flex-1 min-w-0 pb-24 md:pb-0">
          
          @if($isInternal)
          {{-- ========================================== --}}
          {{-- INTERNAL CERTIFICATE RESULTS (Simplified) --}}
          {{-- ========================================== --}}
          <div class="results-slider-container relative overflow-visible md:overflow-hidden">
            
            {{-- Section 1: Status Keseluruhan --}}
            <div class="results-slide active" data-slide="internal-status">
              <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
                {{-- Status Header --}}
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                  <div class="flex items-center gap-6">
                    <div class="p-4 rounded-full {{ $internal_verified ? 'bg-green-100 dark:bg-green-900/40' : 'bg-red-100 dark:bg-red-900/40' }}">
                      <i data-lucide="{{ $statusIcon }}" class="{{ $statusColor }}" style="width:48px;height:48px"></i>
                    </div>
                    <div>
                      <h2 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.internalVerificationTitle') }}</h2>
                      <p class="text-3xl font-extrabold {{ $statusColor }}">{{ $statusLabel }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Section 2: Detail Verifikasi (Combined) --}}
            <div class="results-slide" data-slide="internal-detail">
              <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
                <div class="flex items-start gap-4 mb-6">
                  <div class="p-3 bg-[#B62A2D] rounded-lg">
                    <i data-lucide="clipboard-check" class="w-8 h-8 text-white"></i>
                  </div>
                  <div>
                    <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-1">{{ __('results.verificationDetails') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('results.internalVerificationDesc') }}</p>
                  </div>
                </div>
                
                {{-- Comparison Layout: Desktop side-by-side, Mobile vertical --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                  {{-- Left: Data yang Dimasukkan --}}
                  <div class="bg-gray-50 dark:bg-[#333334] rounded-xl p-5">
                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4 flex items-center gap-2">
                      <i data-lucide="edit-3" class="w-4 h-4"></i>
                      {{ __('results.submittedData') }}
                    </h4>
                    <div class="space-y-3">
                      <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('form.name') }}</p>
                        <p class="font-semibold text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->nama }}</p>
                      </div>
                      <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('form.nim') }}</p>
                        <p class="font-semibold text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->nim }}</p>
                      </div>
                    </div>
                  </div>

                  {{-- Right: Hasil Pencocokan --}}
                  <div class="{{ $internal_verified ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' }} rounded-xl p-5">
                    <h4 class="text-sm font-bold {{ $internal_verified ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} uppercase mb-4 flex items-center gap-2">
                      <i data-lucide="database" class="w-4 h-4"></i>
                      {{ __('results.databaseMatch') }}
                    </h4>
                    
                    @if($internal_participant_data)
                    <div class="space-y-3">
                      {{-- NIM Match --}}
                      <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-black/20 rounded-lg">
                        <div>
                          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('form.nim') }}</p>
                          <p class="font-semibold {{ $internal_verified ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">{{ $internal_participant_data['nim'] }}</p>
                        </div>
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
                      </div>
                      {{-- Name Match --}}
                      @php
                        $nameMatch = strtolower(trim($certificate->nama)) === strtolower(trim($internal_participant_data['name']));
                        $similarity = 0;
                        if (!$nameMatch) {
                          similar_text(strtolower($certificate->nama), strtolower($internal_participant_data['name']), $similarity);
                        }
                      @endphp
                      <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-black/20 rounded-lg">
                        <div>
                          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('form.name') }}</p>
                          <p class="font-semibold {{ $nameMatch ? 'text-green-800 dark:text-green-300' : 'text-amber-700 dark:text-amber-400' }}">{{ $internal_participant_data['name'] }}</p>
                          @if(!$nameMatch)
                          <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">{{ __('results.nameMismatch') }}</p>
                          @endif
                        </div>
                        @if($nameMatch)
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
                        @else
                        <i data-lucide="alert-circle" class="w-6 h-6 text-amber-500"></i>
                        @endif
                      </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                      <i data-lucide="x-circle" class="w-10 h-10 text-red-500 mx-auto mb-2"></i>
                      <p class="text-red-700 dark:text-red-400 font-semibold">{{ __('results.noDataFound') }}</p>
                      <p class="text-red-600 dark:text-red-500 text-sm mt-1">{{ __('results.noDataFoundDesc') }}</p>
                    </div>
                    @endif
                  </div>
                </div>

                {{-- Event Info (non-editable, auto-filled) --}}
                <div class="bg-gray-100 dark:bg-[#2a2a2b] rounded-xl p-5">
                  <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    {{ __('results.eventInfo') ?? 'Informasi Kegiatan' }}
                  </h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('form.eventName') }}</p>
                      <p class="font-semibold text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->nama_kegiatan }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('form.organizer') }}</p>
                      <p class="font-semibold text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->penyelenggara }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
          </div>

          @else
          {{-- ========================================== --}}
          {{-- EXTERNAL CERTIFICATE RESULTS (Full)       --}}
          {{-- ========================================== --}}
          <div class="results-slider-container relative overflow-visible md:overflow-hidden">
            
            <div class="results-slide active" data-slide="overall">
              <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                  <div class="flex items-center gap-6">
                    <div>
                        <i data-lucide="{{ $statusIcon }}" class="{{ $statusColor }}" style="width:48px;height:48px"></i>
                    </div>
                    <div>
                      <h2 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('results.overallStatus') }}</h2>
                      <p class="text-3xl font-extrabold {{ $statusColor }}">{{ $statusLabel }}</p>
                    </div>
                  </div>
                  <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ __('results.confidenceLevel') }}</p>
                    <div class="relative w-32 h-32">
                      <svg class="transform -rotate-90 w-32 h-32 drop-shadow-lg">
                        <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="12" fill="none" class="text-gray-200 dark:text-[#3D3D3E]"></circle>
                        <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="12" fill="none" 
                                stroke-dasharray="352" 
                                stroke-dashoffset="{{ $dashOffset }}" 
                                stroke-linecap="round" 
                                class="transition-all duration-1000 text-green-500"></circle>
                      </svg>
                      <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-3xl font-bold {{ $statusColor }}">{{ round($final_score) }}%</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="mt-8 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-[#333334] rounded-xl text-center">
                        <p class="text-2xl font-bold text-green-500">{{ round($avgTextScore, 1) }}%</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Text Matching</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-[#333334] rounded-xl text-center">
                        <p class="text-2xl font-bold text-blue-500">{{ $googleCount }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Google Results</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-[#333334] rounded-xl text-center">
                        <p class="text-2xl font-bold text-amber-500">{{ $ocrCount }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">OCR Segments</p>
                    </div>
                </div>
                
                {{-- Admin Status Update Panel (Only for admin viewing suspicious certificates) --}}
                @if(isset($isAdmin) && $isAdmin && $status === 'suspicious')
                <div class="mt-8 p-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl" id="adminStatusPanel">
                  <div class="flex items-start gap-4 mb-4">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/40 rounded-lg">
                      <i data-lucide="shield-check" class="w-6 h-6 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div class="flex-1">
                      <h4 class="text-lg font-bold text-amber-800 dark:text-amber-300 mb-1">{{ __('results.adminReviewTitle') }}</h4>
                      <p class="text-sm text-amber-700 dark:text-amber-400">{{ __('results.adminReviewDesc') }}</p>
                    </div>
                  </div>
                  
                  {{-- Notes textarea --}}
                  <div class="mb-4">
                    <label for="adminNotes" class="block text-sm font-medium text-amber-800 dark:text-amber-300 mb-2">
                      {{ __('results.adminNotes') }} <span class="text-amber-600 dark:text-amber-500">({{ __('results.optional') }})</span>
                    </label>
                    <textarea 
                      id="adminNotes" 
                      rows="3" 
                      class="w-full px-4 py-3 bg-white dark:bg-[#2D2D2E] border border-amber-200 dark:border-amber-700 rounded-xl text-[#222223] dark:text-[#FEFEFE] placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none transition-colors"
                      placeholder="{{ __('results.adminNotesPlaceholder') }}"
                    ></textarea>
                  </div>
                  
                  {{-- Action buttons --}}
                  <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                      type="button" 
                      id="markVerifiedBtn"
                      class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors"
                    >
                      <i data-lucide="check-circle" class="w-5 h-5"></i>
                      {{ __('results.markAsVerified') }}
                    </button>
                    <button 
                      type="button" 
                      id="markNotVerifiedBtn"
                      class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-colors"
                    >
                      <i data-lucide="x-circle" class="w-5 h-5"></i>
                      {{ __('results.markAsNotVerified') }}
                    </button>
                  </div>
                </div>
                @endif
              </div>
            </div>

            <div class="results-slide" data-slide="ai">
              <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
                <div class="flex items-start gap-4 mb-6">
                    <div class="p-3 bg-[#B62A2D] rounded-lg">
                        <i data-lucide="sparkles" class="w-8 h-8 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-1">{{ __('results.aiVerification') }}</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ __('results.aiVerificationDesc') }}</p>
                    </div>
                </div>
                
                @if(isset($verifikasi_ai))
                <div class="text-gray-800 dark:text-gray-200 leading-relaxed bg-gray-50 dark:bg-[#333334] p-6 rounded-xl border-l-4 border-green-500">
                    {!! nl2br(e($verifikasi_ai)) !!}
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <p>Analisis AI tidak tersedia.</p>
                </div>
                @endif
              </div>
            </div>

            <div class="results-slide" data-slide="text">
              <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
                <div class="flex items-start gap-4 mb-6">
                    <div class="p-3 bg-[#B62A2D] rounded-lg">
                        <i data-lucide="file-text" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">Text Matching</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('results.dataMatching') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('results.similarityScore') }}</p>
                        <p class="text-3xl font-bold text-green-500">{{ round($avgTextScore, 1) }}%</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($match_scores as $field => $score)
                        @php
                            $colorClass = $score >= 90 ? 'text-green-500' : ($score >= 75 ? 'text-yellow-500' : 'text-red-500');
                            $bgClass = $score >= 90 ? 'bg-green-500' : ($score >= 75 ? 'bg-yellow-500' : 'bg-red-500');
                            $fieldKey = strtolower($field);
                        @endphp
                        <div class="flex flex-col p-4 bg-gray-50 dark:bg-[#333334] rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ __('fields.' . $fieldKey) }}</span>
                                <span class="font-bold {{ $colorClass }}">{{ round($score, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $bgClass }} transition-all duration-1000" style="width: {{ $score }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
              </div>
            </div>

            <div class="results-slide" data-slide="ocr">
              <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
                <div class="flex items-start gap-4 mb-6">
                    <div class="p-3 bg-[#B62A2D] rounded-lg">
                        <i data-lucide="eye" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">OCR & Font Analysis</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('results.ocrAnalysisDesc') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-[#B62A2D]/10 text-[#B62A2D] dark:bg-[#B62A2D]/20 dark:text-[#B62A2D]">
                            {{ count($ocr_details ?? []) }} {{ __('results.segments') }}
                        </span>
                    </div>
                </div>

                @if(!empty($ocr_details))
                    <div class="space-y-6 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach ($ocr_details as $index => $item)
                        {{-- Filter: Skip if accuracy low or no font data --}}
                        @php
                            $font = $item['font'] ?? null;
                            $fontClass = trim($font['class'] ?? '');
                            $fontConfidence = (float) ($font['confidence'] ?? 0);
                            $ocrAccuracy = (float) ($item['accuracy'] ?? 0);
                            $fontStatus = $font['status'] ?? null;
                        @endphp
                        
                        @if (
                            !is_array($font) ||
                            $fontClass === '' ||
                            $fontStatus === 'unknown' ||
                            $ocrAccuracy < 0.3
                        )
                            @continue
                    @endif


                        <div class="bg-gray-50 dark:bg-[#333334] rounded-xl p-5 border border-gray-200 dark:border-gray-700 transition-all hover:shadow-md">
                            
                            {{-- Item Header: Segment & Accuracy --}}
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-gray-600">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Segment #{{ $index + 1 }}
                                </span>
                                @if(isset($item['accuracy']))
                                    @php
                                        $accVal = $item['accuracy'];
                                        $accDisplay = $accVal > 1 ? $accVal : $accVal * 100;
                                        $accColor = $accDisplay >= 90 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 
                                                   ($accDisplay >= 75 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                                   'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400');
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md {{ $accColor }}">
                                        OCR Accuracy: {{ round($accDisplay, 1) }}%
                                    </span>
                                @endif
                            </div>

                            {{-- TrOCR Result Box --}}
                            @if(!empty($item['trocr']))
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 mb-1.5 ml-1">Detected Text</p>
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                    <p class="font-mono text-base text-gray-800 dark:text-gray-200 break-words leading-relaxed">
                                        {{ $item['trocr'] }}
                                    </p>
                                </div>
                            </div>
                            @endif

                            {{-- Font Analysis Section --}}
                            @if(!empty($item['font']) && is_array($item['font']))
                                @php
                                    $fontConf = ($item['font']['confidence'] ?? 0) * 100;
                                    $fontStatus = $item['font']['status'] ?? 'unknown';
                                    
                                    // Logic Warna Status & Icon
                                    $statusConfig = match($fontStatus) {
                                        'match'    => ['color' => 'text-green-600 dark:text-green-400', 'bg' => 'bg-green-100 dark:bg-green-900/30', 'border' => 'border-green-200 dark:border-green-800', 'label' => 'MATCH'],
                                        'mismatch' => ['color' => 'text-red-600 dark:text-red-400', 'bg' => 'bg-red-100 dark:bg-red-900/30', 'border' => 'border-red-200 dark:border-red-800', 'label' => 'MISMATCH'],
                                        'new'      => ['color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-100 dark:bg-blue-900/30', 'border' => 'border-blue-200 dark:border-blue-800', 'label' => 'NEW FONT'],
                                        default    => ['color' => 'text-gray-600 dark:text-gray-400', 'bg' => 'bg-gray-100 dark:bg-gray-700', 'border' => 'border-gray-200 dark:border-gray-600', 'label' => strtoupper($fontStatus)]
                                    };
                                @endphp

                                <div class="bg-gray-100 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-3 tracking-wide">Font Analysis</p>
                                    
                                    <!-- {{-- Reference Font (Database) --}}
                                    @if (!empty($item['font']['reference_font']))
                                    <div class="flex items-center gap-3 mb-4 p-3 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">
                                        <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded">
                                            <i data-lucide="database" class="w-4 h-4 text-gray-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-500 uppercase">Reference Font</p>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $item['font']['reference_font'] }}</p>
                                        </div>
                                    </div>
                                    @endif -->

                                    <div class="flex flex-col md:flex-row gap-4 justify-between">
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 mb-1">Detected Class</p>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-lg font-bold text-[#222223] dark:text-[#FEFEFE]">
                                                    {{ $item['font']['class'] ?? '-' }}
                                                </span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded border {{ $statusConfig['bg'] }} {{ $statusConfig['color'] }} {{ $statusConfig['border'] }}">
                                                    {{ $statusConfig['label'] }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ isset($item['font']['google_font']) ? (is_array($item['font']['google_font']) ? implode(', ', $item['font']['google_font']) : $item['font']['google_font']) : '-' }}">
                                                Google Font: {{ isset($item['font']['google_font']) ? (is_array($item['font']['google_font']) ? implode(', ', $item['font']['google_font']) : $item['font']['google_font']) : '-' }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-3 px-4 py-2 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm min-w-[140px]">
                                            @if($fontConf >= 80)
                                                <i data-lucide="check-circle" class="text-green-500 w-8 h-8"></i>
                                            @elseif($fontConf >= 60)
                                                <i data-lucide="alert-circle" class="text-yellow-500 w-8 h-8"></i>
                                            @else
                                                <i data-lucide="alert-triangle" class="text-red-500 w-8 h-8"></i>
                                            @endif
                                            <div>
                                                <p class="text-xs text-gray-500">Confidence</p>
                                                <p class="text-lg font-bold {{ $fontConf >= 80 ? 'text-green-500' : ($fontConf >= 60 ? 'text-yellow-500' : 'text-red-500') }}">
                                                    {{ round($fontConf, 1) }}%
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-full mb-3">
                            <i data-lucide="file-x" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-600 dark:text-gray-300">{{ __('results.noOcrData') }}</p>
                        <p class="text-sm">{{ __('results.noOcrDataDesc') }}</p>
                    </div>
                @endif
              </div>
            </div>

            <div class="results-slide" data-slide="google">
              <div class="glass-card-strong rounded-2xl p-8 animate-fade-in">
                <div class="flex items-start gap-4 mb-6">
                    <div class="p-3 bg-[#B62A2D] rounded-lg">
                        <i data-lucide="search" class="w-8 h-8 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">Google Search Results</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('results.googleReferencesDesc') }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-[#B62A2D]/10 text-[#B62A2D]">{{ count($google_results) }} {{ __('results.resultsCount') }}</span>
                </div>

                <div class="space-y-4 max-h-[600px] overflow-y-auto custom-scrollbar">
                    @foreach ($google_results as $index => $result)
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-[#B62A2D] hover:shadow-md transition-all bg-white dark:bg-[#333334]">
                        <a href="{{ $result['link'] }}" target="_blank" class="block font-semibold text-[#B62A2D] dark:text-red-400 mb-1 hover:underline text-lg">
                            {{ $result['title'] }}
                        </a>
                        <p class="text-xs text-green-700 dark:text-green-400 mb-2 truncate">{{ $result['link'] }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                            {{ $result['description'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
              </div>
            </div>

          </div>

          <div class="mt-8 mb-8 flex justify-center">
            <div class="glass-card-strong border-l-4 border-amber-500 rounded-xl p-4 animate-fade-in max-w-xl">
              <div class="flex items-center gap-3">
                <i data-lucide="alert-triangle" class="text-amber-600 dark:text-amber-400 w-5 h-5 flex-shrink-0"></i>
                <span class="text-amber-800 dark:text-amber-300 font-medium text-sm">{{ __('results.disclaimerWarning') }}</span>
              </div>
            </div>
          </div>
          @endif
          {{-- End of Internal/External conditional --}}
        </div>

      </div>
    </div>

    {{-- Right Floating Buttons (Desktop) --}}
    <div class="hidden md:block fixed right-4 lg:right-6 top-1/2 -translate-y-1/2 z-20">
      <div class="glass-card-strong rounded-2xl p-3 space-y-2" id="resultsActions">
        {{-- Back Button (History) --}}
        <button type="button" onclick="history.back()" class="results-action-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative" title="{{ __('results.backButton') }}">
          <div class="w-10 h-10 rounded-full bg-[#222223]/10 dark:bg-[#FEFEFE]/20 flex items-center justify-center group-hover:bg-[#222223]/20 dark:group-hover:bg-[#FEFEFE]/30 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6 text-[#222223] dark:text-[#FEFEFE]"></i>
          </div>
          <div class="absolute right-full mr-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            {{ __('results.backButton') }}
          </div>
        </button>

        {{-- Restart Button (New Verification) --}}
        <a href="/form" class="results-action-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative" title="{{ __('results.restartButton') }}">
          <div class="w-10 h-10 rounded-full bg-[#B62A2D]/10 dark:bg-[#B62A2D]/20 flex items-center justify-center group-hover:bg-[#B62A2D]/20 dark:group-hover:bg-[#B62A2D]/30 transition-colors">
            <i data-lucide="refresh-cw" class="w-6 h-6 text-[#B62A2D]"></i>
          </div>
          <div class="absolute right-full mr-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            {{ __('results.restartButton') }}
          </div>
        </a>
        
        {{-- Download Button (Only for External) --}}
        @if(!$isInternal)
        <button type="button" onclick="window.print()" class="results-action-btn w-14 h-14 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group relative" title="{{ __('results.downloadReport') }}">
          <div class="w-10 h-10 rounded-full bg-[#B62A2D]/10 dark:bg-[#B62A2D]/20 flex items-center justify-center group-hover:bg-[#B62A2D]/20 dark:group-hover:bg-[#B62A2D]/30 transition-colors">
            <i data-lucide="download" class="w-6 h-6 text-[#B62A2D]"></i>
          </div>
          <div class="absolute right-full mr-3 px-3 py-1.5 bg-[#222223] dark:bg-[#FEFEFE] text-white dark:text-[#222223] text-sm font-medium rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            {{ __('results.downloadReport') }}
          </div>
        </button>
        @endif
      </div>
    </div>

    {{-- Mobile Floating Buttons --}}
    <div class="md:hidden fixed bottom-6 right-4 z-[60] flex flex-col gap-3">
      {{-- Back Button --}}
      <button type="button" onclick="history.back()" class="w-14 h-14 rounded-full flex items-center justify-center shadow-xl bg-white dark:bg-[#444445] border-2 border-gray-200 dark:border-gray-600 hover:scale-110 transition-transform active:scale-95">
        <i data-lucide="arrow-left" class="w-6 h-6 text-[#222223] dark:text-[#FEFEFE] pointer-events-none"></i>
      </button>
      {{-- Restart Button --}}
      <a href="/form" class="w-14 h-14 rounded-full flex items-center justify-center shadow-xl bg-white dark:bg-[#444445] border-2 border-gray-200 dark:border-gray-600 hover:scale-110 transition-transform active:scale-95">
        <i data-lucide="refresh-cw" class="w-6 h-6 text-[#B62A2D] pointer-events-none"></i>
      </a>
      {{-- Download Button (Only for External) --}}
      @if(!$isInternal)
      <button type="button" onclick="window.print()" class="w-14 h-14 rounded-full flex items-center justify-center shadow-xl bg-[#B62A2D] border-2 border-[#B62A2D] hover:scale-110 transition-transform active:scale-95">
        <i data-lucide="download" class="w-6 h-6 text-white pointer-events-none"></i>
      </button>
      @endif
    </div>

    {{-- Certificate View Modal with Zoom/Drag --}}
    @if(!$isInternal && !empty($certificate->berkas))
    <div id="certificateModal" class="fixed inset-0 z-[9999] hidden items-center justify-center" role="dialog" aria-modal="true">
      {{-- Overlay that covers EVERYTHING including header --}}
      <div id="certificateModalOverlay" class="fixed inset-0 bg-black/80 backdrop-blur-md"></div>
      
      {{-- Modal Content --}}
      <div class="relative z-10 w-full h-full flex flex-col bg-white dark:bg-[#222223] overflow-hidden shadow-2xl">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#333334]">
          <h3 class="text-lg font-semibold text-[#222223] dark:text-[#FEFEFE] flex items-center gap-2">
            <i data-lucide="file-scan" class="w-5 h-5 text-blue-500"></i>
            <span class="hidden sm:inline">{{ __('results.viewCertificate') }}</span>
            <span class="sm:hidden">{{ __('results.viewCertificate') }}</span>
          </h3>
          <div class="flex items-center gap-1 sm:gap-2">
            {{-- Zoom Controls --}}
            <button type="button" id="zoomOutBtn" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" title="Zoom Out">
              <i data-lucide="zoom-out" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
            </button>
            <span id="zoomLevel" class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-[3rem] text-center">100%</span>
            <button type="button" id="zoomInBtn" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" title="Zoom In">
              <i data-lucide="zoom-in" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
            </button>
            <button type="button" id="zoomResetBtn" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" title="Reset">
              <i data-lucide="maximize-2" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
            </button>
            <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
            <button type="button" id="closeCertificateModal" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
              <i data-lucide="x" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
            </button>
          </div>
        </div>
        
        {{-- Mobile Tab Buttons (only visible on mobile) --}}
        <div class="lg:hidden flex border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#2a2a2b]">
          <button type="button" id="tabCertificateBtn" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium transition-colors border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 bg-white dark:bg-[#222223]">
            <i data-lucide="image" class="w-4 h-4"></i>
            {{ __('results.tabCertificate') ?? 'Sertifikat' }}
          </button>
          <button type="button" id="tabDataBtn" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium transition-colors border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            <i data-lucide="clipboard-list" class="w-4 h-4"></i>
            {{ __('results.tabData') ?? 'Data' }}
          </button>
        </div>
        
        {{-- Body: Image Left/Top, Data Right/Bottom --}}
        <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
          {{-- Certificate Image Viewer --}}
          <div id="certificateImagePanel" class="flex-1 lg:w-2/3 bg-gray-100 dark:bg-[#1a1a1b] overflow-hidden relative" id="imageViewerContainer">
            <div id="imageLoader" class="absolute inset-0 flex items-center justify-center">
              <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
            </div>
            <div id="imageWrapper" class="w-full h-full flex items-center justify-center cursor-grab active:cursor-grabbing overflow-hidden" style="min-height: 300px;">
              <img id="certificateImage" src="" alt="Certificate" class="max-w-none select-none" style="transform-origin: center center;" draggable="false" />
              {{-- PDF fallback --}}
              <iframe id="certificatePdf" src="" class="hidden w-full h-full" style="min-height: 500px;"></iframe>
            </div>
          </div>
          
          {{-- Form Data Panel (hidden on mobile by default, shown on desktop) --}}
          <div id="certificateDataPanel" class="hidden lg:block lg:w-1/3 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700 overflow-y-auto bg-white dark:bg-[#222223]">
            <div class="p-4 md:p-6 space-y-4">
              <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                {{ __('results.submittedData') ?? 'Data yang Dimasukkan' }}
              </h4>
              
              <div class="space-y-3">
                {{-- Name --}}
                <div class="p-3 bg-gray-50 dark:bg-[#333334] rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('form.name') }}</p>
                  <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->nama ?? '-' }}</p>
                </div>
                
                {{-- Event Name --}}
                <div class="p-3 bg-gray-50 dark:bg-[#333334] rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('form.eventName') }}</p>
                  <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->nama_kegiatan ?? '-' }}</p>
                </div>
                
                {{-- Organizer --}}
                <div class="p-3 bg-gray-50 dark:bg-[#333334] rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('form.organizer') }}</p>
                  <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->penyelenggara ?? '-' }}</p>
                </div>
                
                {{-- Date --}}
                <div class="p-3 bg-gray-50 dark:bg-[#333334] rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('form.eventDate') }}</p>
                  <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">
                    @if($certificate->tanggal_kegiatan)
                      {{ \Carbon\Carbon::parse($certificate->tanggal_kegiatan)->format('d M Y') }}
                    @elseif($certificate->tanggal_mulai && $certificate->tanggal_selesai)
                      {{ \Carbon\Carbon::parse($certificate->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($certificate->tanggal_selesai)->format('d M Y') }}
                    @else
                      -
                    @endif
                  </p>
                </div>
                
                {{-- Academic Year --}}
                <div class="p-3 bg-gray-50 dark:bg-[#333334] rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('form.academicYear') }}</p>
                  <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->tahun_akademik ?? '-' }}</p>
                </div>
                
                {{-- Status --}}
                <div class="p-3 bg-gray-50 dark:bg-[#333334] rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('results.status') }}</p>
                  <div class="flex items-center gap-2">
                    @if($status === 'verified')
                      <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm font-medium rounded-full">{{ $statusLabel }}</span>
                    @elseif($status === 'suspicious')
                      <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-sm font-medium rounded-full">{{ $statusLabel }}</span>
                    @else
                      <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm font-medium rounded-full">{{ $statusLabel }}</span>
                    @endif
                    <span class="text-sm text-gray-600 dark:text-gray-400">({{ round($final_score) }}%)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    @include('partials.footer')
  </div>
</div>

<div id="printContainer" class="print-only hidden">
  <div class="print-page">
    <div class="print-header">
      <div class="print-logo">
        <img src="/assets/logo-autentik.png" alt="Autentik Logo" class="print-logo-img" />
        <div class="print-brand">
          <span class="print-brand-name">AUTENTIK</span>
          <span class="print-brand-tagline">Platform Verifikasi Kredensial</span>
        </div>
      </div>
      <div class="print-date">
        {{ now()->translatedFormat('l, d F Y H:i') }}
      </div>
    </div>

    <div class="print-title-section">
      <h1 class="print-title">{{ __('results.title') }}</h1>
      <p class="print-subtitle">{{ __('results.subtitle') }}</p>
    </div>

    <div class="print-section">
      <div class="print-section-header">
        <span class="print-section-icon">✓</span>
        <h2>{{ __('results.overallStatus') }}</h2>
      </div>
      <div class="print-section-content">
        <p><strong>Status:</strong> <span class="{{ $status == 'verified' ? 'print-status-verified' : ($status == 'suspicious' ? 'print-status-warning' : 'print-status-error') }}">{{ $statusLabel }}</span></p>
        <p><strong>{{ __('results.confidence') }}:</strong> <span class="print-score">{{ round($final_score) }}%</span></p>
      </div>
    </div>

    <div class="print-section">
      <div class="print-section-header">
        <span class="print-section-icon">✦</span>
        <h2>{{ __('results.aiVerification') }}</h2>
      </div>
      <div class="print-section-content">
         <p>{!! nl2br(e($verifikasi_ai ?? __('print.noData'))) !!}</p>
      </div>
    </div>

    <div class="print-section">
      <div class="print-section-header">
        <span class="print-section-icon">📄</span>
        <h2>Text Matching</h2>
      </div>
      <div class="print-section-content">
        <p><strong>{{ __('print.averageScore') }}:</strong> <span class="print-score">{{ round($avgTextScore, 1) }}%</span></p>
        <table>
          <thead><tr><th>{{ __('print.matchField') }}</th><th>{{ __('print.matchRate') }}</th></tr></thead>
          <tbody>
            @foreach($match_scores as $field => $score)
            <tr>
                <td>{{ __('fields.' . strtolower($field)) }}</td>
                <td>{{ round($score) }}%</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="print-section">
      <div class="print-section-header">
        <span class="print-section-icon">🔍</span>
        <h2>Google Search</h2>
      </div>
      <div class="print-section-content">
        <p><strong>{{ __('print.resultsFound') }}:</strong> {{ count($google_results) }}</p>
        @foreach ($google_results as $index => $result)
        <div style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
            <p style="font-weight: bold; margin:0;">{{ $index + 1 }}. {{ $result['title'] }}</p>
            <p style="font-size: 10px; color: #666; margin:0;">{{ $result['link'] }}</p>
        </div>
        @endforeach
      </div>
    </div>

    <div class="print-footer">
      <p>Laporan ini dibuat oleh sistem AUTENTIK</p>
    </div>
  </div>
</div>

{{-- Certificate viewer removed - now opens directly in new tab --}}

{{-- Admin Status Update Confirmation Modal --}}
@if(isset($isAdmin) && $isAdmin && !$isInternal && $status === 'suspicious')
<div id="statusConfirmModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="confirm-modal-title" role="dialog" aria-modal="true">
  <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
    {{-- Background overlay --}}
    <div id="confirmModalOverlay" class="fixed inset-0 bg-gray-500/75 dark:bg-black/80 backdrop-blur-sm transition-opacity z-10"></div>

    {{-- Modal panel - z-20 to be above overlay --}}
    <div class="relative z-20 inline-block align-bottom bg-white dark:bg-[#2D2D2E] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      {{-- Header --}}
      <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
          <div id="confirmIconContainer" class="p-3 rounded-xl">
            <i id="confirmIcon" class="w-6 h-6"></i>
          </div>
          <h3 id="confirm-modal-title" class="text-xl font-bold text-[#222223] dark:text-[#FEFEFE]"></h3>
        </div>
      </div>
      
      {{-- Content --}}
      <div class="px-6 py-5">
        <p id="confirmMessage" class="text-gray-700 dark:text-gray-300 mb-4"></p>
        
        <div id="confirmNotesPreview" class="hidden mb-4 p-4 bg-gray-50 dark:bg-[#333334] rounded-xl">
          <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('results.adminNotes') }}</p>
          <p id="confirmNotesText" class="text-sm text-gray-700 dark:text-gray-300"></p>
        </div>
        
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('results.actionCannotBeUndone') }}</p>
      </div>
      
      {{-- Footer --}}
      <div class="px-6 py-4 bg-gray-50 dark:bg-[#1D1D1E] flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
        <button type="button" id="cancelConfirmBtn" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-xl transition-colors">
          {{ __('admin.cancel') }}
        </button>
        <button type="button" id="proceedConfirmBtn" class="px-5 py-2.5 font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
          <span id="proceedBtnText"></span>
          <div id="proceedBtnSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
        </button>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Hide the default footer from layout since it's included above --}}
@section('hide_footer', true)

<style>
/* CSS from the provided design */
.results-nav-btn { background: transparent; border: 2px solid transparent; }
.results-nav-btn.active { background: transparent; border-color: transparent; }
.results-nav-btn.active > div { box-shadow: 0 0 0 3px rgba(182, 42, 45, 0.4); }
.results-nav-btn.active[data-slide-target="overall"] > div { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.4); }
.dark .results-nav-btn.active > div { box-shadow: 0 0 0 3px rgba(182, 42, 45, 0.5); }
.dark .results-nav-btn.active[data-slide-target="overall"] > div { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.5); }

.results-nav-btn-mobile { background: rgba(255, 255, 255, 0.7); border: 2px solid transparent; }
.results-nav-btn-mobile.active { background: rgba(255, 255, 255, 0.9); border-color: #B62A2D; box-shadow: 0 0 0 3px rgba(182, 42, 45, 0.3); }
.results-nav-btn-mobile.active[data-slide-target="overall"] { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3); }
.results-nav-btn-mobile.active[data-slide-target="internal-status"] { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3); }
.dark .results-nav-btn-mobile { background: rgba(68, 68, 69, 0.8); }
.dark .results-nav-btn-mobile.active { background: rgba(68, 68, 69, 1); border-color: #B62A2D; box-shadow: 0 0 0 3px rgba(182, 42, 45, 0.4); }
.dark .results-nav-btn-mobile.active[data-slide-target="overall"] { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.4); }
.dark .results-nav-btn-mobile.active[data-slide-target="internal-status"] { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.4); }

.results-slider-container { min-height: 400px; isolation: isolate; }
.results-slide .glass-card-strong { overflow: hidden; isolation: isolate; box-shadow: 0 4px 20px rgba(34, 34, 35, 0.08); }
.dark .results-slide .glass-card-strong { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); }

.results-slide { display: none; opacity: 0; transform: translateX(30px); transition: opacity 0.4s ease, transform 0.4s ease; }
.results-slide.active { display: block; opacity: 1; transform: translateX(0); }
.results-slide.slide-out-left { display: block; opacity: 0; transform: translateX(-30px); }

.results-action-btn { background: transparent; border: 2px solid transparent; }
.results-action-btn:hover { background: rgba(182, 42, 45, 0.05); }
.dark .results-action-btn:hover { background: rgba(182, 42, 45, 0.1); }

/* Scrollbar for lists */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.5); border-radius: 20px; }

/* Print Styles */
.print-only { display: none !important; }
@media print {
  body * { visibility: hidden; }
  #printContainer, #printContainer * { visibility: visible; }
  #printContainer { display: block !important; position: absolute; left: 0; top: 0; width: 100%; background: white !important; color: #222 !important; }
  .print-page { max-width: 800px; margin: 0 auto; padding: 20px; }
  .print-header { border-bottom: 3px solid #B62A2D; margin-bottom: 20px; display: flex; justify-content: space-between; }
  .print-logo-img { width: 50px; }
  .print-title { font-size: 24px; font-weight: bold; }
  .print-section { margin-bottom: 20px; }
  .print-section-header { background: #f5f5f5; border-left: 4px solid #B62A2D; padding: 5px 10px; display: flex; align-items: center; gap: 10px; }
  .print-status-verified { color: #22c55e; font-weight: bold; }
  .print-status-warning { color: #f59e0b; font-weight: bold; }
  .print-status-error { color: #ef4444; font-weight: bold; }
  .print-score { font-weight: bold; color: #B62A2D; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { border-bottom: 1px solid #ddd; padding: 5px; text-align: left; }
}
</style>

<script>
// Slide Navigation Logic
document.addEventListener('DOMContentLoaded', () => {
  const navBtns = document.querySelectorAll('[data-slide-target]');
  const slides = document.querySelectorAll('[data-slide]');
  
  navBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.getAttribute('data-slide-target');
      
      // Update nav active state
      navBtns.forEach(b => b.classList.remove('active'));
      // Activate matching buttons (both desktop and mobile)
      document.querySelectorAll(`[data-slide-target="${target}"]`).forEach(b => b.classList.add('active'));
      
      // Slide animation
      slides.forEach(slide => {
        if (slide.getAttribute('data-slide') === target) {
          slide.classList.remove('slide-out-left');
          slide.classList.add('active');
        } else {
          slide.classList.remove('active');
          slide.classList.add('slide-out-left');
          // Reset position after animation roughly ends
          setTimeout(() => {
             if(!slide.classList.contains('active')) slide.classList.remove('slide-out-left');
          }, 400);
        }
      });
    });
  });

  // Certificate File Viewer - Modal with Zoom/Drag
  @if(!$isInternal && !empty($certificate->berkas))
  const viewCertificateBtn = document.getElementById('viewCertificateBtn');
  const viewCertificateBtnMobile = document.getElementById('viewCertificateBtnMobile');
  const certificateModal = document.getElementById('certificateModal');
  const certificateModalOverlay = document.getElementById('certificateModalOverlay');
  const closeCertificateModalBtn = document.getElementById('closeCertificateModal');
  const certificateImage = document.getElementById('certificateImage');
  const certificatePdf = document.getElementById('certificatePdf');
  const imageWrapper = document.getElementById('imageWrapper');
  const imageLoader = document.getElementById('imageLoader');
  const zoomInBtn = document.getElementById('zoomInBtn');
  const zoomOutBtn = document.getElementById('zoomOutBtn');
  const zoomResetBtn = document.getElementById('zoomResetBtn');
  const zoomLevelDisplay = document.getElementById('zoomLevel');
  
  // Mobile tab elements
  const tabCertificateBtn = document.getElementById('tabCertificateBtn');
  const tabDataBtn = document.getElementById('tabDataBtn');
  const certificateImagePanel = document.getElementById('certificateImagePanel');
  const certificateDataPanel = document.getElementById('certificateDataPanel');
  
  const certificateId = {{ $certificate->id ?? 0 }};
  const isAdmin = {{ isset($isAdmin) && $isAdmin ? 'true' : 'false' }};
  
  // Mobile tab switching
  function switchToTab(tab) {
    if (tab === 'certificate') {
      // Show certificate image, hide data
      certificateImagePanel.classList.remove('hidden');
      certificateImagePanel.classList.add('flex-1');
      certificateDataPanel.classList.add('hidden');
      certificateDataPanel.classList.remove('flex-1');
      
      // Update tab button styles
      tabCertificateBtn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'bg-white', 'dark:bg-[#222223]');
      tabCertificateBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
      tabDataBtn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'bg-white', 'dark:bg-[#222223]');
      tabDataBtn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    } else {
      // Show data, hide certificate image
      certificateImagePanel.classList.add('hidden');
      certificateImagePanel.classList.remove('flex-1');
      certificateDataPanel.classList.remove('hidden');
      certificateDataPanel.classList.add('flex-1');
      
      // Update tab button styles
      tabDataBtn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'bg-white', 'dark:bg-[#222223]');
      tabDataBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
      tabCertificateBtn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400', 'bg-white', 'dark:bg-[#222223]');
      tabCertificateBtn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    }
  }
  
  // Tab event listeners
  if (tabCertificateBtn) {
    tabCertificateBtn.addEventListener('click', () => switchToTab('certificate'));
  }
  if (tabDataBtn) {
    tabDataBtn.addEventListener('click', () => switchToTab('data'));
  }
  
  // Zoom/Pan State
  let currentZoom = 1;
  const minZoom = 0.5;
  const maxZoom = 4;
  const zoomStep = 0.25;
  let isPanning = false;
  let startX = 0, startY = 0;
  let translateX = 0, translateY = 0;
  let lastTranslateX = 0, lastTranslateY = 0;
  
  function updateZoomDisplay() {
    zoomLevelDisplay.textContent = Math.round(currentZoom * 100) + '%';
  }
  
  function updateImageTransform() {
    certificateImage.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom})`;
  }
  
  function zoomIn() {
    if (currentZoom < maxZoom) {
      currentZoom = Math.min(currentZoom + zoomStep, maxZoom);
      updateZoomDisplay();
      updateImageTransform();
    }
  }
  
  function zoomOut() {
    if (currentZoom > minZoom) {
      currentZoom = Math.max(currentZoom - zoomStep, minZoom);
      updateZoomDisplay();
      updateImageTransform();
    }
  }
  
  function resetZoom() {
    currentZoom = 1;
    translateX = 0;
    translateY = 0;
    lastTranslateX = 0;
    lastTranslateY = 0;
    updateZoomDisplay();
    updateImageTransform();
  }
  
  // Mouse/Touch Pan handlers
  function startPan(e) {
    if (currentZoom <= 1) return;
    isPanning = true;
    imageWrapper.style.cursor = 'grabbing';
    const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
    const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
    startX = clientX - lastTranslateX;
    startY = clientY - lastTranslateY;
  }
  
  function doPan(e) {
    if (!isPanning) return;
    e.preventDefault();
    const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
    const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
    translateX = clientX - startX;
    translateY = clientY - startY;
    updateImageTransform();
  }
  
  function endPan() {
    isPanning = false;
    imageWrapper.style.cursor = 'grab';
    lastTranslateX = translateX;
    lastTranslateY = translateY;
  }
  
  // Wheel zoom
  function handleWheel(e) {
    e.preventDefault();
    if (e.deltaY < 0) {
      zoomIn();
    } else {
      zoomOut();
    }
  }
  
  // Pinch zoom for touch
  let initialPinchDistance = null;
  let initialZoom = 1;
  
  function handleTouchStart(e) {
    if (e.touches.length === 2) {
      const dx = e.touches[0].clientX - e.touches[1].clientX;
      const dy = e.touches[0].clientY - e.touches[1].clientY;
      initialPinchDistance = Math.sqrt(dx * dx + dy * dy);
      initialZoom = currentZoom;
    } else if (e.touches.length === 1) {
      startPan(e);
    }
  }
  
  function handleTouchMove(e) {
    if (e.touches.length === 2 && initialPinchDistance) {
      e.preventDefault();
      const dx = e.touches[0].clientX - e.touches[1].clientX;
      const dy = e.touches[0].clientY - e.touches[1].clientY;
      const distance = Math.sqrt(dx * dx + dy * dy);
      const scale = distance / initialPinchDistance;
      currentZoom = Math.min(maxZoom, Math.max(minZoom, initialZoom * scale));
      updateZoomDisplay();
      updateImageTransform();
    } else if (e.touches.length === 1 && isPanning) {
      doPan(e);
    }
  }
  
  function handleTouchEnd(e) {
    if (e.touches.length < 2) {
      initialPinchDistance = null;
    }
    if (e.touches.length === 0) {
      endPan();
    }
  }
  
  // Get header element for hiding when modal is open
  const appHeader = document.querySelector('[data-app-header]')?.closest('.fixed');
  
  function openCertificateModal() {
    certificateModal.classList.remove('hidden');
    certificateModal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Hide the header completely when modal is open
    if (appHeader) {
      appHeader.style.display = 'none';
    }
    
    // Reset to certificate tab on mobile
    switchToTab('certificate');
    
    // Show loader
    imageLoader.classList.remove('hidden');
    certificateImage.classList.add('hidden');
    certificatePdf.classList.add('hidden');
    
    // Reset zoom/pan
    resetZoom();
    
    // Fetch the certificate file
    fetchCertificateFile();
  }
  
  // Store original imageWrapper content for reset
  const originalImageWrapperContent = imageWrapper ? imageWrapper.innerHTML : '';
  
  function closeCertificateModal() {
    certificateModal.classList.add('hidden');
    certificateModal.classList.remove('flex');
    document.body.style.overflow = '';
    certificateImage.src = '';
    certificatePdf.src = '';
    
    // Show the header again
    if (appHeader) {
      appHeader.style.display = '';
    }
    
    // Reset imageWrapper to original state
    if (imageWrapper && originalImageWrapperContent) {
      imageWrapper.innerHTML = originalImageWrapperContent;
    }
  }
  
  async function fetchCertificateFile() {
    try {
      // Use proxy URL to avoid CORS/CSP issues - server fetches from R2 and serves directly
      const proxyUrl = isAdmin 
        ? `/admin/certificate/${certificateId}/file`
        : `/certificate/${certificateId}/file`;
      
      // Also fetch metadata to know file type
      const metaUrl = isAdmin 
        ? `/admin/certificate/${certificateId}/file-url`
        : `/certificate/${certificateId}/file-url`;
      
      console.log('Fetching certificate metadata from:', metaUrl);
      
      const response = await fetch(metaUrl, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
      });
      
      console.log('Response status:', response.status);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      console.log('Response data:', data);
      
      if (data.success) {
        const isPdf = data.file_type === 'pdf';
        
        console.log('File type:', data.file_type, 'Is PDF:', isPdf);
        console.log('Using proxy URL:', proxyUrl);
        
        if (isPdf) {
          // For PDF, use iframe with proxy URL (served from same origin, no CSP issues)
          imageLoader.classList.add('hidden');
          certificateImage.classList.add('hidden');
          certificatePdf.src = proxyUrl;
          certificatePdf.classList.remove('hidden');
        } else {
          // Show image using proxy URL (served from same origin, no CORS issues)
          certificateImage.onload = function() {
            console.log('Image loaded successfully via proxy');
            imageLoader.classList.add('hidden');
            certificateImage.classList.remove('hidden');
          };
          certificateImage.onerror = function(e) {
            console.error('Image load error:', e);
            imageLoader.classList.add('hidden');
            // Show error message with open in new tab option
            imageWrapper.innerHTML = `
              <div class="text-center p-8">
                <i data-lucide="image-off" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 font-medium mb-4">{{ __("results.imageLoadError") ?? 'Gagal memuat gambar' }}</p>
                <a href="${proxyUrl}" target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors text-sm">
                  <i data-lucide="external-link" class="w-4 h-4"></i>
                  {{ __("results.openInNewTab") }}
                </a>
              </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
          };
          certificateImage.src = proxyUrl;
        }
      } else {
        console.error('Failed to get file info:', data.message);
        imageLoader.classList.add('hidden');
        imageWrapper.innerHTML = `
          <div class="text-center p-8">
            <i data-lucide="file-x" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400 font-medium">${data.message || '{{ __("results.fileUrlError") }}'}</p>
          </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      }
    } catch (error) {
      console.error('Error fetching certificate file:', error);
      imageLoader.classList.add('hidden');
      imageWrapper.innerHTML = `
        <div class="text-center p-8">
          <i data-lucide="alert-circle" class="w-16 h-16 text-red-400 mx-auto mb-4"></i>
          <p class="text-gray-600 dark:text-gray-400 font-medium">{{ __("results.fileUrlError") }}</p>
          <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">${error.message}</p>
        </div>
      `;
      if (typeof lucide !== 'undefined') lucide.createIcons();
    }
  }
  
  // Event listeners for modal
  if (viewCertificateBtn) viewCertificateBtn.addEventListener('click', openCertificateModal);
  if (viewCertificateBtnMobile) viewCertificateBtnMobile.addEventListener('click', openCertificateModal);
  if (closeCertificateModalBtn) closeCertificateModalBtn.addEventListener('click', closeCertificateModal);
  if (certificateModalOverlay) certificateModalOverlay.addEventListener('click', closeCertificateModal);
  
  // Zoom controls
  if (zoomInBtn) zoomInBtn.addEventListener('click', zoomIn);
  if (zoomOutBtn) zoomOutBtn.addEventListener('click', zoomOut);
  if (zoomResetBtn) zoomResetBtn.addEventListener('click', resetZoom);
  
  // Pan events for mouse
  if (imageWrapper) {
    imageWrapper.addEventListener('mousedown', startPan);
    imageWrapper.addEventListener('mousemove', doPan);
    imageWrapper.addEventListener('mouseup', endPan);
    imageWrapper.addEventListener('mouseleave', endPan);
    imageWrapper.addEventListener('wheel', handleWheel, { passive: false });
    
    // Touch events
    imageWrapper.addEventListener('touchstart', handleTouchStart, { passive: false });
    imageWrapper.addEventListener('touchmove', handleTouchMove, { passive: false });
    imageWrapper.addEventListener('touchend', handleTouchEnd);
  }
  
  // Close modal on escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !certificateModal.classList.contains('hidden')) {
      closeCertificateModal();
    }
  });
  @endif

  // Admin Status Update Logic
  @if(isset($isAdmin) && $isAdmin && !$isInternal && $status === 'suspicious')
  const adminCertificateId = {{ $certificate->id ?? 0 }};
  const adminNotesTextarea = document.getElementById('adminNotes');
  const markVerifiedBtn = document.getElementById('markVerifiedBtn');
  const markNotVerifiedBtn = document.getElementById('markNotVerifiedBtn');
  const statusConfirmModal = document.getElementById('statusConfirmModal');
  const confirmModalOverlay = document.getElementById('confirmModalOverlay');
  const cancelConfirmBtn = document.getElementById('cancelConfirmBtn');
  const proceedConfirmBtn = document.getElementById('proceedConfirmBtn');
  const proceedBtnText = document.getElementById('proceedBtnText');
  const proceedBtnSpinner = document.getElementById('proceedBtnSpinner');
  const confirmIconContainer = document.getElementById('confirmIconContainer');
  const confirmIcon = document.getElementById('confirmIcon');
  const confirmModalTitle = document.getElementById('confirm-modal-title');
  const confirmMessage = document.getElementById('confirmMessage');
  const confirmNotesPreview = document.getElementById('confirmNotesPreview');
  const confirmNotesText = document.getElementById('confirmNotesText');
  const adminStatusPanel = document.getElementById('adminStatusPanel');
  
  let pendingStatus = null;
  
  function openConfirmModal(newStatus) {
    pendingStatus = newStatus;
    const notes = adminNotesTextarea.value.trim();
    
    if (newStatus === 'verified') {
      confirmIconContainer.className = 'p-3 rounded-xl bg-green-100 dark:bg-green-900/40';
      confirmIcon.className = 'w-6 h-6 text-green-500';
      confirmIcon.setAttribute('data-lucide', 'check-circle');
      confirmModalTitle.textContent = '{{ __("results.confirmVerified") }}';
      confirmMessage.textContent = '{{ __("results.confirmVerifiedDesc") }}';
      proceedConfirmBtn.className = 'px-5 py-2.5 font-medium rounded-xl transition-colors flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white';
      proceedBtnText.textContent = '{{ __("results.markAsVerified") }}';
    } else {
      confirmIconContainer.className = 'p-3 rounded-xl bg-red-100 dark:bg-red-900/40';
      confirmIcon.className = 'w-6 h-6 text-red-500';
      confirmIcon.setAttribute('data-lucide', 'x-circle');
      confirmModalTitle.textContent = '{{ __("results.confirmNotVerified") }}';
      confirmMessage.textContent = '{{ __("results.confirmNotVerifiedDesc") }}';
      proceedConfirmBtn.className = 'px-5 py-2.5 font-medium rounded-xl transition-colors flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white';
      proceedBtnText.textContent = '{{ __("results.markAsNotVerified") }}';
    }
    
    if (notes) {
      confirmNotesPreview.classList.remove('hidden');
      confirmNotesText.textContent = notes;
    } else {
      confirmNotesPreview.classList.add('hidden');
    }
    
    statusConfirmModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Refresh icons
    if (window.lucide?.createIcons) window.lucide.createIcons();
  }
  
  function closeConfirmModal() {
    statusConfirmModal.classList.add('hidden');
    document.body.style.overflow = '';
    pendingStatus = null;
  }
  
  async function updateStatus() {
    if (!pendingStatus) return;
    
    const notes = adminNotesTextarea.value.trim();
    
    // Show loading state
    proceedBtnText.classList.add('opacity-50');
    proceedBtnSpinner.classList.remove('hidden');
    proceedConfirmBtn.disabled = true;
    cancelConfirmBtn.disabled = true;
    
    try {
      const response = await fetch(`/admin/certificate/${adminCertificateId}/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          new_status: pendingStatus,
          notes: notes
        })
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Reload the page to show the updated status
        window.location.reload();
      } else {
        alert(data.message || '{{ __("results.statusUpdateError") }}');
        closeConfirmModal();
      }
    } catch (error) {
      console.error('Error updating status:', error);
      alert('{{ __("results.statusUpdateError") }}');
      closeConfirmModal();
    } finally {
      proceedBtnText.classList.remove('opacity-50');
      proceedBtnSpinner.classList.add('hidden');
      proceedConfirmBtn.disabled = false;
      cancelConfirmBtn.disabled = false;
    }
  }
  
  // Event listeners for admin status update
  markVerifiedBtn.addEventListener('click', () => openConfirmModal('verified'));
  markNotVerifiedBtn.addEventListener('click', () => openConfirmModal('not_verified'));
  cancelConfirmBtn.addEventListener('click', closeConfirmModal);
  confirmModalOverlay.addEventListener('click', closeConfirmModal);
  proceedConfirmBtn.addEventListener('click', updateStatus);
  
  // Close confirm modal on escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !statusConfirmModal.classList.contains('hidden')) {
      closeConfirmModal();
    }
  });
  @endif

  // Init Icons
  if (window.lucide?.createIcons) window.lucide.createIcons();
});
</script>
@endsection
