<section class="relative min-h-screen flex items-center pt-20 bg-gradient-to-br from-[#C5D3D8] via-[#B8C8CE] to-[#A8B8BE]">
  <!-- Decorative watermark -->
  <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
    <img src="https://customer-assets.emergentagent.com/job_cd2810c3-bd90-4983-af99-b7abbf45f0c5/artifacts/sa2vlsjk_Logo%20ONLY.png" alt="Watermark" class="w-[600px] h-[600px] object-contain" />
  </div>

  <div class="container mx-auto px-6 sm:pl-8 md:pl-16 lg:pl-24 relative z-10">
    <div class="max-w-4xl">
      <div class="mb-8 animate-fade-in">
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-[#0F0F10] mb-6 leading-tight">{{ __('hero.title') }}</h1>
        <p class="text-lg md:text-xl text-gray-700 mb-8 leading-relaxed max-w-3xl">{{ __('hero.subtitle') }}</p>
      </div>

      <div class="flex flex-col sm:flex-row gap-4 animate-slide-up">
        <a href="/form" class="group px-8 py-4 bg-[#000033] text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-[#000055] transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
          {{ __('hero.ctaPrimary') }}
          <i data-lucide="arrow-right" class="transition-transform group-hover:translate-x-1" style="width:20px;height:20px"></i>
        </a>
        <a href="/#process-section" data-scroll-process class="px-8 py-4 bg-transparent border-2 border-[#4A7C87] text-[#0F0F10] rounded-lg font-semibold text-lg hover:bg-[#4A7C87] hover:text-white transform hover:scale-105 transition-all duration-300">
          {{ __('hero.ctaSecondary') }}
        </a>
      </div>
    </div>
  </div>

  <!-- Animated floating element (hide on small screens) -->
  <div class="hidden md:block absolute bottom-10 right-10 opacity-20 animate-float">
    <i data-lucide="shield" class="text-[#4A7C87]" style="width:120px;height:120px"></i>
  </div>
</section>
