<section id="hero-section" class="relative min-h-screen flex items-center pt-20">
  <div class="container mx-auto px-6 sm:pl-8 md:pl-16 lg:pl-24 relative z-10">
    <div class="max-w-4xl">
      <div class="mb-8">
        <h1 id="hero-title" class="text-5xl md:text-6xl lg:text-7xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-6 leading-tight hero-element">
          {{ __('hero.title') }}
        </h1>
        <p id="hero-subtitle" class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed max-w-3xl hero-element">
          {{ __('hero.subtitle') }}
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-4">
        @auth
        <a id="hero-btn-1" href="/form" class="hero-btn group px-8 py-4 bg-[#222223] dark:bg-[#B62A2D] text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-[#333334] dark:hover:bg-[#9a2426] transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl relative overflow-hidden">
          <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
          {{ __('hero.ctaPrimary') }}
          <i data-lucide="arrow-right" class="transition-transform group-hover:translate-x-1" style="width:20px;height:20px"></i>
        </a>
        @else
        <a id="hero-btn-1" href="{{ route('login') }}" class="hero-btn group px-8 py-4 bg-[#222223] dark:bg-[#B62A2D] text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-[#333334] dark:hover:bg-[#9a2426] transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl relative overflow-hidden">
          <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
          {{ __('hero.ctaPrimary') }}
          <i data-lucide="arrow-right" class="transition-transform group-hover:translate-x-1" style="width:20px;height:20px"></i>
        </a>
        @endauth
        <a id="hero-btn-2" href="/#process-section" data-scroll-process class="hero-btn px-8 py-4 bg-white/60 dark:bg-[#222223]/60 backdrop-blur-sm hover:bg-white/80 dark:hover:bg-[#222223]/80 border-2 border-[#222223]/20 dark:border-[#FEFEFE]/20 hover:border-[#222223] dark:hover:border-[#FEFEFE] text-[#222223] dark:text-[#FEFEFE] rounded-lg font-semibold text-lg transform hover:scale-105 transition-all duration-300">
          {{ __('hero.ctaSecondary') }}
        </a>
      </div>
    </div>
  </div>

  <!-- Animated floating element (hide on small screens) -->
  <div class="hidden md:block absolute bottom-20 right-10 animate-float">
    <div class="relative">
      <div class="absolute inset-0 bg-[#B62A2D]/30 rounded-full blur-2xl scale-150"></div>
      <div class="glass-icon p-6 relative z-10">
        <i data-lucide="shield" class="text-[#B62A2D]" style="width:80px;height:80px"></i>
      </div>
    </div>
  </div>
</section>
