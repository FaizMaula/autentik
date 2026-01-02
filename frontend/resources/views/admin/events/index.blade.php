@extends('layouts.app')

@section('content')
<section class="relative min-h-screen flex flex-col pt-20 pb-0 overflow-hidden">
  @include('components.animated-background', ['showWatermark' => true])

  <div class="grow py-8">
    <div class="container mx-auto px-6 relative z-10">
      <!-- Back to Dashboard -->
      <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-[#B62A2D] transition-colors mb-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('admin.backToDashboard') }}
      </a>

      <!-- Header -->
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE]">{{ __('admin.eventsTitle') }}</h1>
          <p class="text-gray-600 dark:text-gray-300 mt-1">{{ __('admin.eventsSubtitle') }}</p>
        </div>
        <div class="flex gap-3">
          <a href="{{ route('admin.events.template') }}" 
             data-no-loading
             class="px-4 py-2 bg-gray-200 dark:bg-[#3D3D3E] text-gray-700 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-[#4D4D4E] transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            {{ __('admin.downloadTemplate') }}
          </a>
          <a href="{{ route('admin.events.create') }}" 
             class="px-4 py-2 bg-[#B62A2D] text-white rounded-lg font-medium hover:bg-[#d5575e] transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.uploadEvent') }}
          </a>
        </div>
      </div>

      <!-- Success/Error Messages -->
      @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 rounded-lg">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400 rounded-lg">
          {{ session('error') }}
        </div>
      @endif

      <!-- Events Table -->
      <div class="glass-card-strong rounded-2xl overflow-hidden w-full">
        <div class="overflow-x-auto w-full">
          <table class="w-full table-auto">
            <thead class="bg-gray-100 dark:bg-[#333334]">
              <tr>
                <th class="pl-6 pr-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('admin.eventName') }}</th>
                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('admin.organizer') }}</th>
                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ __('admin.eventDate') }}</th>
                <th class="px-4 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ __('admin.participantCount') }}</th>
                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">{{ __('admin.uploadedAt') }}</th>
                <th class="pl-4 pr-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('admin.actions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-[#3D3D3E]">
              @forelse($events as $event)
                <tr class="hover:bg-gray-50 dark:hover:bg-[#333334]/50 transition-colors">
                  <td class="pl-6 pr-4 py-4 max-w-xs">
                    <div class="font-medium text-[#222223] dark:text-[#FEFEFE] truncate">{{ $event->event_name }}</div>
                    @if($event->event_name_en)
                      <div class="text-sm text-gray-500 dark:text-gray-300 truncate">{{ $event->event_name_en }}</div>
                    @endif
                  </td>
                  <td class="px-4 py-4 text-gray-600 dark:text-gray-300 truncate max-w-[180px]">{{ $event->organizer }}</td>
                  <td class="px-4 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                    @if($event->event_date)
                      {{ $event->event_date->format('d M Y') }}
                    @elseif($event->start_date && $event->end_date)
                      {{ $event->start_date->format('d M') }} - {{ $event->end_date->format('d M Y') }}
                    @else
                      -
                    @endif
                  </td>
                  <td class="px-4 py-4 text-center whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                      {{ $event->participants->count() }} {{ __('admin.participants') }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                    {{ $event->created_at->format('d M Y, H:i') }}
                  </td>
                  <td class="pl-4 pr-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                      <a href="{{ route('admin.events.show', $event) }}" 
                         class="p-2 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                         title="{{ __('admin.viewDetails') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </a>
                      <form action="{{ route('admin.events.destroy', $event) }}" method="POST" 
                            onsubmit="return confirm(&quot;{{ __('admin.confirmDelete') }}&quot;)" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                title="{{ __('admin.delete') }}">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center gap-3">
                      <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <p>{{ __('admin.noEvents') }}</p>
                      <a href="{{ route('admin.events.create') }}" class="text-[#B62A2D] hover:underline">{{ __('admin.uploadFirstEvent') }}</a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        @if($events->hasPages())
          <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $events->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
  
  @include('partials.footer')
</section>

@section('hide_footer', true)
@endsection
