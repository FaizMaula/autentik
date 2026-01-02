<section id="process-section" class="relative py-20">
  <div class="container mx-auto px-6 relative z-10">
    <div class="text-center mb-16">
      <h2 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] section-title" data-section-group="process">
        {{ __('process.title') }}
      </h2>
      <div class="academic-line academic-line-animate w-32 mx-auto mt-6" data-section-group="process"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
      <!-- Step 1 -->
      <div class="relative group scroll-animate delay-1" data-section-group="process">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col items-center text-center text-white">
            <div class="mb-6 glass-icon p-4">
              <i data-lucide="upload" style="width:48px;height:48px"></i>
            </div>
            <h3 class="text-2xl font-bold mb-4">{{ __('process.step1Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('process.step1Desc') }}</p>
          </div>
        </div>
        <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-[#4A7C87] z-10">
          <!--<svg width="30" height="30" viewBox="0 0 30 30" fill="currentColor"><path d="M15 3 L27 15 L15 27 L15 18 L3 18 L3 12 L15 12 Z" /></svg>-->
        </div>
      </div>

      <!-- Step 2 -->
      <div class="relative group scroll-animate delay-2" data-section-group="process">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col items-center text-center text-white">
            <div class="mb-6 glass-icon p-4">
              <i data-lucide="search" style="width:48px;height:48px"></i>
            </div>
            <h3 class="text-2xl font-bold mb-4">{{ __('process.step2Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('process.step2Desc') }}</p>
          </div>
        </div>
        <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2 text-[#4A7C87] z-10">
          <!--<svg width="30" height="30" viewBox="0 0 30 30" fill="currentColor"><path d="M15 3 L27 15 L15 27 L15 18 L3 18 L3 12 L15 12 Z" /></svg>-->
        </div>
      </div>

      <!-- Step 3 -->
      <div class="relative group scroll-animate delay-3" data-section-group="process">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col items-center text-center text-white">
            <div class="mb-6 glass-icon p-4">
              <i data-lucide="check-circle" style="width:48px;height:48px"></i>
            </div>
            <h3 class="text-2xl font-bold mb-4">{{ __('process.step3Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('process.step3Desc') }}</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
