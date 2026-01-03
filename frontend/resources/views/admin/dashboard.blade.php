@extends('layouts.app')

@section('content')
<section class="relative min-h-screen flex flex-col pt-20 pb-0 overflow-hidden">
  @include('components.animated-background', ['showWatermark' => true])

  <div class="flex-grow py-8 px-3 md:px-4">
    <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
      <!-- Greeting Section -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">
          {{ __('admin.greeting') }}, {{ Auth::user()->name }}!
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-300 font-semibold">{{ __('admin.dashboardSubtitle') }}</p>
        <div class="academic-line academic-line-animate w-32 mx-auto mt-6"></div>
      </div>

      <!-- Admin Menu Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
        <!-- Events Card -->
        <a href="{{ route('admin.events.index') }}" class="relative group">
          <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex flex-col items-center text-center text-white">
              <div class="mb-6 glass-icon p-4">
                <i data-lucide="calendar-plus" style="width:48px;height:48px"></i>
              </div>
              <h3 class="text-2xl font-bold mb-4">{{ __('admin.eventsTitle') }}</h3>
              <p class="text-gray-100 leading-relaxed">{{ __('admin.eventsCardDesc') }}</p>
            </div>
          </div>
        </a>

        <!-- History Card -->
        <a href="{{ route('admin.history.all') }}" class="relative group">
          <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300 cursor-pointer">
            <div class="flex flex-col items-center text-center text-white">
              <div class="mb-6 glass-icon p-4">
                <i data-lucide="list-checks" style="width:48px;height:48px"></i>
              </div>
              <h3 class="text-2xl font-bold mb-4">{{ __('admin.allHistoryTitle') }}</h3>
              <p class="text-gray-100 leading-relaxed">{{ __('admin.historyCardDesc') }}</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
  
  @include('partials.footer')
</section>

@section('hide_footer', true)
@endsection
