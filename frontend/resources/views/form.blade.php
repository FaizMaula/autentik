@extends('layouts.app')
@section('content')
<!-- Background Wrapper Extended to include footer -->
<div class="min-h-screen pt-24 pb-0 relative overflow-hidden">
  <!-- Enhanced Animated Background Component -->
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
        <span class="font-medium">{{ __('form.title') }}</span>
      </div>
      <h1 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4">{{ __('form.title') }}</h1>
      <p class="text-lg text-gray-700 dark:text-gray-300">{{ __('form.subtitle') }}</p>
    </div>

    <!-- Form Card -->
    <div class="max-w-6xl mx-auto">
      <div class="glass-card-strong rounded-2xl p-8 md:p-12 animate-fade-in">

        {{-- Laravel Validation Errors Display --}}
        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 rounded-xl">
          <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
              <h4 class="font-semibold text-red-700 dark:text-red-400 mb-2">Validation Error (Server)</h4>
              <ul class="text-sm text-red-600 dark:text-red-300 space-y-1">
                @foreach ($errors->all() as $error)
                  <li>• {{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
        @endif

        <form id="certForm" method="POST" action="{{ route('certificate.store') }}" enctype="multipart/form-data">
        @csrf
        
        <!-- Certificate Type Toggle -->
        <div class="mb-8">
          <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
            {{ __('form.certificateType') }} <span class="text-red-500">*</span>
          </label>
          <div class="flex gap-4">
            <label class="flex-1 cursor-pointer">
              <input type="radio" name="certificate_type" value="internal" class="peer hidden" checked />
              <div class="peer-checked:bg-[#B62A2D] peer-checked:text-white peer-checked:border-[#B62A2D] bg-white dark:bg-[#333334] border-2 border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center transition-all duration-300 hover:border-[#B62A2D]">
                <div class="flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                  <span class="font-semibold">{{ __('form.internal') }}</span>
                </div>
                <p class="text-xs mt-1 opacity-80">{{ __('form.internalDesc') }}</p>
              </div>
            </label>
            <label class="flex-1 cursor-pointer">
              <input type="radio" name="certificate_type" value="external" class="peer hidden" />
              <div class="peer-checked:bg-[#B62A2D] peer-checked:text-white peer-checked:border-[#B62A2D] bg-white dark:bg-[#333334] border-2 border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center transition-all duration-300 hover:border-[#B62A2D]">
                <div class="flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                  </svg>
                  <span class="font-semibold">{{ __('form.external') }}</span>
                </div>
                <p class="text-xs mt-1 opacity-80">{{ __('form.externalDesc') }}</p>
              </div>
            </label>
          </div>
        </div>

        <!-- Two Column Layout: Left and Right -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- LEFT COLUMN -->
          <div id="leftColumn" class="space-y-5">
            <!-- Name -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('form.name') }} <span class="text-red-500">*</span>
              </label>
              <input type="text" name="nama" placeholder="{{ __('form.namePlaceholder') }}" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" required />
            </div>

            <!-- NIM Field (Only for Internal) -->
            <div id="nimField" class="transition-all duration-300">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                {{ __('form.nim') }} <span class="text-red-500">*</span>
              </label>
              <input type="text" name="nim" id="nimInput" placeholder="{{ __('form.nimPlaceholder') }}" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" />
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('form.nimHint') }}</p>
            </div>

            <!-- Academic Year + Organizer (2 columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Academic Year -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('form.academicYear') }}</label>
                <input type="text" name="tahun_akademik" placeholder="{{ __('form.academicYearPlaceholder') }}" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" />
              </div>

              <!-- Organizer -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                  {{ __('form.organizer') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" name="penyelenggara" placeholder="{{ __('form.organizerPlaceholder') }}" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" required />
              </div>
            </div>

            <!-- Start Date + End Date (2 columns) - Movable for Internal -->
            <div id="dateFieldsWrapper" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('form.startDate') }} <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_mulai" id="startDate" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" required />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('form.endDate') }} <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_selesai" id="endDate" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" required />
              </div>
            </div>

            <!-- Event Name (ID) - Dropdown for Internal, Text Input for External - Movable for Internal -->
            <div id="eventNameFieldsWrapper">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('form.eventName') }} <span class="text-red-500">*</span></label>
              
              <!-- Dropdown for Internal Certificate -->
              <div id="eventNameDropdownWrapper">
                <select name="nama_kegiatan" id="eventNameDropdown" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" required>
                  <option value="">{{ __('form.selectEvent') }}</option>
                  @foreach($events as $event)
                    <option value="{{ $event->event_name }}" 
                            data-event-name-en="{{ $event->event_name_en }}"
                            data-organizer="{{ $event->organizer }}"
                            data-start-date="{{ $event->start_date ? $event->start_date->format('Y-m-d') : '' }}"
                            data-end-date="{{ $event->end_date ? $event->end_date->format('Y-m-d') : '' }}">
                      {{ $event->event_name }}
                    </option>
                  @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('form.eventNameInternalHint') }}</p>
              </div>
              
              <!-- Text Input for External Certificate -->
              <div id="eventNameTextWrapper" class="hidden">
                <input type="text" name="nama_kegiatan_external" id="eventNameText" placeholder="{{ __('form.eventNamePlaceholder') }}" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" />
              </div>
            </div>

            <!-- Event Name (EN) - Part of movable wrapper -->
            <div id="eventNameEngWrapper" class="mt-5">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('form.eventNameEng') }}</label>
              <input type="text" name="nama_kegiatan_inggris" id="eventNameEng" placeholder="{{ __('form.eventNameEngPlaceholder') }}" class="w-full px-4 py-3 rounded-lg glass-input focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all" />
            </div>
          </div>

          <!-- RIGHT COLUMN -->
          <div id="rightColumn" class="space-y-5">
            <!-- Container for Internal-only fields (moved from left column) -->
            <div id="internalFieldsContainer" class="space-y-5 hidden"></div>
            
            <!-- File Upload (External Only) -->
            <div id="fileUploadSection">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('form.fileUpload') }} <span class="text-red-500">*</span></label>
              <div id="fileDropZone" class="relative glass-input border-2 border-dashed border-[#B62A2D]/30 rounded-2xl p-6 text-center hover:border-[#B62A2D] transition-all duration-300 hover:bg-white/80 dark:hover:bg-[#333334]/80">
                <!-- Drag-over overlay -->
                <div id="dropOverlay" class="pointer-events-none hidden absolute inset-0 flex flex-col items-center justify-center gap-2 rounded-2xl bg-[#222223]/80 backdrop-blur-md border border-dashed border-[#B62A2D]">
                  <i data-lucide="paperclip" class="text-[#B62A2D]" style="width:32px;height:32px"></i>
                  <span class="text-sm md:text-base text-[#FEFEFE] font-medium tracking-wide">{{ __('form.dropHere') }}</span>
                </div>
                <input type="file" id="fileInput" name="berkas" accept=".jpg,.jpeg,.png,.pdf" class="hidden" />
                <div id="fileDropper" class="cursor-pointer">
                  <i data-lucide="upload" class="mx-auto mb-3 text-gray-400" style="width:40px;height:40px"></i>
                  <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ __('form.fileUploadDesc') }}</p>
                  <button type="button" onclick="document.getElementById('fileInput').click()" class="mt-2 px-5 py-2 bg-[#B62A2D] text-white text-sm rounded-lg hover:bg-[#9a2426] transition-colors">{{ __('form.chooseFile') }}</button>
                </div>
                <div id="filePreviewContainer" class="mt-4"></div>
                <button type="button" id="removeFileButton" class="hidden mt-4 px-4 py-2 bg-[#B62A2D] text-white rounded-lg hover:bg-[#9a2426] transition-colors mx-auto">{{ __('form.removeFile') }}</button>
              </div>
            </div>

            <!-- Confirmation Checkbox -->
            <div class="flex items-center gap-3 pt-2">
              <input type="checkbox" id="confirmData" name="confirmData" class="h-5 w-5 text-[#B62A2D] border-gray-300 rounded focus:ring-[#B62A2D]" required />
              <label for="confirmData" class="text-sm text-gray-700 dark:text-gray-300">{{ __('form.confirmData') }}</label>
            </div>

            <!-- Action Buttons (Smaller, Horizontal) -->
            <div class="flex flex-row gap-3 pt-2">
              <button id="submitBtn" type="button" class="flex-1 px-4 py-3 bg-[#222223] dark:bg-[#B62A2D] text-white rounded-lg font-semibold text-sm flex items-center justify-center gap-2 hover:bg-[#333334] dark:hover:bg-[#d5575e] transform transition-all duration-300 shadow-lg glow-red relative overflow-hidden group">
                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                <i data-lucide="send" style="width:16px;height:16px"></i>
                {{ __('form.submitBtn') }}
              </button>
              <a href="/form" class="flex-1 px-4 py-3 glass-card hover:bg-white/80 dark:hover:bg-[#3D3D3E]/80 border-2 border-[#B62A2D]/30 hover:border-[#B62A2D] text-[#B62A2D] rounded-lg font-semibold text-sm flex items-center justify-center gap-2 transition-all duration-300">
                <i data-lucide="rotate-ccw" style="width:16px;height:16px"></i>
                {{ __('form.resetBtn') }}
              </a>
            </div>
          </div>
        </div>
        </form>
      </div>

      <!-- Processing Modal with Sheriff Icon -->
      <div id="processingModal" class="fixed inset-0 z-50 hidden items-center justify-center glass-overlay">
        <div class="glass-card-strong rounded-2xl px-8 py-6 max-w-sm w-full flex flex-col items-center text-center gap-4 animate-fade-in">
          <img src="/assets/sheriff.png" alt="Loading" class="w-16 h-16 animate-spin" style="animation: sheriff-spin 1.5s linear infinite; filter: drop-shadow(0 4px 12px rgba(182, 42, 45, 0.3));" />
          <div>
            <p class="text-lg font-semibold text-[#222223] dark:text-[#FEFEFE]">{{ __('form.processingTitle') }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ __('form.processingDesc') }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Section - Inside z-10 container for proper layering -->
    @include('partials.footer')
  </div>
