<!-- Floating Header Wrapper -->
<div class="fixed top-0 left-0 right-0 z-50 p-3 md:p-4">
  <header data-app-header class="floating-header mx-auto max-w-7xl rounded-2xl transition-all duration-500 shadow-2xl shadow-black/20 animate-slide-down">
    <div class="px-4 md:px-6 py-3">
      <div class="flex items-center justify-between">
        <!-- Logo - Different behavior for admin vs user -->
        @auth
          @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
          @else
            <a href="/#hero-section" data-nav-home class="flex items-center gap-3 group">
          @endif
        @else
          <a href="/#hero-section" data-nav-home class="flex items-center gap-3 group">
        @endauth
          <div class="relative">
            <div class="absolute inset-0 bg-[#B62A2D]/30 rounded-full blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <img src="{{ asset('assets/logo-autentik.png') }}" alt="Autentik Logo" class="h-9 w-9 md:h-10 md:w-10 object-contain relative z-10 transition-transform duration-300 group-hover:scale-110" />
          </div>
          <div class="flex flex-col">
            <span class="text-white text-lg md:text-xl font-bold tracking-wide group-hover:text-[#B62A2D] dark:group-hover:text-gray-400 transition-colors duration-300">AUTENTIK</span>
            <span class="text-gray-200 text-[10px] md:text-xs hidden sm:block">{{ __('nav.tagline') }}</span>
          </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-2">
          <!-- Nav Links - Only show for non-admin users -->
          @auth
            @if(Auth::user()->isAdmin())
              {{-- Admin: No navigation links in header (use dashboard cards instead) --}}
            @else
              <!-- User Navigation Links -->
              <a href="/#hero-section" data-nav-home class="relative z-10 px-4 py-2 text-sm font-medium text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.home') }}</a>
              <a href="/#about-section" data-nav-about class="relative z-10 px-4 py-2 text-sm font-medium text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.aboutUs') }}</a>
            @endif
          @else
            <!-- Guest Navigation Links -->
            <a href="/#hero-section" data-nav-home class="relative z-10 px-4 py-2 text-sm font-medium text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.home') }}</a>
            <a href="/#about-section" data-nav-about class="relative z-10 px-4 py-2 text-sm font-medium text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.aboutUs') }}</a>
          @endauth
          
          <!-- Separator -->
          <div class="w-px h-6 bg-white/20 mx-2"></div>
          
          <div class="relative ms-0 self-center" data-lang>
            <button type="button" data-lang-toggle aria-haspopup="true" aria-expanded="false" class="inline-flex items-center justify-center w-9 h-9 rounded-xl overflow-hidden border border-white/20 hover:border-[#B62A2D] hover:bg-white/10 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-white/5 transition-all duration-300">
              <img src="{{ app()->getLocale()==='id' ? '/flags/indonesia.png' : '/flags/united-states.png' }}" alt="Current language" class="block w-5 h-5 rounded-md object-cover" loading="lazy" />
            </button>
          <div data-lang-menu class="hidden absolute right-0 mt-3 w-44 floating-dropdown rounded-xl p-2 z-50">
            <a href="/locale/en" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#B62A2D]/15 hover:translate-x-1 transition-all duration-200 group">
              <img src="/flags/united-states.png" alt="English" class="w-6 h-6 rounded-lg object-cover ring-2 ring-transparent group-hover:ring-[#B62A2D]/50 transition-all shadow-sm" />
              <span class="text-sm text-gray-700 group-hover:text-[#B62A2D] font-medium transition-colors">{{ __('nav.english') }}</span>
              @if(app()->getLocale()==='en')
                <i data-lucide="check" class="w-4 h-4 text-[#B62A2D] ml-auto"></i>
              @endif
            </a>
            <a href="/locale/id" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#B62A2D]/15 hover:translate-x-1 transition-all duration-200 group">
              <img src="/flags/indonesia.png" alt="Bahasa" class="w-6 h-6 rounded-lg object-cover ring-2 ring-transparent group-hover:ring-[#B62A2D]/50 transition-all shadow-sm" />
              <span class="text-sm text-gray-700 group-hover:text-[#B62A2D] font-medium transition-colors">{{ __('nav.bahasa') }}</span>
              @if(app()->getLocale()==='id')
                <i data-lucide="check" class="w-4 h-4 text-[#B62A2D] ml-auto"></i>
              @endif
            </a>
          </div>
        </div>

          <!-- Theme Toggle Button (Desktop) -->
          <button type="button" id="themeToggleDesktop" class="relative z-10 inline-flex items-center justify-center w-9 h-9 rounded-xl overflow-hidden border border-white/20 hover:border-[#B62A2D] hover:bg-white/10 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-white/5 transition-all duration-300 cursor-pointer">
            <i data-lucide="moon" class="w-5 h-5 text-gray-300 dark-icon pointer-events-none"></i>
            <i data-lucide="sun" class="w-5 h-5 text-gray-300 light-icon hidden pointer-events-none"></i>
          </button>

          <!-- User Profile Icon -->
          <div class="relative ms-0 self-center" data-user>
          @auth
            <button type="button" data-user-toggle aria-haspopup="true" aria-expanded="false" class="inline-flex items-center justify-center w-9 h-9 rounded-xl overflow-hidden border border-[#B62A2D]/50 hover:border-[#B62A2D] hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-gradient-to-br from-[#B62A2D] to-[#D5575E] text-white font-semibold text-sm shadow-lg transition-all duration-300">
              {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </button>
            <div data-user-menu class="hidden absolute right-0 mt-3 w-52 floating-dropdown rounded-xl p-2 z-50">
              <div class="px-3 py-3 border-b border-gray-100 mb-2 bg-gradient-to-r from-[#B62A2D]/5 to-transparent rounded-lg">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                @if(Auth::user()->isAdmin())
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#B62A2D] text-white mt-1">Admin</span>
                @endif
              </div>
              @if(!Auth::user()->isAdmin())
                {{-- User Menu Items --}}
                <a href="{{ route('certificate.history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#B62A2D]/10 hover:translate-x-1 transition-all duration-200 w-full text-left group">
                  <div class="w-8 h-8 rounded-lg bg-[#B62A2D]/10 flex items-center justify-center group-hover:bg-[#B62A2D]/20 transition-colors">
                    <i data-lucide="history" class="w-4 h-4 text-[#B62A2D]"></i>
                  </div>
                  <span class="text-sm text-gray-700 group-hover:text-[#B62A2D] font-medium transition-colors">{{ __('nav.history') }}</span>
                </a>
              @endif
              <div class="border-t border-gray-100 my-2"></div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-red-50 hover:translate-x-1 transition-all duration-200 w-full text-left group">
                  <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i>
                  </div>
                  <span class="text-sm text-gray-700 group-hover:text-red-500 font-medium transition-colors">{{ __('auth.logout') }}</span>
                </button>
              </form>
            </div>
          @else
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl overflow-hidden border border-white/20 hover:border-[#B62A2D] hover:bg-white/10 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-white/5 transition-all duration-300">
              <i data-lucide="user" class="w-5 h-5 text-gray-300"></i>
            </a>
          @endauth
        </div>
      </nav>

      <!-- Mobile Menu Button -->
      <button id="mobileMenuButton" class="md:hidden text-white p-2 hover:bg-white/10 rounded-xl transition-all duration-300" aria-label="Toggle menu">
        <i data-lucide="menu" style="width:22px;height:22px"></i>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden mt-3 pt-3 border-t border-white/10 hidden">
      <nav class="flex flex-col gap-1">
        @auth
          @if(Auth::user()->isAdmin())
            {{-- Admin Mobile Navigation: No links, use dashboard cards instead --}}
          @else
            {{-- User Mobile Navigation --}}
            <a href="/#hero-section" data-nav-home class="relative z-10 px-4 py-3 text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.home') }}</a>
            <a href="/#about-section" data-nav-about class="relative z-10 px-4 py-3 text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.aboutUs') }}</a>
          @endif
        @else
          {{-- Guest Mobile Navigation --}}
          <a href="/#hero-section" data-nav-home class="relative z-10 px-4 py-3 text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.home') }}</a>
          <a href="/#about-section" data-nav-about class="relative z-10 px-4 py-3 text-gray-100 hover:text-white hover:bg-white/15 rounded-xl transition-all duration-300 cursor-pointer">{{ __('nav.aboutUs') }}</a>
        @endauth
        
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-white/10">
          <!-- Language Selector Mobile -->
          <div class="relative" data-lang>
            <button type="button" data-lang-toggle aria-haspopup="true" aria-expanded="false" class="inline-flex items-center justify-center w-10 h-10 rounded-xl overflow-hidden border border-white/20 hover:border-[#B62A2D] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-white/5 transition-all duration-300">
              <img src="{{ app()->getLocale()==='id' ? '/flags/indonesia.png' : '/flags/united-states.png' }}" alt="Current language" class="block w-5 h-5 rounded-md object-cover" loading="lazy" />
            </button>
            <div data-lang-menu class="hidden absolute left-0 mt-2 w-44 floating-dropdown rounded-xl p-2 z-50">
              <a href="/locale/en" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#B62A2D]/15 hover:translate-x-1 transition-all duration-200 group">
                <img src="/flags/united-states.png" alt="English" class="w-6 h-6 rounded-lg object-cover ring-2 ring-transparent group-hover:ring-[#B62A2D]/50 transition-all shadow-sm" />
                <span class="text-sm text-gray-700 group-hover:text-[#B62A2D] font-medium transition-colors">{{ __('nav.english') }}</span>
                @if(app()->getLocale()==='en')
                  <i data-lucide="check" class="w-4 h-4 text-[#B62A2D] ml-auto"></i>
                @endif
              </a>
              <a href="/locale/id" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#B62A2D]/15 hover:translate-x-1 transition-all duration-200 group">
                <img src="/flags/indonesia.png" alt="Bahasa" class="w-6 h-6 rounded-lg object-cover ring-2 ring-transparent group-hover:ring-[#B62A2D]/50 transition-all shadow-sm" />
                <span class="text-sm text-gray-700 group-hover:text-[#B62A2D] font-medium transition-colors">{{ __('nav.bahasa') }}</span>
                @if(app()->getLocale()==='id')
                  <i data-lucide="check" class="w-4 h-4 text-[#B62A2D] ml-auto"></i>
                @endif
              </a>
            </div>
          </div>

          <!-- Theme Toggle Mobile -->
          <button type="button" id="themeToggleMobile" class="relative z-10 inline-flex items-center justify-center w-10 h-10 rounded-xl overflow-hidden border border-white/20 hover:border-[#B62A2D] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-white/5 transition-all duration-300 cursor-pointer">
            <i data-lucide="moon" class="w-5 h-5 text-gray-300 dark-icon pointer-events-none"></i>
            <i data-lucide="sun" class="w-5 h-5 text-gray-300 light-icon hidden pointer-events-none"></i>
          </button>

          <!-- User Profile Mobile -->
          <div class="relative" data-user>
            @auth
              <button type="button" data-user-toggle aria-haspopup="true" aria-expanded="false" class="inline-flex items-center justify-center w-10 h-10 rounded-xl overflow-hidden border border-[#B62A2D]/50 hover:border-[#B62A2D] focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-gradient-to-br from-[#B62A2D] to-[#D5575E] text-white font-semibold text-sm transition-all duration-300">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
              </button>
              <div data-user-menu class="hidden absolute left-0 mt-2 w-48 floating-dropdown rounded-xl p-2 z-50">
                <div class="px-3 py-3 border-b border-gray-100 mb-2 bg-gradient-to-r from-[#B62A2D]/5 to-transparent rounded-lg">
                  <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                  @if(Auth::user()->isAdmin())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#B62A2D] text-white mt-1">Admin</span>
                  @endif
                </div>
                @if(!Auth::user()->isAdmin())
                  {{-- User Mobile Menu Items --}}
                  <a href="{{ route('certificate.history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#B62A2D]/10 hover:translate-x-1 transition-all duration-200 w-full text-left group">
                    <div class="w-8 h-8 rounded-lg bg-[#B62A2D]/10 flex items-center justify-center group-hover:bg-[#B62A2D]/20 transition-colors">
                      <i data-lucide="history" class="w-4 h-4 text-[#B62A2D]"></i>
                    </div>
                    <span class="text-sm text-gray-700 group-hover:text-[#B62A2D] font-medium transition-colors">{{ __('nav.history') }}</span>
                  </a>
                @endif
                <div class="border-t border-gray-100 my-2"></div>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-red-50 hover:translate-x-1 transition-all duration-200 w-full text-left group">
                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                      <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i>
                    </div>
                    <span class="text-sm text-gray-700 group-hover:text-red-500 font-medium transition-colors">{{ __('auth.logout') }}</span>
                  </button>
                </form>
              </div>
            @else
              <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl overflow-hidden border border-white/20 hover:border-[#B62A2D] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#B62A2D] bg-white/5 transition-all duration-300">
                <i data-lucide="user" class="w-5 h-5 text-gray-300"></i>
              </a>
            @endauth
          </div>
        </div>
      </nav>
    </div>
  </div>
  </header>
</div>
