@extends('layouts.app')

@section('content')
<section class="relative min-h-screen flex flex-col pt-20 pb-0 overflow-hidden">
  @include('components.animated-background', ['showWatermark' => true])

  <div class="grow py-8">
    <div class="container mx-auto px-6 relative z-10">
      <!-- Header -->
      <div class="mb-8">
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-[#B62A2D] transition-colors mb-4">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          {{ __('admin.backToEvents') }}
        </a>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <h1 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE]">{{ $event->event_name }}</h1>
            @if($event->event_name_en)
              <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $event->event_name_en }}</p>
            @endif
          </div>
          <form action="{{ route('admin.events.destroy', $event) }}" method="POST" 
                onsubmit="return confirm(&quot;{{ __('admin.confirmDelete') }}&quot;)" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition-all flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              {{ __('admin.deleteEvent') }}
            </button>
          </form>
        </div>
      </div>

      <!-- Event Info Card -->
      <div class="glass-card-strong rounded-2xl p-6 md:p-8 mb-6 w-full">
        <h2 class="text-xl font-semibold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ __('admin.eventInfo') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('form.organizer') }}</span>
            <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $event->organizer }}</p>
          </div>
          <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('form.eventDate') }}</span>
            <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">
              @if($event->event_date)
                {{ $event->event_date->format('d M Y') }}
              @elseif($event->start_date && $event->end_date)
                {{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}
              @else
                -
              @endif
            </p>
          </div>
          <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('form.academicYear') }}</span>
            <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $event->academic_year ?? '-' }}</p>
          </div>
          <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.uploadedBy') }}</span>
            <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $event->uploadedBy->name }}</p>
          </div>
          <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.uploadedAt') }}</span>
            <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $event->created_at->format('d M Y, H:i') }}</p>
          </div>
          <div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.originalFile') }}</span>
            <p class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $event->original_filename ?? '-' }}</p>
          </div>
        </div>
        @if($event->description)
          <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.description') }}</span>
            <p class="font-medium text-[#222223] dark:text-[#FEFEFE] mt-1">{{ $event->description }}</p>
          </div>
        @endif
      </div>

      <!-- Participants Table -->
      <div class="glass-card-strong rounded-2xl overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
          <h2 class="text-xl font-semibold text-[#222223] dark:text-[#FEFEFE]">
            {{ __('admin.participantList') }} 
            <span class="text-base font-normal text-gray-500">({{ $event->participants->count() }} {{ __('admin.participants') }})</span>
          </h2>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100 dark:bg-gray-800/50">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.nim') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.participantName') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('auth.email') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.faculty') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.studyProgram') }}</th>
                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.status') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              @forelse($event->participants as $index => $participant)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $index + 1 }}</td>
                  <td class="px-6 py-4 font-mono text-[#222223] dark:text-[#FEFEFE]">{{ $participant->nim }}</td>
                  <td class="px-6 py-4 font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $participant->participant_name }}</td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $participant->email ?? '-' }}</td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $participant->faculty ?? '-' }}</td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $participant->study_program ?? '-' }}</td>
                  <td class="px-6 py-4 text-center">
                    @if($participant->attendance_status === 'present' || $participant->attendance_status === 'Hadir')
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                        {{ __('admin.present') }}
                      </span>
                    @else
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                        {{ $participant->attendance_status }}
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    {{ __('admin.noParticipants') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  @include('partials.footer')
</section>

@section('hide_footer', true)
@endsection