</div>

{{-- Hide the default footer from layout since it's included above --}}
@section('hide_footer', true)

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-24 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>

<!-- Custom Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('certForm');
  const fileInput = document.getElementById('fileInput');
  const nimField = document.getElementById('nimField');
  const nimInput = document.getElementById('nimInput');
  const certificateTypeRadios = document.querySelectorAll('input[name="certificate_type"]');
  
  // Event name elements
  const eventNameDropdownWrapper = document.getElementById('eventNameDropdownWrapper');
  const eventNameTextWrapper = document.getElementById('eventNameTextWrapper');
  const eventNameDropdown = document.getElementById('eventNameDropdown');
  const eventNameText = document.getElementById('eventNameText');
  const eventNameEng = document.getElementById('eventNameEng');
  const organizerInput = document.querySelector('input[name="penyelenggara"]');
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');
  
  // File upload section (only for External)
  const fileUploadSection = document.getElementById('fileUploadSection');
  
  // Layout elements for Internal fields relocation
  const leftColumn = document.getElementById('leftColumn');
  const rightColumn = document.getElementById('rightColumn');
  const internalFieldsContainer = document.getElementById('internalFieldsContainer');
  const dateFieldsWrapper = document.getElementById('dateFieldsWrapper');
  const eventNameFieldsWrapper = document.getElementById('eventNameFieldsWrapper');
  const eventNameEngWrapper = document.getElementById('eventNameEngWrapper');
  
  // Field label mapping for display names
  const fieldLabels = {
    'nama': '{{ __("form.name") }}',
    'nim': '{{ __("form.nim") }}',
    'penyelenggara': '{{ __("form.organizer") }}',
    'tanggal_mulai': '{{ __("form.startDate") }}',
    'tanggal_selesai': '{{ __("form.endDate") }}',
    'nama_kegiatan': '{{ __("form.eventName") }}',
    'confirmData': '{{ __("form.confirmData") }}',
    'fileInput': '{{ __("form.fileUpload") }}'
  };

  // Toggle NIM field and Event Name field based on certificate type
  function toggleFieldsBasedOnType() {
    const selectedType = document.querySelector('input[name="certificate_type"]:checked').value;
    
    if (selectedType === 'internal') {
      // Show NIM field
      nimField.classList.remove('hidden');
      nimInput.setAttribute('required', 'true');
      
      // Show dropdown, hide text input
      eventNameDropdownWrapper.classList.remove('hidden');
      eventNameTextWrapper.classList.add('hidden');
      eventNameDropdown.setAttribute('required', 'true');
      eventNameDropdown.setAttribute('name', 'nama_kegiatan');
      eventNameText.removeAttribute('required');
      eventNameText.removeAttribute('name');
      
      // Hide file upload for Internal (verification via database only)
      fileUploadSection.classList.add('hidden');
      fileInput.removeAttribute('required');
      // Clear any selected file
      fileInput.value = '';
      const dropZone = document.getElementById('fileDropZone');
      if (dropZone) removeErrorState(dropZone);
      
      // Move date and event fields to right column for Internal layout
      if (dateFieldsWrapper && eventNameFieldsWrapper && eventNameEngWrapper && internalFieldsContainer) {
        internalFieldsContainer.classList.remove('hidden');
        internalFieldsContainer.appendChild(dateFieldsWrapper);
        internalFieldsContainer.appendChild(eventNameFieldsWrapper);
        internalFieldsContainer.appendChild(eventNameEngWrapper);
      }
    } else {
      // Hide NIM field
      nimField.classList.add('hidden');
      nimInput.removeAttribute('required');
      nimInput.value = '';
      removeErrorState(nimInput);
      
      // Hide dropdown, show text input
      eventNameDropdownWrapper.classList.add('hidden');
      eventNameTextWrapper.classList.remove('hidden');
      eventNameDropdown.removeAttribute('required');
      eventNameDropdown.removeAttribute('name');
      eventNameText.setAttribute('required', 'true');
      eventNameText.setAttribute('name', 'nama_kegiatan');
      
      // Show file upload for External (OCR/AI verification)
      fileUploadSection.classList.remove('hidden');
      fileInput.setAttribute('required', 'true');
      
      // Move date and event fields back to left column for External layout
      if (dateFieldsWrapper && eventNameFieldsWrapper && eventNameEngWrapper && leftColumn && internalFieldsContainer) {
        internalFieldsContainer.classList.add('hidden');
        // Find the NIM field to insert after
        const nimFieldElement = document.getElementById('nimField');
        const academicYearField = nimFieldElement.nextElementSibling;
        // Insert date fields after academic year/organizer row
        if (academicYearField && academicYearField.nextSibling) {
          leftColumn.insertBefore(dateFieldsWrapper, academicYearField.nextSibling);
          leftColumn.insertBefore(eventNameFieldsWrapper, dateFieldsWrapper.nextSibling);
          leftColumn.insertBefore(eventNameEngWrapper, eventNameFieldsWrapper.nextSibling);
        } else {
          leftColumn.appendChild(dateFieldsWrapper);
          leftColumn.appendChild(eventNameFieldsWrapper);
          leftColumn.appendChild(eventNameEngWrapper);
        }
      }
      
      // Clear auto-filled fields when switching to external
      eventNameDropdown.value = '';
      eventNameEng.value = '';
      eventNameEng.removeAttribute('readonly');
      if (organizerInput) {
        organizerInput.value = '';
        organizerInput.removeAttribute('readonly');
      }
    }
  }
  
  // Auto-fill fields when event is selected from dropdown
  eventNameDropdown.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
      // Auto-fill Event Name English
      const eventNameEnValue = selectedOption.getAttribute('data-event-name-en');
      if (eventNameEnValue) {
        eventNameEng.value = eventNameEnValue;
        eventNameEng.setAttribute('readonly', 'true');
      } else {
        eventNameEng.value = '';
        eventNameEng.removeAttribute('readonly');
      }
      
      // Auto-fill Organizer
      const organizerValue = selectedOption.getAttribute('data-organizer');
      if (organizerValue && organizerInput) {
        organizerInput.value = organizerValue;
        organizerInput.setAttribute('readonly', 'true');
      }
      
      // Auto-fill Start Date
      const startDateValue = selectedOption.getAttribute('data-start-date');
      if (startDateValue && startDateInput) {
        startDateInput.value = startDateValue;
      }
      
      // Auto-fill End Date
      const endDateValue = selectedOption.getAttribute('data-end-date');
      if (endDateValue && endDateInput) {
        endDateInput.value = endDateValue;
      }
      
      // Remove error state from dropdown
      removeErrorState(eventNameDropdown);
    } else {
      // Clear auto-filled fields
      eventNameEng.value = '';
      eventNameEng.removeAttribute('readonly');
      if (organizerInput) {
        organizerInput.value = '';
        organizerInput.removeAttribute('readonly');
      }
    }
  });

  // Add listeners to certificate type radios
  certificateTypeRadios.forEach(radio => {
    radio.addEventListener('change', toggleFieldsBasedOnType);
  });

  // Initialize fields based on type
  toggleFieldsBasedOnType();

  // Remove default browser validation
  form.setAttribute('novalidate', 'true');

  // Create toast notification
  function showToast(errors) {
    const container = document.getElementById('toastContainer');
    
    // Remove existing toasts
    container.innerHTML = '';
    
    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto glass-card-strong rounded-xl p-4 shadow-2xl border border-red-400/30 animate-slide-in-right max-w-sm';
    toast.innerHTML = `
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-red-500/20 rounded-full flex items-center justify-center">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <div class="flex-1">
          <h4 class="font-semibold text-[#222223] dark:text-[#FEFEFE] text-sm mb-2">{{ __('form.validationError') ?? 'Lengkapi Data Berikut' }}</h4>
          <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
            ${errors.map(err => `<li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>${err}</li>`).join('')}
          </ul>
        </div>
        <button onclick="this.closest('#toastContainer > div').remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    `;
    
    container.appendChild(toast);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
      if (toast.parentNode) {
        toast.classList.add('animate-slide-out-right');
        setTimeout(() => toast.remove(), 300);
      }
    }, 5000);
  }

  // Add error state to field
  function addErrorState(field) {
    field.classList.add('border-red-500', 'ring-2', 'ring-red-500/30', 'animate-shake');
    
    // Remove shake after animation
    setTimeout(() => {
      field.classList.remove('animate-shake');
    }, 500);
  }

  // Remove error state from field
  function removeErrorState(field) {
    field.classList.remove('border-red-500', 'ring-2', 'ring-red-500/30', 'animate-shake');
  }

  // Add input listeners to remove error state on input
  const inputs = form.querySelectorAll('input[required]');
  inputs.forEach(input => {
    input.addEventListener('input', () => removeErrorState(input));
    input.addEventListener('change', () => removeErrorState(input));
  });

  // File input listener
  fileInput.addEventListener('change', () => {
    const dropZone = document.getElementById('fileDropZone');
    removeErrorState(dropZone);
  });

  // Checkbox listener
  const checkbox = document.getElementById('confirmData');
  checkbox.addEventListener('change', () => removeErrorState(checkbox));

  // Submit button click handler (NOT form submit - full control, no race conditions)
  const submitBtn = document.getElementById('submitBtn');
  submitBtn.addEventListener('click', function() {
    const errors = [];
    let firstErrorField = null;
    
    // Get current certificate type
    const selectedType = document.querySelector('input[name="certificate_type"]:checked').value;

    // Re-query inputs fresh each time to get current required state
    const currentInputs = form.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="hidden"])');
    
    // Check required text/date inputs
    currentInputs.forEach(input => {
      // Skip NIM validation for external certificates
      if (input.name === 'nim' && selectedType !== 'internal') {
        return;
      }
      
      // Skip non-required fields
      if (!input.hasAttribute('required') && !(input.name === 'nim' && selectedType === 'internal')) {
        return;
      }
      
      // Check if field is required and empty
      const isNimRequired = input.name === 'nim' && selectedType === 'internal';
      const isOtherRequired = input.hasAttribute('required');
      
      if ((isNimRequired || isOtherRequired) && !input.value.trim()) {
        const label = fieldLabels[input.name] || input.name;
        if (!errors.includes(label)) {
          errors.push(label);
        }
        addErrorState(input);
        if (!firstErrorField) firstErrorField = input;
      }
    });

    // Check event name based on certificate type
    if (selectedType === 'internal') {
      // Check dropdown for internal
      if (!eventNameDropdown.value) {
        const label = fieldLabels['nama_kegiatan'];
        if (!errors.includes(label)) {
          errors.push(label);
        }
        addErrorState(eventNameDropdown);
        if (!firstErrorField) firstErrorField = eventNameDropdown;
      }
    } else {
      // Check text input for external
      if (!eventNameText.value.trim()) {
        const label = fieldLabels['nama_kegiatan'];
        if (!errors.includes(label)) {
          errors.push(label);
        }
        addErrorState(eventNameText);
        if (!firstErrorField) firstErrorField = eventNameText;
      }
    }

    // Check file input (only for External certificates)
    if (selectedType === 'external') {
      if (!fileInput.files || fileInput.files.length === 0) {
        const dropZone = document.getElementById('fileDropZone');
        errors.push(fieldLabels['fileInput']);
        addErrorState(dropZone);
        if (!firstErrorField) firstErrorField = dropZone;
      }
    }

    // Check checkbox
    if (!checkbox.checked) {
      errors.push(fieldLabels['confirmData']);
      addErrorState(checkbox);
      if (!firstErrorField) firstErrorField = checkbox;
    }

    // If errors exist, show toast and DO NOT proceed
    if (errors.length > 0) {
      showToast(errors);
      
      // Scroll to first error field
      if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        if (firstErrorField.focus) firstErrorField.focus();
      }
      
      // Stop here - do NOT show loading or submit
      return;
    }
    
    // === ALL VALIDATION PASSED ===
    // Show processing modal FIRST
    const processingModal = document.getElementById('processingModal');
    if (processingModal) {
      processingModal.classList.remove('hidden');
      processingModal.classList.add('flex');
    }
    
    // Disable button to prevent double-click
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.7';
    submitBtn.style.cursor = 'not-allowed';
    
    // Submit the form programmatically
    form.submit();
  });
});
</script>

<style>
/* Shake Animation */
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
  20%, 40%, 60%, 80% { transform: translateX(4px); }
}

.animate-shake {
  animation: shake 0.5s ease-in-out;
}

/* Slide In Right Animation */
@keyframes slide-in-right {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slide-in-right {
  animation: slide-in-right 0.3s ease-out forwards;
}

/* Slide Out Right Animation */
@keyframes slide-out-right {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}

.animate-slide-out-right {
  animation: slide-out-right 0.3s ease-in forwards;
}

/* Error state for file drop zone */
#fileDropZone.border-red-500 {
  border-color: rgb(239 68 68) !important;
}

/* Sheriff Spin Animation for Processing Modal */
@keyframes sheriff-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Dark mode sheriff icon */
.dark #processingModal img {
  filter: invert(1) drop-shadow(0 4px 12px rgba(182, 42, 45, 0.5)) !important;
}
</style>
@endsection
