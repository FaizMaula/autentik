@extends('layouts.app')

{{-- Hide scrollbar on Landing Page only --}}
@push('styles')
<style>
  html, body {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE/Edge */
  }
  html::-webkit-scrollbar, body::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
  }
</style>
@endpush

@section('content')

{{-- Splash Screen Logic: Only show for --}}
{{-- 1. Auth success (login/register) --}}
{{-- 2. Guest first visit (checked via sessionStorage in JS) --}}
@php
  $hasAuthSuccess = session('auth_success');
@endphp

<!-- Splash Screen Overlay (Only on Landing Page) -->
<div id="splashScreen" class="fixed inset-0 z-[9999] flex items-center justify-center transition-opacity duration-500 backdrop-blur-md bg-[#FEFEFE]/80 dark:bg-[#222223]/80" style="display: none;">
  <div class="flex flex-col items-center justify-center w-full h-full">
    <lottie-player
      id="splashLottie"
      class="invert dark:invert-0"
      src="{{ asset('assets/animated-logo-fix.json') }}"
      background="transparent"
      speed="1"
      style="width: 80vw; height: 80vh; max-width: 600px; max-height: 600px;"
      autoplay
    ></lottie-player>
    @if($hasAuthSuccess)
    <p class="absolute bottom-20 left-1/2 -translate-x-1/2 text-2xl font-semibold text-[#222223] dark:text-[#FEFEFE] animate-pulse">
      @if(session('auth_success') === 'login')
        {{ __('auth.welcomeBack') ?? 'Selamat Datang Kembali!' }}
      @else
        {{ __('auth.registerSuccess') ?? 'Registrasi Berhasil!' }}
      @endif
    </p>
    @endif
  </div>
</div>

<!-- Unified Background Wrapper for Seamless Sections (EXTENDED to include footer) -->
<div class="relative overflow-clip min-h-screen pb-0">
  <!-- Enhanced Animated Background Component -->
  @include('components.animated-background', ['showWatermark' => true])

  <!-- Sections with Transparent Backgrounds -->
  @include('sections.hero')
  @include('sections.process')
  @include('sections.about')
  
  <!-- Footer Section - Seamlessly integrated with landing page background -->
  @include('partials.footer')
</div>

<!-- Scroll Navigation Orbs (Fixed Right Side) -->
<nav id="scrollNavOrbs" class="fixed right-6 top-1/2 -translate-y-1/2 z-50 flex flex-col gap-4 opacity-0 transition-opacity duration-500">
  <!-- Hero Orb -->
  <button type="button" class="scroll-nav-orb group relative" data-target="hero-section" aria-label="{{ __('nav.home') }}">
    <span class="scroll-nav-dot"></span>
    <span class="scroll-nav-tooltip">{{ __('nav.home') }}</span>
  </button>
  
  <!-- Process Orb -->
  <button type="button" class="scroll-nav-orb group relative" data-target="process-section" aria-label="{{ __('nav.howItWorks') }}">
    <span class="scroll-nav-dot"></span>
    <span class="scroll-nav-tooltip">{{ __('nav.howItWorks') }}</span>
  </button>
  
  <!-- About 1 Orb -->
  <button type="button" class="scroll-nav-orb group relative" data-target="about-section" aria-label="{{ __('nav.whyUs') }}">
    <span class="scroll-nav-dot"></span>
    <span class="scroll-nav-tooltip">{{ __('nav.whyUs') }}</span>
  </button>
  
  <!-- About 2 Orb -->
  <button type="button" class="scroll-nav-orb group relative" data-target="about2-section" aria-label="{{ __('nav.forProfessional') }}">
    <span class="scroll-nav-dot"></span>
    <span class="scroll-nav-tooltip">{{ __('nav.forProfessional') }}</span>
  </button>
</nav>

