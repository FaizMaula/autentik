@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#C5D3D8] via-[#B8C8CE] to-[#A8B8BE] pt-24 pb-12">
  <div class="container mx-auto px-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-700 mb-6">
      <a href="/" class="hover:text-[#4A7C87] transition-colors">
        <i data-lucide="home" style="width:18px;height:18px"></i>
      </a>
      <span>/</span>
      <span class="font-medium">{{ __('form.title') }}</span>
    </div>

    <!-- Form Card -->
    <div class="relative max-w-4xl mx-auto">
      <a href="/" aria-label="{{ __('form.backToHome') }}" class="absolute -left-6 sm:-left-10 md:-left-14 top-0 inline-flex items-center justify-center border-2 border-[#4A7C87] text-[#4A7C87] rounded-full p-2 hover:bg-[#4A7C87] hover:text-white transition-colors shadow-sm">
        <i data-lucide="arrow-left" style="width:20px;height:20px"></i>
        <span class="sr-only">{{ __('form.backToHome') }}</span>
      </a>

      <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
        <h1 class="text-3xl md:text-4xl font-bold text-[#0F0F10] mb-8">{{ __('form.title') }}</h1>

        <!-- Form -->
        <form action="{{ route('certificate.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
          @csrf

          <!-- Name -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              {{ __('form.name') }} <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama" placeholder="{{ __('form.namePlaceholder') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A7C87] focus:border-transparent transition-all" required />
          </div>

          <!-- Academic Year & Organizer -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('form.academicYear') }}</label>
              <input type="text" name="tahun_akademik" placeholder="{{ __('form.academicYearPlaceholder') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A7C87] focus:border-transparent transition-all" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ __('form.organizer') }} <span class="text-red-500">*</span>
              </label>
              <input type="text" name="penyelenggara" placeholder="{{ __('form.organizerPlaceholder') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A7C87] focus:border-transparent transition-all" required />
            </div>
          </div>

          <!-- Start & End Dates -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('form.startDate') }} <span class="text-red-500">*</span></label>
              <input type="date" name="tanggal_mulai" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A7C87] focus:border-transparent transition-all" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('form.endDate') }} <span class="text-red-500">*</span></label>
              <input type="date" name="tanggal_selesai" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A7C87] focus:border-transparent transition-all" required />
            </div>
          </div>

          <!-- Event Name -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('form.eventName') }} <span class="text-red-500">*</span></label>
            <input type="text" name="nama_kegiatan" placeholder="{{ __('form.eventNamePlaceholder') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A7C87] focus:border-transparent transition-all" required />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('form.eventNameEng') }}</label>
            <input type="text" name="nama_kegiatan_inggris" placeholder="{{ __('form.eventNameEngPlaceholder') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#4A7C87] focus:border-transparent transition-all" />
          </div>

          <!-- File Upload -->
          <!-- File Upload -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              {{ __('form.fileUpload') }} <span class="text-red-500">*</span>
            </label>
            <div id="fileDropZone" class="relative border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-[#4A7C87] transition-colors bg-white/70">
              
              <!-- Drag-over overlay -->
              <div id="dropOverlay" class="pointer-events-none hidden absolute inset-0 flex flex-col items-center justify-center gap-2 rounded-2xl bg-[#000033]/80 backdrop-blur-md border border-dashed border-[#4A7C87]">
                <i data-lucide="paperclip" class="text-[#4A7C87]" style="width:32px;height:32px"></i>
                <span class="text-sm md:text-base text-[#E5EEF1] font-medium tracking-wide">{{ __('form.dropHere') }}</span>
              </div>

              <!-- Hidden file input -->
              <input type="file" id="fileInput" name="berkas" accept=".jpg,.jpeg,.png,.pdf" class="hidden" required />

              <!-- Dropper / Clickable area -->
              <div id="fileDropper" class="cursor-pointer" onclick="document.getElementById('fileInput').click()">
                <i data-lucide="upload" class="mx-auto mb-4 text-gray-400" style="width:48px;height:48px"></i>
                <p class="text-gray-600 mb-2">{{ __('form.fileUploadDesc') }}</p>
                <button type="button" class="mt-4 px-6 py-2 bg-[#4A7C87] text-white rounded-lg hover:bg-[#3A6C77] transition-colors">{{ __('form.chooseFile') }}</button>
              </div>

              <!-- Preview -->
              <div id="filePreviewContainer" class="mt-4 text-gray-700 text-sm"></div>

              <!-- Remove Button -->
              <button type="button" id="removeFileButton" class="hidden mt-4 px-4 py-2 bg-[#4A7C87] text-white rounded-lg hover:bg-[#3A6C77] transition-colors mx-auto">
                {{ __('form.removeFile') }}
              </button>
            </div>
          </div>

          <!-- Confirmation Checkbox -->
          <div class="flex items-center gap-3">
            <input type="checkbox" id="confirmData" name="confirmData" class="h-5 w-5 text-[#4A7C87] border-gray-300 rounded focus:ring-[#4A7C87]" required />
            <label for="confirmData" class="text-sm text-gray-700">{{ __('form.confirmData') }}</label>
          </div>

          <!-- Submit Button -->
          <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button type="submit" class="flex-1 px-6 py-4 bg-[#000033] text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-[#000055] transform hover:scale-105 transition-all duration-300 shadow-lg">
              {{ __('form.submitBtn') }}
            </button>
            <a href="{{ route('certificate.create') }}" class="px-6 py-4 bg-transparent border-2 border-[#4A7C87] text-[#4A7C87] rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-[#4A7C87] hover:text-white transition-all duration-300">
              {{ __('form.resetBtn') }}
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
    <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>

