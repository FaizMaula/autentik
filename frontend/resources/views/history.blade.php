@extends('layouts.app')
@section('content')
<section class="relative min-h-screen flex flex-col pt-20 pb-0 overflow-hidden">
  @include('components.animated-background', ['showWatermark' => true])

  <div class="flex-grow py-8">
    <div class="container mx-auto px-6 relative z-10">
      <!-- Header -->
      <div class="max-w-6xl mx-auto mb-8">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-6">
          <a href="/" class="hover:text-[#B62A2D] transition-colors">
            <i data-lucide="home" style="width:18px;height:18px"></i>
          </a>
          <span>/</span>
          <span class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ __('history.breadcrumb') }}</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ __('history.title') }}</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ __('history.subtitle') }}</p>
      </div>

      @if($histories->isEmpty())
        <!-- Empty State -->
        <div class="max-w-6xl mx-auto">
          <div class="glass-card-strong rounded-2xl py-20 px-12 md:py-24 md:px-16 text-center">
            <div class="flex justify-center mb-10">
              <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-full">
                <i data-lucide="history" class="text-gray-400 dark:text-gray-500" style="width:64px;height:64px"></i>
              </div>
            </div>
            <h2 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-6">{{ __('history.noHistory') }}</h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-16 max-w-2xl mx-auto">{{ __('history.noHistoryDesc') }}</p>
            <a href="{{ route('certificate.create') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#B62A2D] text-white rounded-lg font-semibold text-lg hover:bg-[#9a2426] dark:hover:bg-[#d5575e] transform hover:scale-105 transition-all duration-300 shadow-lg">
              <i data-lucide="file-check" style="width:20px;height:20px"></i>
              {{ __('history.startVerifying') }}
            </a>
          </div>
        </div>
      @else
        <!-- History List -->
        <div class="max-w-6xl mx-auto space-y-6" id="historyList">
          @foreach($histories as $history)
            <div class="glass-card-strong rounded-2xl overflow-hidden hover:shadow-2xl transition-shadow duration-300 history-card {{ $history->overall_status === 'pending' ? 'pending-card' : '' }}" data-id="{{ $history->id }}" data-status="{{ $history->overall_status }}">
              <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                  <!-- Left Side: Content -->
                  <div class="flex-1">
                    <div class="flex items-start gap-4 mb-4">
                      <!-- Status Icon -->
                      <div class="flex-shrink-0 mt-1">
                        @if($history->overall_status === 'pending')
                          <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                            <i data-lucide="loader" class="text-blue-600 dark:text-blue-400 animate-spin" style="width:32px;height:32px"></i>
                          </div>
                        @elseif($history->overall_status === 'verified')
                          <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                            <i data-lucide="check-circle" class="text-green-600 dark:text-green-400" style="width:32px;height:32px"></i>
                          </div>
                        @elseif($history->overall_status === 'not_verified')
                          <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-xl">
                            <i data-lucide="x-circle" class="text-red-600 dark:text-red-400" style="width:32px;height:32px"></i>
                          </div>
                        @elseif($history->overall_status === 'suspicious')
                          <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                            <i data-lucide="alert-triangle" class="text-yellow-600 dark:text-yellow-400" style="width:32px;height:32px"></i>
                          </div>
                        @else
                          <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                            <i data-lucide="alert-circle" class="text-yellow-600 dark:text-yellow-400" style="width:32px;height:32px"></i>
                          </div>
                        @endif
                      </div>

                      <!-- Info -->
                      <div class="flex-1">
                        <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ $history->nama_kegiatan }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                          <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="user" style="width:16px;height:16px" class="text-gray-400 dark:text-gray-500"></i>
                            <span>{{ $history->nama }}</span>
                          </div>
                          <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="building" style="width:16px;height:16px" class="text-gray-400 dark:text-gray-500"></i>
                            <span>{{ $history->penyelenggara }}</span>
                          </div>
                          <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="calendar" style="width:16px;height:16px" class="text-gray-400 dark:text-gray-500"></i>
                              <span>
                                  {{ \Carbon\Carbon::parse($history->tanggal_mulai)->format('d M Y') }}
                                  -
                                  {{ \Carbon\Carbon::parse($history->tanggal_selesai)->format('d M Y') }}
                              </span>
                          </div>
                          <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="file" style="width:16px;height:16px" class="text-gray-400 dark:text-gray-500"></i>
                            <span class="truncate">{{ basename($history->berkas) }}</span>
                          </div>
                        </div>

                        <!-- Status Badge and Confidence -->
                        <div class="flex flex-wrap items-center gap-3">
                          @if($history->overall_status === 'pending')
                              <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 rounded-full text-sm font-semibold flex items-center gap-2">
                                  <i data-lucide="loader" class="animate-spin" style="width:14px;height:14px"></i>
                                  {{ __('results.pending') }}
                              </span>
                          @elseif($history->overall_status === 'verified')
                              <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded-full text-sm font-semibold">
                                  {{ __('results.verified') }}
                              </span>
                          @elseif($history->overall_status === 'suspicious')
                              <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 rounded-full text-sm font-semibold flex items-center gap-2">
                                  <i data-lucide="alert-triangle" style="width:14px;height:14px"></i>
                                  {{ __('results.suspicious') }}
                              </span>
                          @else
                              <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded-full text-sm font-semibold">
                                  {{ __('results.notVerified') }}
                              </span>
                          @endif

                          @if($history->overall_status === 'pending')
                            <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-full text-sm">
                              {{ __('results.pendingDesc') }}
                            </span>
                          @elseif($history->final_score && $history->final_score >= 0)
                            {{-- Confidence badge color matches status --}}
                            @if($history->overall_status === 'verified')
                              <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded-full text-sm font-semibold">
                                {{ __('history.confidence') }}: {{ round($history->final_score) }}%
                              </span>
                            @elseif($history->overall_status === 'suspicious')
                              <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 rounded-full text-sm font-semibold">
                                {{ __('history.confidence') }}: {{ round($history->final_score) }}%
                              </span>
                            @else
                              <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded-full text-sm font-semibold">
                                {{ __('history.confidence') }}: {{ round($history->final_score) }}%
                              </span>
                            @endif
                          @endif
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                          <i data-lucide="clock" style="width:14px;height:14px" class="inline"></i>
                          @if($history->overall_status === 'pending')
                            {{ __('history.submittedAt') ?? 'Diajukan pada' }} {{ $history->created_at->format('d M Y, H:i') }}
                          @else
                            {{ __('history.verifiedAt') }} {{ $history->created_at->format('d M Y, H:i') }}
                          @endif
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Right Side: Action Button -->
                  <div class="flex-shrink-0">
                    @if($history->overall_status === 'pending')
                      <button disabled class="inline-flex items-center gap-2 px-6 py-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg font-semibold cursor-not-allowed">
                        <i data-lucide="loader" class="animate-spin" style="width:18px;height:18px"></i>
                        {{ __('history.processing') ?? 'Memproses...' }}
                      </button>
                    @else
                      <a href="{{ route('result.show', $history->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#B62A2D] text-white rounded-lg font-semibold hover:bg-[#9a2426] dark:hover:bg-[#d5575e] transform hover:scale-105 transition-all duration-300 shadow-md">
                        <i data-lucide="eye" style="width:18px;height:18px"></i>
                        {{ __('history.viewDetail') }}
                      </a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach

          
        </div>
      @endif
    </div>
  </div>
  
  @include('partials.footer')