<!-- Landing Page Animations Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ========================================
  // HASH-BASED NAVIGATION (from other pages)
  // Scroll to section based on URL hash after page load
  // ========================================
  function scrollToHashSection() {
    var hash = window.location.hash;
    if (!hash) return;
    
    var targetId = hash.replace('#', '');
    var targetSection = document.getElementById(targetId);
    
    if (targetSection) {
      // Wait for page to fully render, then scroll
      setTimeout(function() {
        var headerOffset = 80;
        
        // Special offsets per section
        if (targetId === 'about-section') {
          headerOffset = 40;
        } else if (targetId === 'about2-section') {
          // Scroll to bottom
          window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
          return;
        } else if (targetId === 'hero-section') {
          // Scroll to top
          window.scrollTo({ top: 0, behavior: 'smooth' });
          return;
        }
        
        var elementPosition = targetSection.getBoundingClientRect().top;
        var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
        
        window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
      }, 100);
    }
  }
  
  // ========================================
  // SPLASH SCREEN LOGIC
  // Only show for: 1) Auth success, 2) Guest first visit
  // ========================================
  var hasAuthSuccess = {{ $hasAuthSuccess ? 'true' : 'false' }};
  var isGuestFirstVisit = !sessionStorage.getItem('landingVisited');
  // Don't show splash if coming from hash navigation (navigating from another page)
  var hasHashNavigation = window.location.hash && window.location.hash.length > 1;
  var showSplash = (hasAuthSuccess || isGuestFirstVisit) && !hasHashNavigation;
  
  var splash = document.getElementById('splashScreen');
  
  if (showSplash && splash) {
    // Mark as visited for guests (so splash won't show again in this session)
    if (!hasAuthSuccess) {
      sessionStorage.setItem('landingVisited', 'true');
    }
    
    // Show splash and add splash-active class
    splash.style.display = 'flex';
    document.body.classList.add('splash-active');
    
    // Fade out after animation (2.6s)
    setTimeout(function() {
      splash.style.opacity = '0';
      setTimeout(function() {
        splash.style.display = 'none';
        document.body.classList.remove('splash-active');
        // After splash, scroll to hash section if exists
        scrollToHashSection();
      }, 500);
    }, 2600);
  } else {
    // No splash - scroll to hash section immediately
    scrollToHashSection();
  }
  
  // ========================================
  // HERO SECTION SEQUENTIAL ANIMATION
  // Delay depends on whether splash is shown
  // ========================================
  function initHeroAnimations() {
    var heroTitle = document.getElementById('hero-title');
    var heroSubtitle = document.getElementById('hero-subtitle');
    var heroBtn1 = document.getElementById('hero-btn-1');
    var heroBtn2 = document.getElementById('hero-btn-2');
    
    // Sequential animation: title -> subtitle -> buttons
    if (heroTitle) heroTitle.classList.add('animate-in');
    
    setTimeout(function() {
      if (heroSubtitle) heroSubtitle.classList.add('animate-in');
      
      setTimeout(function() {
        if (heroBtn1) heroBtn1.classList.add('animate-in');
        
        setTimeout(function() {
          if (heroBtn2) heroBtn2.classList.add('animate-in');
        }, 200);
      }, 300);
    }, 400);
  }

  // Calculate delay based on splash visibility
  var heroDelay;
  if (showSplash) {
    // Splash shown: wait for splash (2.6s) + fade (0.5s) + header (0.6s) + buffer
    heroDelay = 2600 + 500 + 600 + 200; // = 3900ms
  } else {
    // No splash: just wait for header animation + small delay
    heroDelay = 800; // Normal delay
  }
  
  setTimeout(function() {
    initHeroAnimations();
  }, heroDelay);

  // ========================================
  // REPEATING SCROLL ANIMATIONS (PROCESS & ABOUT)
  // With delay threshold to prevent flickering
  // ========================================
  
  // Store timeout IDs to prevent animation conflicts
  const animationTimeouts = new Map();
  const FADE_OUT_DELAY = 150; // ms delay before fade out (prevents flickering)
  
  // Helper: Animate group elements IN
  function animateGroupIn(group) {
    // Clear any pending fade-out
    if (animationTimeouts.has(group + '-out')) {
      clearTimeout(animationTimeouts.get(group + '-out'));
      animationTimeouts.delete(group + '-out');
    }
    
    const title = document.querySelector('.section-title[data-section-group="' + group + '"]');
    const subtitle = document.querySelector('.section-subtitle[data-section-group="' + group + '"]');
    const academicLine = document.querySelector('.academic-line-animate[data-section-group="' + group + '"]');
    const cards = document.querySelectorAll('.scroll-animate[data-section-group="' + group + '"]');
    
    // Animate title
    if (title) title.classList.add('animate-in');
    
    // Animate subtitle after title
    if (subtitle) {
      setTimeout(function() {
        subtitle.classList.add('animate-in');
      }, 200);
    }
    
    // Animate academic line
    if (academicLine) {
      setTimeout(function() {
        academicLine.classList.add('animate-in');
      }, subtitle ? 400 : 300);
    }
    
    // Animate cards with stagger
    cards.forEach(function(card, index) {
      setTimeout(function() {
        card.classList.add('animate-in');
      }, (subtitle ? 500 : 400) + (index * 100));
    });
  }
  
  // Helper: Animate group elements OUT
  function animateGroupOut(group) {
    // Add delay before fade out to prevent flickering
    const timeoutId = setTimeout(function() {
      const title = document.querySelector('.section-title[data-section-group="' + group + '"]');
      const subtitle = document.querySelector('.section-subtitle[data-section-group="' + group + '"]');
      const academicLine = document.querySelector('.academic-line-animate[data-section-group="' + group + '"]');
      const cards = document.querySelectorAll('.scroll-animate[data-section-group="' + group + '"]');
      
      // Fade out all elements
      if (title) title.classList.remove('animate-in');
      if (subtitle) subtitle.classList.remove('animate-in');
      if (academicLine) academicLine.classList.remove('animate-in');
      cards.forEach(function(card) {
        card.classList.remove('animate-in');
      });
      
      animationTimeouts.delete(group + '-out');
    }, FADE_OUT_DELAY);
    
    animationTimeouts.set(group + '-out', timeoutId);
  }
  
  // Track which groups are currently visible
  const visibleGroups = new Set();
  
  // Observer for section containers (Process & About)
  const sectionObserver = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      const group = entry.target.getAttribute('data-section-group');
      if (!group) return;
      
      if (entry.isIntersecting) {
        // Section entering viewport
        if (!visibleGroups.has(group)) {
          visibleGroups.add(group);
          animateGroupIn(group);
        }
      } else {
        // Section leaving viewport
        if (visibleGroups.has(group)) {
          visibleGroups.delete(group);
          animateGroupOut(group);
        }
      }
    });
  }, {
    root: null,
    rootMargin: '-10% 0px -10% 0px', // Trigger slightly inside viewport
    threshold: 0.1
  });
  
  // Observe section containers (titles act as triggers)
  document.querySelectorAll('.section-title[data-section-group]').forEach(function(el) {
    sectionObserver.observe(el);
  });

  // ========================================
  // SCROLL NAVIGATION ORBS
  // Click to scroll, active state on scroll
  // ========================================
  
  var scrollNavOrbs = document.getElementById('scrollNavOrbs');
  var scrollNavButtons = document.querySelectorAll('.scroll-nav-orb');
  var sections = ['hero-section', 'process-section', 'about-section', 'about2-section'];
  
  // Show orbs after splash/initial load
  setTimeout(function() {
    if (scrollNavOrbs) {
      scrollNavOrbs.style.opacity = '1';
    }
  }, showSplash ? 3200 : 1000);
  
  // Click handler - smooth scroll to section
  scrollNavButtons.forEach(function(btn) {
    btn.addEventListener('click', function() {
      var targetId = this.getAttribute('data-target');
      var targetSection = document.getElementById(targetId);
      
      if (targetSection) {
        // Special case: about2-section scrolls to bottom of page
        if (targetId === 'about2-section') {
          window.scrollTo({
            top: document.documentElement.scrollHeight,
            behavior: 'smooth'
          });
          return;
        }
        
        // Custom offsets per section for better visual alignment
        var headerOffset = 80; // Default offset for header
        
        // About section needs less offset to show title better centered
        if (targetId === 'about-section') {
          headerOffset = 40; // Less offset = scrolls more down
        }
        
        var elementPosition = targetSection.getBoundingClientRect().top;
        var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
        
        window.scrollTo({
          top: offsetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
  
  // Scroll spy - update active orb based on which section is most visible
  function updateActiveOrb() {
    var viewportHeight = window.innerHeight;
    var currentSection = null;
    var maxVisibleArea = 0;
    
    // Find which section has the most visible area in viewport
    for (var i = 0; i < sections.length; i++) {
      var section = document.getElementById(sections[i]);
      if (!section) continue;
      
      var rect = section.getBoundingClientRect();
      
      // Calculate visible area of this section
      var visibleTop = Math.max(0, rect.top);
      var visibleBottom = Math.min(viewportHeight, rect.bottom);
      var visibleHeight = Math.max(0, visibleBottom - visibleTop);
      
      // Weight towards sections that are more centered in viewport
      var sectionCenter = rect.top + rect.height / 2;
      var viewportCenter = viewportHeight / 2;
      var centerDistance = Math.abs(sectionCenter - viewportCenter);
      var centerWeight = 1 - (centerDistance / viewportHeight);
      
      var score = visibleHeight * centerWeight;
      
      if (score > maxVisibleArea) {
        maxVisibleArea = score;
        currentSection = sections[i];
      }
    }
    
    // Update active states
    scrollNavButtons.forEach(function(btn) {
      var targetId = btn.getAttribute('data-target');
      if (targetId === currentSection) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });
  }
  
  // Initial check
  updateActiveOrb();
  
  // Update on scroll (throttled)
  var scrollTicking = false;
  window.addEventListener('scroll', function() {
    if (!scrollTicking) {
      requestAnimationFrame(function() {
        updateActiveOrb();
        scrollTicking = false;
      });
      scrollTicking = true;
    }
  }, { passive: true });
});
</script>

{{-- Hide the default footer from layout since it's included above --}}
@section('hide_footer', true)
@endsection