@endif

<!-- Processing Modal -->
<div id="processingModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
  <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 max-w-sm w-full flex flex-col items-center text-center gap-4 animate-fadeIn">
    <!-- Spinner -->
    <div class="w-12 h-12 rounded-full border-4 border-[#4A7C87]/30 border-t-[#4A7C87] animate-spin"></div>
    <!-- Teks -->
    <div>
      <p class="text-lg font-semibold text-[#0F0F10]">{{ __('form.processingTitle') }}</p>
      <p class="text-sm text-gray-600 mt-1">{{ __('form.processingDesc') }}</p>
    </div>
  </div>
</div>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}
</style>

<script>
const form = document.querySelector('form');
const processingModal = document.getElementById('processingModal');

form.addEventListener('submit', (e) => {
  // Tampilkan modal di tengah layar
  processingModal.classList.remove('hidden');

  // Disable tombol submit agar tidak double click
  const submitBtn = form.querySelector('[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
});
</script>


<!-- Preview -->
<div id="filePreviewContainer" class="mt-4 flex flex-col items-center text-gray-700 text-sm"></div>

<script>
const fileInput = document.getElementById('fileInput');
const previewContainer = document.getElementById('filePreviewContainer');
const removeButton = document.getElementById('removeFileButton');

fileInput.addEventListener('change', () => {
  previewContainer.innerHTML = ''; // reset
  const fileDropper = document.getElementById('fileDropper'); // ambil dropper
  if (fileInput.files.length > 0) {
    const file = fileInput.files[0];
    const fileType = file.type;

    // Sembunyikan dropper setelah file dipilih
    fileDropper.classList.add('hidden');

    if (fileType.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex flex-col items-center'; 

        const previewElement = document.createElement('img');
        previewElement.src = e.target.result;
        previewElement.className = 'max-h-80 rounded-lg border border-gray-300';
        wrapper.appendChild(previewElement);

        const label = document.createElement('p');
        label.textContent = `${file.name} (${Math.round(file.size/1024)} KB)`;
        label.className = 'mt-2 text-gray-700';
        wrapper.appendChild(label);

        previewContainer.appendChild(wrapper);
      }
      reader.readAsDataURL(file);

    } else if (fileType === 'application/pdf') {
      const wrapper = document.createElement('div');
      wrapper.className = 'flex flex-col items-center gap-2';

      const previewElement = document.createElement('div');
      previewElement.className = 'flex items-center gap-2';
      previewElement.innerHTML = `
        <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
          <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
        </svg>
        <span>${file.name} (${Math.round(file.size/1024)} KB)</span>
      `;
      wrapper.appendChild(previewElement);
      previewContainer.appendChild(wrapper);

    } else {
      const p = document.createElement('p');
      p.textContent = `${file.name} (${Math.round(file.size/1024)} KB)`;
      previewContainer.appendChild(p);
    }

    removeButton.classList.remove('hidden');
  } else {
    removeButton.classList.add('hidden');
    fileDropper.classList.remove('hidden'); // tampilkan dropper jika tidak ada file
  }
});

removeButton.addEventListener('click', () => {
  fileInput.value = '';
  previewContainer.innerHTML = '';
  removeButton.classList.add('hidden');
  document.getElementById('fileDropper').classList.remove('hidden'); // tampilkan lagi dropper
});



removeButton.addEventListener('click', () => {
  fileInput.value = '';
  previewContainer.innerHTML = '';
  removeButton.classList.add('hidden');
});

// Drag-over effect
const dropZone = document.getElementById('fileDropZone');
const dropOverlay = document.getElementById('dropOverlay');

dropZone.addEventListener('dragover', (e) => {
  e.preventDefault();
  dropOverlay.classList.remove('hidden');
});
dropZone.addEventListener('dragleave', () => dropOverlay.classList.add('hidden'));
dropZone.addEventListener('drop', (e) => {
  e.preventDefault();
  dropOverlay.classList.add('hidden');
  if(e.dataTransfer.files.length > 0){
    fileInput.files = e.dataTransfer.files;
    fileInput.dispatchEvent(new Event('change'));
  }
});
</script>


@endsection
<!-- JS for preview and remove -->