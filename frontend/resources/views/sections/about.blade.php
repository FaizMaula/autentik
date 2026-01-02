<section id="about-section" class="relative py-20 pb-12">
  <div class="container mx-auto px-6 relative z-10">
    <div class="text-center mb-16">
      <h2 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] mb-4 section-title" data-section-group="about1">
        {{ __('about.title') }}
      </h2>
      <p class="text-xl text-gray-600 dark:text-gray-300 font-semibold section-subtitle" data-section-group="about1">{{ __('about.subtitle') }}</p>
      <div class="academic-line academic-line-animate w-32 mx-auto mt-6" data-section-group="about1"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20 max-w-6xl mx-auto">
      <div class="relative group scroll-animate delay-1" data-section-group="about1">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col items-center text-center text-white">
            <div class="mb-6 glass-icon p-4"><i data-lucide="zap" style="width:40px;height:40px"></i></div>
            <h3 class="text-xl font-bold mb-4">{{ __('about.card1Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('about.card1Desc') }}</p>
          </div>
        </div>
      </div>
      <div class="relative group scroll-animate delay-2" data-section-group="about1">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col items-center text-center text-white">
            <div class="mb-6 glass-icon p-4"><i data-lucide="lock" style="width:40px;height:40px"></i></div>
            <h3 class="text-xl font-bold mb-4">{{ __('about.card2Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('about.card2Desc') }}</p>
          </div>
        </div>
      </div>
      <div class="relative group scroll-animate delay-3" data-section-group="about1">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col items-center text-center text-white">
            <div class="mb-6 glass-icon p-4"><i data-lucide="database" style="width:40px;height:40px"></i></div>
            <h3 class="text-xl font-bold mb-4">{{ __('about.card3Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('about.card3Desc') }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- About Sub-section 2: Professional -->
    <div id="about2-section" class="pt-8">
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold text-[#222223] dark:text-[#FEFEFE] section-title" data-section-group="about2">
          {{ __('about.professionalTitle') }}
        </h2>
        <div class="academic-line academic-line-animate w-32 mx-auto mt-6" data-section-group="about2"></div>
      </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
      <div class="relative group scroll-animate delay-1" data-section-group="about2">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col text-white">
            <div class="mb-6 glass-icon p-4 w-fit"><i data-lucide="briefcase" style="width:40px;height:40px"></i></div>
            <h3 class="text-xl font-bold mb-4">{{ __('about.proCard1Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('about.proCard1Desc') }}</p>
          </div>
        </div>
      </div>
      <div class="relative group scroll-animate delay-2" data-section-group="about2">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col text-white">
            <div class="mb-6 glass-icon p-4 w-fit"><i data-lucide="graduation-cap" style="width:40px;height:40px"></i></div>
            <h3 class="text-xl font-bold mb-4">{{ __('about.proCard2Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('about.proCard2Desc') }}</p>
          </div>
        </div>
      </div>
      <div class="relative group scroll-animate delay-3" data-section-group="about2">
        <div class="glass-feature-card rounded-2xl p-8 h-full transform group-hover:scale-105 transition-all duration-300">
          <div class="flex flex-col text-white">
            <div class="mb-6 glass-icon p-4 w-fit"><i data-lucide="user" style="width:40px;height:40px"></i></div>
            <h3 class="text-xl font-bold mb-4">{{ __('about.proCard3Title') }}</h3>
            <p class="text-gray-100 leading-relaxed">{{ __('about.proCard3Desc') }}</p>
          </div>
        </div>
      </div>
    </div>
    </div><!-- Close about2-section -->
  </div>
</section>
