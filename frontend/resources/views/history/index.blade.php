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
        <span class="font-medium">{{ __('history.breadcrumb') }}</span>
      </div>
      <h1 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ __('history.title') }}</h1>
      <p class="text-lg text-gray-700 dark:text-gray-300">{{ __('history.subtitle') }}</p>
    </div>

    @if($histories->isEmpty())
      <!-- Empty State -->
      <div class="max-w-6xl mx-auto">
        <div class="glass-card-strong rounded-2xl py-20 px-12 md:py-24 md:px-16 text-center animate-fade-in">
          <div class="flex justify-center mb-10">
            <div class="glass-icon p-6">
              <i data-lucide="history" class="text-[#B62A2D]" style="width:64px;height:64px"></i>
            </div>
          </div>
          <h2 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-6">{{ __('history.noHistory') }}</h2>
          <p class="text-lg text-gray-600 dark:text-gray-400 mb-16 max-w-2xl mx-auto">{{ __('history.noHistoryDesc') }}</p>
          <a href="{{ route('form') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-[#222223] dark:bg-[#B62A2D] text-white rounded-lg font-semibold text-lg hover:bg-[#333334] dark:hover:bg-[#9a2426] transform hover:scale-105 transition-all duration-300 shadow-lg glow-red relative overflow-hidden group">
            <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
            <i data-lucide="file-check" style="width:20px;height:20px"></i>
            {{ __('history.startVerifying') }}
          </a>
        </div>
      </div>
    @else
      <!-- History List -->
      <div class="max-w-6xl mx-auto space-y-6">
        @foreach($histories as $history)
          <div class="glass-card hover:bg-white/85 dark:hover:bg-[#333334]/85 rounded-2xl transition-all duration-300 overflow-hidden animate-fade-in">
            <div class="p-6 md:p-8">
              <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <!-- Left Side: Content -->
                <div class="flex-1">
                  <div class="flex items-start gap-4 mb-4">
                    <!-- Status Icon -->
                    <div class="flex-shrink-0 mt-1">
                      @if($history->overall_status === 'verified')
                        <div class="p-3 bg-green-100/80 dark:bg-green-900/40 backdrop-blur-sm rounded-xl">
                          <i data-lucide="check-circle" class="text-green-600 dark:text-green-400" style="width:32px;height:32px"></i>
                        </div>
                      @elseif($history->overall_status === 'not_verified')
                        <div class="p-3 bg-red-100/80 dark:bg-red-900/40 backdrop-blur-sm rounded-xl">
                          <i data-lucide="x-circle" class="text-red-600 dark:text-red-400" style="width:32px;height:32px"></i>
                        </div>
                      @else
                        <div class="p-3 bg-yellow-100/80 dark:bg-yellow-900/40 backdrop-blur-sm rounded-xl">
                          <i data-lucide="alert-circle" class="text-yellow-600 dark:text-yellow-400" style="width:32px;height:32px"></i>
                        </div>
                      @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-1">
                      <h3 class="text-2xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ $history->event_name }}</h3>
                      
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                          <i data-lucide="user" style="width:16px;height:16px" class="text-gray-400"></i>
                          <span>{{ $history->name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                          <i data-lucide="building" style="width:16px;height:16px" class="text-gray-400"></i>
                          <span>{{ $history->organizer }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                          <i data-lucide="calendar" style="width:16px;height:16px" class="text-gray-400"></i>
                          <span>{{ $history->date_range }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                          <i data-lucide="file" style="width:16px;height:16px" class="text-gray-400"></i>
                          <span class="truncate">{{ $history->file_name }}</span>
                        </div>
                      </div>

                      <!-- Status Badge and Confidence -->
                      <div class="flex flex-wrap items-center gap-3">
                        @if($history->overall_status === 'verified')
                          <span class="px-3 py-1 bg-green-100/80 backdrop-blur-sm text-green-800 rounded-full text-sm font-semibold">
                            {{ $history->status_text }}
                          </span>
                        @elseif($history->overall_status === 'not_verified')
                          <span class="px-3 py-1 bg-red-100/80 backdrop-blur-sm text-red-800 rounded-full text-sm font-semibold">
                            {{ $history->status_text }}
                          </span>
                        @else
                          <span class="px-3 py-1 bg-yellow-100/80 backdrop-blur-sm text-yellow-800 rounded-full text-sm font-semibold">
                            {{ $history->status_text }}
                          </span>
                        @endif

                        @if($history->confidence)
                          <span class="px-3 py-1 glass-button text-[#B62A2D] rounded-full text-sm font-semibold">
                            {{ __('history.confidence') }}: {{ $history->confidence }}%
                          </span>
                        @endif
                      </div>

                      <p class="text-xs text-gray-500 dark:text-gray-500 mt-3">
                        <i data-lucide="clock" style="width:14px;height:14px" class="inline"></i>
                        {{ __('history.verifiedAt') }} {{ $history->created_at->format('d M Y, H:i') }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Right Side: Action Button -->
                <div class="flex-shrink-0">
                  <a href="{{ route('history.show', $history->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#222223] dark:bg-[#B62A2D] text-white rounded-lg font-semibold hover:bg-[#333334] dark:hover:bg-[#9a2426] transform hover:scale-105 transition-all duration-300 shadow-md glow-red-sm relative overflow-hidden group">
                    <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                    <i data-lucide="eye" style="width:18px;height:18px"></i>
                    {{ __('history.viewDetail') }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endforeach

        <!-- Pagination -->
        <div class="mt-8">
          {{ $histories->links() }}
        </div>
      </div>
    @endif
  </div>
  
  <!-- Footer Section - Seamlessly integrated with page background -->
  @include('partials.footer')
</div>

{{-- Hide the default footer from layout since it's included above --}}
@section('hide_footer', true)
@endsection
