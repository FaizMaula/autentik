@extends('layouts.app')

@section('content')
<!-- Background Wrapper Extended to include footer -->
<section class="relative min-h-screen flex flex-col pt-20 overflow-hidden">
  <!-- Enhanced Animated Background Component -->
  @include('components.animated-background', ['showWatermark' => true])

  <!-- Content wrapper with flex-grow to center the form -->
  <div class="flex-grow flex items-center justify-center py-8">
    <div class="container mx-auto px-6 relative z-10">
      <div class="max-w-lg mx-auto">
        <!-- Login Card -->
        <div class="glass-card-strong rounded-2xl p-6 md:p-8 animate-fade-in">
          <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-2">{{ __('auth.loginTitle') }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ __('auth.loginSubtitle') }}</p>
          </div>

          <!-- Error Messages will be shown as Toast -->
          @if ($errors->any())
            <div id="serverErrors" data-errors='@json($errors->all())' class="hidden"></div>
          @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
          @csrf

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('auth.email') }}</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 fill-gray-700 dark:fill-gray-400" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </div>
              <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}"
                class="w-full pl-10 pr-4 py-3 glass-input rounded-lg focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror" 
                placeholder="{{ __('auth.emailPlaceholder') }}"
                required 
                autofocus
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('auth.password') }}</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 fill-gray-700 dark:fill-gray-400" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM9 8V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9z"/>
                </svg>
              </div>
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="w-full pl-10 pr-12 py-3 glass-input rounded-lg focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror" 
                placeholder="{{ __('auth.passwordPlaceholder') }}"
                required
              />
              <button 
                type="button" 
                id="togglePassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer z-20 hover:opacity-80 transition-opacity"
                onclick="togglePasswordVisibility('password', this)"
              >
                <svg class="w-5 h-5 fill-gray-500 dark:fill-gray-400 eye-open" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                </svg>
                <svg class="w-5 h-5 fill-gray-500 dark:fill-gray-400 eye-closed hidden" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            class="w-full py-3 px-4 bg-[#222223] dark:bg-[#B62A2D] text-white rounded-lg font-semibold text-lg hover:bg-[#333334] dark:hover:bg-[#9a2426] transform hover:scale-[1.02] transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 glow-red relative overflow-hidden group"
          >
            <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
            {{ __('auth.loginBtn') }}
          </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
          <p class="text-gray-600 dark:text-gray-400">
            {{ __('auth.noAccount') }} 
            <a href="{{ route('register') }}" class="text-[#B62A2D] font-semibold hover:underline">{{ __('auth.registerLink') }}</a>
          </p>
        </div>
      </div>
    </div>
  </div>
  </div>
  
  <!-- Footer Section - Seamlessly integrated with page background -->
  @include('partials.footer')
</section>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-24 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>

<script>
// Toggle password visibility function
function togglePasswordVisibility(inputId, btn) {
  var input = document.getElementById(inputId);
  var eyeOpen = btn.querySelector('.eye-open');
  var eyeClosed = btn.querySelector('.eye-closed');
  
  if (input) {
    if (input.type === 'password') {
      input.type = 'text';
      if (eyeOpen) eyeOpen.classList.add('hidden');
      if (eyeClosed) eyeClosed.classList.remove('hidden');
    } else {
      input.type = 'password';
      if (eyeOpen) eyeOpen.classList.remove('hidden');
      if (eyeClosed) eyeClosed.classList.add('hidden');
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {

  var form = document.querySelector('form');
  
  // Field labels for display
  const fieldLabels = {
    'email': '{{ __("auth.email") }}',
    'password': '{{ __("auth.password") }}'
  };

  // Show toast notification
  function showToast(errors) {
    const container = document.getElementById('toastContainer');
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
          <h4 class="font-semibold text-[#222223] dark:text-[#FEFEFE] text-sm mb-2">{{ __('auth.validationError') ?? 'Periksa Data Anda' }}</h4>
          <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
            ${errors.map(err => `<li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>${err}</li>`).join('')}
          </ul>
        </div>
        <button onclick="this.closest('#toastContainer > div').remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
      if (toast.parentNode) {
        toast.classList.add('animate-slide-out-right');
        setTimeout(() => toast.remove(), 300);
      }
    }, 5000);
  }

  // Add/remove error state
  function addErrorState(field) {
    field.classList.add('border-red-500', 'ring-2', 'ring-red-500/30', 'animate-shake');
    setTimeout(() => field.classList.remove('animate-shake'), 500);
  }
  function removeErrorState(field) {
    field.classList.remove('border-red-500', 'ring-2', 'ring-red-500/30', 'animate-shake');
  }

  // Input listeners
  form.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', () => removeErrorState(input));
  });

  // Show server errors as toast
  const serverErrors = document.getElementById('serverErrors');
  if (serverErrors) {
    const errors = JSON.parse(serverErrors.dataset.errors);
    if (errors.length > 0) {
      setTimeout(() => showToast(errors), 100);
    }
  }

  // Client-side validation
  form.addEventListener('submit', function(e) {
    var errors = [];
    var firstErrorField = null;

    form.querySelectorAll('input[required]').forEach(function(input) {
      if (!input.value.trim()) {
        var label = fieldLabels[input.name] || input.name;
        errors.push(label + ' wajib diisi');
        addErrorState(input);
        if (!firstErrorField) firstErrorField = input;
      }
    });

    if (errors.length > 0) {
      e.preventDefault();
      showToast(errors);
      if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstErrorField.focus();
      }
    }
  });
});
</script>

{{-- Hide the default footer from layout since it's included above --}}
@section('hide_footer', true)
@endsection
