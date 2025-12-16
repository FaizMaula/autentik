<section id="about-section" class="py-20 bg-gradient-to-br from-[#C5D3D8] via-[#B8C8CE] to-[#A8B8BE]">
  <div class="container mx-auto px-6">
    <div class="text-center mb-16">
      <h2 class="text-4xl md:text-5xl font-bold text-[#0F0F10] mb-4">{{ __('about.title') }}</h2>
      <p class="text-xl text-gray-700 font-semibold">{{ __('about.subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20 max-w-6xl mx-auto">
      <div class="bg-[#4A7C87] rounded-2xl p-8 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl">
        <div class="flex flex-col items-center text-center text-white">
          <div class="mb-6"><i data-lucide="zap" style="width:40px;height:40px"></i></div>
          <h3 class="text-xl font-bold mb-4">{{ __('about.card1Title') }}</h3>
          <p class="text-gray-100 leading-relaxed">{{ __('about.card1Desc') }}</p>
        </div>
      </div>
      <div class="bg-[#4A7C87] rounded-2xl p-8 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl">
        <div class="flex flex-col items-center text-center text-white">
          <div class="mb-6"><i data-lucide="lock" style="width:40px;height:40px"></i></div>
          <h3 class="text-xl font-bold mb-4">{{ __('about.card2Title') }}</h3>
          <p class="text-gray-100 leading-relaxed">{{ __('about.card2Desc') }}</p>
        </div>
      </div>
      <div class="bg-[#4A7C87] rounded-2xl p-8 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl">
        <div class="flex flex-col items-center text-center text-white">
          <div class="mb-6"><i data-lucide="database" style="width:40px;height:40px"></i></div>
          <h3 class="text-xl font-bold mb-4">{{ __('about.card3Title') }}</h3>
          <p class="text-gray-100 leading-relaxed">{{ __('about.card3Desc') }}</p>
        </div>
      </div>
    </div>

    <div class="text-center mb-16">
      <h2 class="text-4xl md:text-5xl font-bold text-[#0F0F10]">{{ __('about.professionalTitle') }}</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
      <div class="bg-[#4A7C87] rounded-2xl p-8 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl">
        <div class="flex flex-col text-white">
          <div class="mb-6"><i data-lucide="briefcase" style="width:40px;height:40px"></i></div>
          <h3 class="text-xl font-bold mb-4">{{ __('about.proCard1Title') }}</h3>
          <p class="text-gray-100 leading-relaxed">{{ __('about.proCard1Desc') }}</p>
        </div>
      </div>
      <div class="bg-[#4A7C87] rounded-2xl p-8 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl">
        <div class="flex flex-col text-white">
          <div class="mb-6"><i data-lucide="graduation-cap" style="width:40px;height:40px"></i></div>
          <h3 class="text-xl font-bold mb-4">{{ __('about.proCard2Title') }}</h3>
          <p class="text-gray-100 leading-relaxed">{{ __('about.proCard2Desc') }}</p>
        </div>
      </div>
      <div class="bg-[#4A7C87] rounded-2xl p-8 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl">
        <div class="flex flex-col text-white">
          <div class="mb-6"><i data-lucide="user" style="width:40px;height:40px"></i></div>
          <h3 class="text-xl font-bold mb-4">{{ __('about.proCard3Title') }}</h3>
          <p class="text-gray-100 leading-relaxed">{{ __('about.proCard3Desc') }}</p>
        </div>
      </div>
    </div>
  </div>
</section>
