<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Autentik' }}</title>
  
  <!-- Theme Detection Script (MUST be in head to prevent flash) -->
  <script>
    (function() {
      // Check localStorage first, then system preference
      var savedTheme = localStorage.getItem('theme');
      var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      
      if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>
  
  @vite(['resources/css/app.css','resources/js/app.js'])
  <!-- Alpine.js CDN -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Lucide icons CDN -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Lottie Player -->
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
  <style>body{font-family:var(--font-sans);}</style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><text x='0' y='14'>🔒</text></svg>">
  @stack('styles')
</head>
<body class="min-h-screen bg-[#FEFEFE] dark:bg-[#222223] text-[#222223] dark:text-[#FEFEFE] transition-colors duration-300">

  <!-- Loading Overlay with Sheriff Icon -->
  <div id="loadingOverlay" class="loading-overlay">
    <div class="loading-content">
      <img src="/assets/sheriff.png" alt="Loading" class="loading-sheriff" />
      <p class="loading-text" id="loadingText">{{ __('common.loading') }}</p>
    </div>
  </div>

  @include('partials.header')
  <main class="min-h-screen">
    @yield('content')
  </main>
  @if(!View::hasSection('hide_footer'))
    @include('partials.footer')
  @endif
  
  <!-- Initialize Lucide Icons -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      lucide.createIcons();
    });
  </script>
  
  <!-- Theme Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Theme toggle functionality
      function updateThemeUI(isDark) {
        // Update icons visibility
        document.querySelectorAll('.dark-icon').forEach(function(el) {
          el.classList.toggle('hidden', isDark);
        });
        document.querySelectorAll('.light-icon').forEach(function(el) {
          el.classList.toggle('hidden', !isDark);
        });
        // Update text visibility
        document.querySelectorAll('.dark-text').forEach(function(el) {
          el.classList.toggle('hidden', isDark);
        });
        document.querySelectorAll('.light-text').forEach(function(el) {
          el.classList.toggle('hidden', !isDark);
        });
        // Re-initialize Lucide icons
        lucide.createIcons();
      }
      
      function toggleTheme() {
        var isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateThemeUI(isDark);
      }
      
      // Initialize UI based on current theme
      var isDark = document.documentElement.classList.contains('dark');
      updateThemeUI(isDark);
      
      // Attach click handlers to all theme toggle buttons
      var toggleButtons = [
        'themeToggleDesktop',
        'themeToggleMobile'
      ];
      
      toggleButtons.forEach(function(id) {
        var btn = document.getElementById(id);
        if (btn) {
          btn.addEventListener('click', toggleTheme);
        }
      });
      
      // Listen for system preference changes
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        // Only auto-switch if user hasn't set a preference
        if (!localStorage.getItem('theme')) {
          if (e.matches) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
          updateThemeUI(e.matches);
        }
      });
    });
  </script>
  
  <!-- Global Loading Messages for JS -->
  <script>
    window.loadingMessages = {
      loading: '{{ __("common.loading") }}',
      verifying: '{{ __("common.verifying") }}',
      pleaseWait: '{{ __("common.pleaseWait") }}',
      switchingLanguage: '{{ __("common.switchingLanguage") }}',
      loggingOut: '{{ __("common.loggingOut") }}'
    };
  </script>
  
  <!-- Page Transition Script (with Sheriff Loading) -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var loadingOverlay = document.getElementById('loadingOverlay');
      var loadingText = document.getElementById('loadingText');
      
      // Show loading overlay
      function showLoading(text) {
        if (loadingText && text) {
          loadingText.textContent = text;
        }
        if (loadingOverlay) {
          loadingOverlay.classList.add('active');
        }
        document.body.classList.add('page-transitioning');
      }
      
      // Hide loading overlay
      function hideLoading() {
        if (loadingOverlay) {
          loadingOverlay.classList.remove('active');
        }
        document.body.classList.remove('page-transitioning');
      }
      
      // Intercept all internal link clicks for smooth page transition
      document.querySelectorAll('a[href]').forEach(function(link) {
        // Only handle internal links (same origin, not hash links, not new tab)
        var href = link.getAttribute('href');
        if (!href) return;
        
        // Skip external links, javascript:, mailto:, tel:, and target="_blank"
        if (href.startsWith('http') && !href.startsWith(window.location.origin)) return;
        if (href.startsWith('javascript:')) return;
        if (href.startsWith('mailto:') || href.startsWith('tel:')) return;
        if (link.getAttribute('target') === '_blank') return;
        if (link.hasAttribute('download')) return;
        // Skip links with data-no-loading attribute
        if (link.hasAttribute('data-no-loading')) return;
        
        // Check if it's a hash-only link (e.g., #section) - skip loading, just scroll
        if (href.startsWith('#')) {
          return; // Let default browser behavior handle in-page hash navigation
        }
        
        // Check if it's a link with hash to the same page (e.g., /#section when on /)
        var currentPath = window.location.pathname;
        var linkUrl = new URL(href, window.location.origin);
        var linkPath = linkUrl.pathname;
        var linkHash = linkUrl.hash;
        
        // If same page with hash, scroll to section instead of reloading
        if (linkPath === currentPath && linkHash) {
          link.addEventListener('click', function(e) {
            e.preventDefault();
            var targetSection = document.querySelector(linkHash);
            if (targetSection) {
              var headerOffset = 80;
              if (linkHash === '#about-section') headerOffset = 40;
              if (linkHash === '#hero-section') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
              }
              var elementPosition = targetSection.getBoundingClientRect().top;
              var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
              window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
            }
          });
          return;
        }
        
        link.addEventListener('click', function(e) {
          e.preventDefault();
          var targetHref = this.getAttribute('href');
          
          // Determine loading message based on link type
          var loadingMessage = '{{ __("common.loading") }}';
          
          // Check if it's a language switch link
          if (targetHref.includes('/locale/')) {
            loadingMessage = '{{ __("common.switchingLanguage") }}';
          }
          
          // Show loading overlay with appropriate message
          showLoading(loadingMessage);
          
          // Navigate after brief delay
          setTimeout(function() {
            window.location.href = targetHref;
          }, 300);
        });
      });
      
      // Handle form submissions with loading overlay
      document.querySelectorAll('form').forEach(function(form) {
        // Skip forms with data-no-loading attribute
        if (form.hasAttribute('data-no-loading')) return;
        // Skip certificate form - it has its own loading handler with validation
        if (form.id === 'certForm') return;
        
        form.addEventListener('submit', function(e) {
          // Check if it's a verification form
          var isVerifyForm = form.classList.contains('verify-form') || 
                             form.getAttribute('action')?.includes('verify') ||
                             form.id === 'verifyForm';
          
          // Check if it's a logout form
          var isLogoutForm = form.getAttribute('action')?.includes('logout');
          
          // Determine loading message based on form type
          var loadingMessage = '{{ __("common.loading") }}';
          if (isLogoutForm) {
            loadingMessage = '{{ __("common.loggingOut") }}';
          } else if (isVerifyForm) {
            loadingMessage = '{{ __("common.verifying") }}';
          }
          
          // Use setTimeout to let other validators run first (e.g., custom validation in form.blade.php)
          // Only show loading if form submission was not prevented by other handlers
          var event = e;
          setTimeout(function() {
            if (!event.defaultPrevented) {
              showLoading(loadingMessage);
            }
          }, 0);
        });
      });
      
      // Hide loading on page show (for back/forward navigation)
      window.addEventListener('pageshow', function(e) {
        // Always hide loading when page is shown (handles bfcache and normal loads)
        hideLoading();
      });
      
      // Also hide loading immediately on DOMContentLoaded to catch any stuck states
      hideLoading();
    });
  </script>
</body>
</html>
