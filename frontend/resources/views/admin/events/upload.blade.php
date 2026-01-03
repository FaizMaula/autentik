@extends('layouts.app')

@section('content')
<section class="relative min-h-screen flex flex-col pt-20 pb-0 overflow-hidden">
  @include('components.animated-background', ['showWatermark' => true])

  <div class="flex-grow py-8  px-3 md:px-4">
    <div class="max-w-7xl mx-auto px-4 md:px-6 relative z-10">
      <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-[#B62A2D] transition-colors mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('admin.backToEvents') }}
          </a>
          <h1 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE]">{{ __('admin.uploadEventTitle') }}</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('admin.uploadEventSubtitle') }}</p>
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

          <!-- Excel Upload -->
          <div class="glass-card-strong rounded-2xl p-6 md:p-8">
            <!-- Download Template Hint -->
            <div class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-blue-700 dark:text-blue-400">
                  {{ __('admin.templateHintShort') }} 
                  <a href="{{ route('admin.events.template') }}" data-no-loading class="font-semibold underline hover:no-underline">{{ __('admin.downloadTemplateHere') }}</a>
                </p>
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
  </div>
  
  @include('partials.footer')
</section>

<script>
function updateFileName(input) {
  const fileNameDisplay = document.getElementById('fileNameDisplay');
  if (input.files && input.files[0]) {
    fileNameDisplay.textContent = input.files[0].name;
    fileNameDisplay.classList.add('text-[#B62A2D]', 'font-semibold');
  }
}
</script>

@section('hide_footer', true)
@endsection
