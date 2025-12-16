<header data-app-header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-[#000033]">
  <div class="container mx-auto px-6 py-4">
    <div class="flex items-center justify-between">
      <!-- Logo -->
      <a href="/" class="flex items-center gap-3">
        <img src="https://customer-assets.emergentagent.com/job_cd2810c3-bd90-4983-af99-b7abbf45f0c5/artifacts/sa2vlsjk_Logo%20ONLY.png" alt="Autentik Logo" class="h-10 w-10 object-contain" />
        <div class="flex flex-col">
          <span class="text-white text-xl font-bold tracking-wide">AUTENTIK</span>
          <span class="text-gray-300 text-xs">{{ __('nav.tagline') }}</span>
        </div>
      </a>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center gap-8">
  <a href="/" class="text-sm font-medium text-gray-300 hover:text-white transition-all">{{ __('nav.home') }}</a>
  <a href="/#about-section" data-scroll-about class="text-sm font-medium text-gray-300 hover:text-white transition-all">{{ __('nav.aboutUs') }}</a>
        <div class="relative ms-0 self-center mt-[2px]" data-lang>
          <button type="button" data-lang-toggle aria-haspopup="true" aria-expanded="false" class="inline-flex items-center justify-center w-8 h-8 md:w-9 md:h-9 rounded-full overflow-hidden border-2 border-gray-500 hover:border-[#4A7C87] focus:outline-none focus:ring-2 focus:ring-[#4A7C87] bg-white/10">
            <img src="{{ app()->getLocale()==='id' ? '/flags/indonesia.png' : '/flags/united-states.png' }}" alt="Current language" class="block w-full h-full object-cover" loading="lazy" />
          </button>
          <div data-lang-menu class="hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-xl p-2 z-50">
            <a href="/locale/en" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">
              <img src="/flags/united-states.png" alt="English" class="w-5 h-5 rounded-full object-cover" />
              <span class="text-sm text-gray-800">{{ __('nav.english') }}</span>
              @if(app()->getLocale()==='en')
                <i data-lucide="check" class="w-4 h-4 text-[#4A7C87] ml-auto"></i>
              @endif
            </a>
            <a href="/locale/id" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">
              <img src="/flags/indonesia.png" alt="Bahasa" class="w-5 h-5 rounded-full object-cover" />
              <span class="text-sm text-gray-800">{{ __('nav.bahasa') }}</span>
              @if(app()->getLocale()==='id')
                <i data-lucide="check" class="w-4 h-4 text-[#4A7C87] ml-auto"></i>
              @endif
            </a>
          </div>
        </div>
      </nav>

      <!-- Mobile Menu Button -->
      <button id="mobileMenuButton" class="md:hidden text-white" aria-label="Toggle menu">
        <i data-lucide="menu" style="width:24px;height:24px"></i>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden mt-4 pb-4 border-t border-gray-700 hidden">
      <nav class="flex flex-col gap-4 mt-4">
  <a href="/" class="text-left text-gray-300 hover:text-white transition-colors">{{ __('nav.home') }}</a>
  <a href="/#about-section" data-scroll-about class="text-left text-gray-300 hover:text-white transition-colors">{{ __('nav.aboutUs') }}</a>
        <div class="relative mt-2 w-fit" data-lang>
          <button type="button" data-lang-toggle aria-haspopup="true" aria-expanded="false" class="inline-flex items-center justify-center w-9 h-9 rounded-full overflow-hidden border-2 border-gray-500 hover:border-[#4A7C87] focus:outline-none focus:ring-2 focus:ring-[#4A7C87] bg-white/10">
            <img src="{{ app()->getLocale()==='id' ? '/flags/indonesia.png' : '/flags/united-states.png' }}" alt="Current language" class="block w-full h-full object-cover" loading="lazy" />
          </button>
          <div data-lang-menu class="hidden absolute left-0 mt-2 w-44 bg-white rounded-lg shadow-xl p-2 z-50">
            <a href="/locale/en" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">
              <img src="/flags/united-states.png" alt="English" class="w-5 h-5 rounded-full object-cover" />
              <span class="text-sm text-gray-800">{{ __('nav.english') }}</span>
              @if(app()->getLocale()==='en')
                <i data-lucide="check" class="w-4 h-4 text-[#4A7C87] ml-auto"></i>
              @endif
            </a>
            <a href="/locale/id" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">
              <img src="/flags/indonesia.png" alt="Bahasa" class="w-5 h-5 rounded-full object-cover" />
              <span class="text-sm text-gray-800">{{ __('nav.bahasa') }}</span>
              @if(app()->getLocale()==='id')
                <i data-lucide="check" class="w-4 h-4 text-[#4A7C87] ml-auto"></i>
              @endif
            </a>
          </div>
        </div>
      </nav>
    </div>
  </div>
</header>
