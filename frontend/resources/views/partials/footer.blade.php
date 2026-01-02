
<!-- Footer Section - Clean minimal footer, seamless with landing page background -->
<footer id="footer-section" class="relative pt-8 pb-2">
  <div class="flex flex-col items-center justify-center gap-2">
    <!-- Logo and Brand -->
    <div class="flex items-center gap-2.5 group">
      <div class="w-8 h-8 rounded-lg bg-[#222223] dark:bg-[#B62A2D] flex items-center justify-center shadow-sm group-hover:bg-[#333334] dark:group-hover:bg-[#9a2426] transition-all duration-300">
        <img src="{{ asset('assets/logo-autentik.png') }}" alt="Autentik Logo" class="h-5 w-5 object-contain" />
      </div>
      <span class="text-[#222223] dark:text-[#FEFEFE] text-sm font-semibold tracking-wide">AUTENTIK</span>
    </div>
    <!-- Copyright -->
    <p class="text-gray-600 dark:text-gray-400 text-xs">{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
  </div>
</footer>