</section>

<style>
/* Shimmer effect for pending cards */
.pending-card {
  position: relative;
  overflow: hidden;
}

.pending-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(59, 130, 246, 0.08),
    rgba(59, 130, 246, 0.15),
    rgba(59, 130, 246, 0.08),
    transparent
  );
  animation: shimmer 2s infinite;
  z-index: 1;
  pointer-events: none;
}

.dark .pending-card::before {
  background: linear-gradient(
    90deg,
    transparent,
    rgba(59, 130, 246, 0.05),
    rgba(59, 130, 246, 0.1),
    rgba(59, 130, 246, 0.05),
    transparent
  );
}

@keyframes shimmer {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}

/* Pending card border glow */
.pending-card {
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.dark .pending-card {
  border: 1px solid rgba(59, 130, 246, 0.2);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Check if there are any pending cards
  const pendingCards = document.querySelectorAll('.history-card[data-status="pending"]');
  
  if (pendingCards.length > 0) {
    // Start polling every 10 seconds
    const pollingInterval = setInterval(async function() {
      try {
        // Get all pending certificate IDs
        const pendingIds = Array.from(pendingCards).map(card => card.dataset.id);
        
        // Check status via API
        const response = await fetch('/api/certificates/check-status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ ids: pendingIds })
        });
        
        if (response.ok) {
          const data = await response.json();
          
          // Check if any status has changed from pending
          let shouldRefresh = false;
          for (const id of pendingIds) {
            if (data.statuses && data.statuses[id] && data.statuses[id] !== 'pending') {
              shouldRefresh = true;
              break;
            }
          }
          
          if (shouldRefresh) {
            // Stop polling and refresh the page
            clearInterval(pollingInterval);
            window.location.reload();
          }
        }
      } catch (error) {
        console.log('Polling check failed:', error);
      }
    }, 10000); // Poll every 10 seconds
    
    // Stop polling after 5 minutes to prevent infinite polling
    setTimeout(function() {
      clearInterval(pollingInterval);
    }, 300000);
  }
});
</script>

@section('hide_footer', true)
@endsection
