{{-- 
  Animated Background Component - Bold Smooth Aurora
  Usage: @include('components.animated-background')
  Props:
    - $showWatermark (default: false)
--}}

@props([
    'showWatermark' => false
])

<!-- Animated Background Container -->
<div class="animated-bg-container absolute inset-0 overflow-hidden pointer-events-none" data-animated-bg>
  
  <!-- Base Gradient Layer -->
  <div class="absolute inset-0 bg-gradient-to-br from-[#FEFEFE] via-[#f5f0ec] to-[#FEFEFE] dark:from-[#222223] dark:via-[#2a2a2b] dark:to-[#222223] transition-colors duration-500"></div>
  
  <!-- Aurora/Northern Lights Effect - Main Smooth Orbs -->
  <div class="absolute inset-0 aurora-container">
    <div class="aurora aurora-1"></div>
    <div class="aurora aurora-2"></div>
    <div class="aurora aurora-3"></div>
    <div class="aurora aurora-4"></div>
  </div>
  
  <!-- Parallax Smooth Orbs - Optimized (Reduced count) -->
  <div class="parallax-container absolute inset-0" data-parallax-container>
    <!-- Parallax Layer 1 (slowest) -->
    <div class="parallax-layer" data-parallax-speed="0.02">
      <div class="absolute top-[10%] right-[5%] w-[450px] h-[450px] rounded-full bg-gradient-radial from-[#B62A2D]/25 via-[#B62A2D]/10 to-transparent dark:from-[#B62A2D]/40 dark:via-[#B62A2D]/18 blur-[100px] animate-float-slow"></div>
    </div>
    
    <!-- Parallax Layer 2 (medium) -->
    <div class="parallax-layer" data-parallax-speed="0.04">
      <div class="absolute top-[25%] left-[10%] w-[350px] h-[350px] rounded-full bg-gradient-radial from-[#B62A2D]/22 via-[#E6A8A8]/10 to-transparent dark:from-[#B62A2D]/35 dark:via-[#E6A8A8]/15 blur-[80px] animate-float-slow" style="animation-delay: -2s;"></div>
    </div>
    
    <!-- Parallax Layer 3 (faster) -->
    <div class="parallax-layer" data-parallax-speed="0.06">
      <div class="absolute top-[40%] left-[30%] w-[250px] h-[250px] rounded-full bg-gradient-radial from-[#E6A8A8]/18 to-transparent dark:from-[#E6A8A8]/28 blur-[70px] animate-float" style="animation-delay: -1s;"></div>
      <div class="absolute top-[75%] left-[45%] w-[180px] h-[180px] rounded-full bg-gradient-radial from-[#B62A2D]/15 to-transparent dark:from-[#B62A2D]/25 blur-[60px] animate-float" style="animation-delay: -5s;"></div>
    </div>
  </div>
  
  <!-- Floating Particles - Reduced -->
  <div class="particles-container absolute inset-0">
    <div class="absolute top-[12%] left-[20%] w-3 h-3 bg-[#B62A2D]/35 dark:bg-[#B62A2D]/50 rounded-full blur-sm animate-float-particle"></div>
    <div class="absolute top-[45%] left-[15%] w-4 h-4 bg-[#B62A2D]/30 dark:bg-[#B62A2D]/45 rounded-full blur-sm animate-float-particle" style="animation-delay: -4s;"></div>
    <div class="absolute top-[78%] left-[40%] w-3 h-3 bg-[#E6A8A8]/35 dark:bg-[#E6A8A8]/50 rounded-full blur-sm animate-float-particle" style="animation-delay: -8s;"></div>
  </div>
  
  <!-- Mouse Following Gradient Glow -->
  <div class="mouse-glow-container absolute inset-0 pointer-events-none" data-mouse-glow>
    <div class="mouse-glow absolute w-[600px] h-[600px] rounded-full opacity-0 pointer-events-none blur-[80px]"
         style="background: radial-gradient(circle, rgba(182,42,45,0.25) 0%, rgba(182,42,45,0.1) 35%, transparent 65%);
                transform: translate(-50%, -50%);">
    </div>
  </div>
  
  <!-- Pattern Dots - Subtle -->
  <div class="absolute inset-0 pattern-dots opacity-30 dark:opacity-20"></div>
  
  <!-- Decorative Watermark (optional) -->
  @if($showWatermark)
  <div class="absolute inset-0 flex items-center justify-center opacity-[0.04] dark:opacity-[0.06] pointer-events-none">
    <img src="/assets/logo-autentik.png" 
         alt="Watermark" 
         class="w-[500px] h-[500px] object-contain invert dark:invert-0 animate-watermark-float" />
  </div>
  @endif
</div>
