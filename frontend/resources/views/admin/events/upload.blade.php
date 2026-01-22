@extends('layouts.app')

@section('content')
<section class="relative min-h-screen flex flex-col pt-20 pb-0 overflow-hidden">
  @include('components.animated-background', ['showWatermark' => true])

  <div class="flex-grow py-8 px-3 md:px-4">
    <div class="max-w-4xl mx-auto px-4 md:px-6 relative z-10">
      <!-- Header -->
      <div class="mb-8">
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-[#B62A2D] transition-colors mb-4">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          {{ __('admin.backToEvents') }}
        </a>
        <h1 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE]">{{ __('admin.uploadEventTitle') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('admin.uploadEventSubtitleNew') }}</p>
      </div>

      <!-- Error Messages -->
      @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-400 rounded-lg">
          <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Upload Form -->
      <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Event Information Card -->
        <div class="glass-card-strong rounded-2xl p-6 md:p-8">
          <h2 class="text-xl font-semibold text-[#222223] dark:text-[#FEFEFE] mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#B62A2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ __('admin.eventInfo') }}
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Event Name (ID) -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.eventName') }} <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                name="event_name" 
                value="{{ old('event_name') }}"
                placeholder="{{ __('admin.eventNamePlaceholder') }}"
                class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all"
                required
              />
            </div>

            <!-- Event Name (EN) - Optional -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.eventNameEn') }}
                <span class="text-gray-400 text-xs font-normal ml-1">({{ __('admin.optional') }})</span>
              </label>
              <input 
                type="text" 
                name="event_name_en" 
                value="{{ old('event_name_en') }}"
                placeholder="{{ __('admin.eventNameEnPlaceholder') }}"
                class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all"
              />
            </div>

            <!-- Organizer -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.organizer') }} <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                name="organizer" 
                value="{{ old('organizer') }}"
                placeholder="{{ __('admin.organizerPlaceholder') }}"
                class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all"
                required
              />
            </div>

            <!-- Academic Year -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.academicYear') }} <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                name="academic_year" 
                value="{{ old('academic_year') }}"
                placeholder="{{ __('admin.academicYearPlaceholder') }}"
                class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all"
                required
              />
            </div>
          </div>

          <!-- Event Duration Type Toggle -->
          <div class="mt-6">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
              {{ __('admin.eventDurationType') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
              <label class="flex-1 cursor-pointer">
                <input type="radio" name="duration_type" value="single" class="peer hidden" checked />
                <div class="peer-checked:bg-[#B62A2D] peer-checked:text-white peer-checked:border-[#B62A2D] bg-white dark:bg-[#333334] border-2 border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center transition-all duration-300 hover:border-[#B62A2D]">
                  <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold">{{ __('admin.singleDay') }}</span>
                  </div>
                  <p class="text-xs mt-1 opacity-80">{{ __('admin.singleDayDesc') }}</p>
                </div>
              </label>
              <label class="flex-1 cursor-pointer">
                <input type="radio" name="duration_type" value="multi" class="peer hidden" />
                <div class="peer-checked:bg-[#B62A2D] peer-checked:text-white peer-checked:border-[#B62A2D] bg-white dark:bg-[#333334] border-2 border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center transition-all duration-300 hover:border-[#B62A2D]">
                  <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold">{{ __('admin.multiDay') }}</span>
                  </div>
                  <p class="text-xs mt-1 opacity-80">{{ __('admin.multiDayDesc') }}</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Date Fields -->
          <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Event Date (Single Day) -->
            <div id="singleDateWrapper">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.eventDate') }} <span class="text-red-500" id="singleDateRequired">*</span>
              </label>
              <input 
                type="date" 
                name="event_date" 
                id="eventDate"
                value="{{ old('event_date') }}"
                class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all"
              />
            </div>

            <!-- Start Date (Multi Day) -->
            <div id="startDateWrapper">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.startDate') }} <span class="text-red-500" id="startDateRequired">*</span>
              </label>
              <input 
                type="date" 
                name="start_date" 
                id="startDate"
                value="{{ old('start_date') }}"
                class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
              />
            </div>

            <!-- End Date (Multi Day) -->
            <div id="endDateWrapper">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('admin.endDate') }} <span class="text-red-500" id="endDateRequired">*</span>
              </label>
              <input 
                type="date" 
                name="end_date" 
                id="endDate"
                value="{{ old('end_date') }}"
                class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
              />
            </div>
          </div>

          <!-- Description -->
          <div class="mt-5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
              {{ __('admin.description') }}
              <span class="text-gray-400 text-xs font-normal ml-1">({{ __('admin.optional') }})</span>
            </label>
            <textarea 
              name="description" 
              rows="3"
              placeholder="{{ __('admin.descriptionPlaceholder') }}"
              class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all resize-none"
            >{{ old('description') }}</textarea>
          </div>
        </div>

        <!-- Participant Upload Card -->
        <div class="glass-card-strong rounded-2xl p-6 md:p-8">
          <h2 class="text-xl font-semibold text-[#222223] dark:text-[#FEFEFE] mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#B62A2D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            {{ __('admin.participantData') }}
          </h2>

          <!-- Download Template Hint -->
          <div class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start gap-2">
              <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div>
                <p class="text-sm text-blue-700 dark:text-blue-400">
                  {{ __('admin.templateHintNew') }} 
                </p>
                <a href="{{ route('admin.events.template') }}" data-no-loading class="inline-flex items-center gap-1 mt-2 text-sm font-semibold text-blue-700 dark:text-blue-400 hover:underline">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                  </svg>
                  {{ __('admin.downloadTemplateHere') }}
                </a>
              </div>
            </div>
          </div>

          <!-- File Upload Area -->
          <div class="relative">
            <input 
              type="file" 
              id="excel_file" 
              name="excel_file" 
              accept=".xlsx,.xls,.csv"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
              required
              onchange="updateFileName(this)"
            />
            <div id="uploadArea" class="border-2 border-dashed border-gray-300 dark:border-[#4D4D4E] rounded-xl p-8 text-center hover:border-[#B62A2D] transition-colors">
              <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              <p class="text-gray-600 dark:text-gray-300 mb-2" id="fileNameDisplay">{{ __('admin.dragDropExcel') }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.excelFormats') }}</p>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-4">
          <a href="{{ route('admin.events.index') }}" 
             class="flex-1 py-3 px-4 bg-gray-200 dark:bg-[#3D3D3E] text-gray-700 dark:text-gray-200 rounded-lg font-semibold text-center hover:bg-gray-300 dark:hover:bg-[#4D4D4E] transition-all duration-300">
            {{ __('admin.cancel') }}
          </a>
          <button 
            type="submit" 
            class="flex-1 py-3 px-4 bg-[#B62A2D] text-white rounded-lg font-semibold hover:bg-[#d5575e] transform hover:scale-[1.02] transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            {{ __('admin.uploadBtn') }}
          </button>
        </div>
      </form>
    </div>
  </div>
  
  @include('partials.footer')
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const durationTypeRadios = document.querySelectorAll('input[name="duration_type"]');
  const eventDateInput = document.getElementById('eventDate');
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');
  const singleDateRequired = document.getElementById('singleDateRequired');
  const startDateRequired = document.getElementById('startDateRequired');
  const endDateRequired = document.getElementById('endDateRequired');

  function toggleDateFields() {
    const selectedType = document.querySelector('input[name="duration_type"]:checked').value;
    
    if (selectedType === 'single') {
      // Enable single date, disable multi-day dates
      eventDateInput.disabled = false;
      eventDateInput.required = true;
      singleDateRequired.classList.remove('hidden');
      
      startDateInput.disabled = true;
      startDateInput.required = false;
      startDateInput.value = '';
      startDateRequired.classList.add('hidden');
      
      endDateInput.disabled = true;
      endDateInput.required = false;
      endDateInput.value = '';
      endDateRequired.classList.add('hidden');
    } else {
      // Disable single date, enable multi-day dates
      eventDateInput.disabled = true;
      eventDateInput.required = false;
      eventDateInput.value = '';
      singleDateRequired.classList.add('hidden');
      
      startDateInput.disabled = false;
      startDateInput.required = true;
      startDateRequired.classList.remove('hidden');
      
      endDateInput.disabled = false;
      endDateInput.required = true;
      endDateRequired.classList.remove('hidden');
    }
  }

  // Initialize on page load
  toggleDateFields();

  // Listen for radio button changes
  durationTypeRadios.forEach(radio => {
    radio.addEventListener('change', toggleDateFields);
  });
});

function updateFileName(input) {
  const fileNameDisplay = document.getElementById('fileNameDisplay');
  const uploadArea = document.getElementById('uploadArea');
  if (input.files && input.files[0]) {
    fileNameDisplay.textContent = input.files[0].name;
    fileNameDisplay.classList.add('text-[#B62A2D]', 'font-semibold');
    uploadArea.classList.add('border-[#B62A2D]', 'bg-red-50', 'dark:bg-red-900/10');
  } else {
    fileNameDisplay.textContent = '{{ __("admin.dragDropExcel") }}';
    fileNameDisplay.classList.remove('text-[#B62A2D]', 'font-semibold');
    uploadArea.classList.remove('border-[#B62A2D]', 'bg-red-50', 'dark:bg-red-900/10');
  }
}
</script>

@section('hide_footer', true)
@endsection
