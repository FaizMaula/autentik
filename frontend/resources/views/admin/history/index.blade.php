@extends('layouts.app')

@section('content')
<section class="relative min-h-screen flex flex-col pt-20 pb-0 overflow-hidden">
  @include('components.animated-background', ['showWatermark' => true])

  <div class="flex-grow py-8">
    <div class="container mx-auto px-6 relative z-10">
      <!-- Back to Dashboard -->
      <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-[#B62A2D] transition-colors mb-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('admin.backToDashboard') }}
      </a>

      <!-- Header with Search & Filter -->
      <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4 mb-8">
        <div>
          <h1 class="text-3xl font-bold text-[#222223] dark:text-[#FEFEFE]">{{ __('admin.allHistoryTitle') }}</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('admin.allHistorySubtitle') }}</p>
        </div>
        
        <!-- Search & Filter Controls -->
        <div class="flex flex-col sm:flex-row gap-3">
          <!-- Search Box -->
          <div class="relative">
            <input 
              type="text" 
              id="searchInput" 
              placeholder="{{ __('admin.searchPlaceholder') }}"
              class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-lg glass-input text-sm focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all"
            />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          
          <!-- Status Filter Dropdown -->
          <select 
            id="statusFilter" 
            class="px-4 py-2 rounded-lg glass-input text-sm focus:ring-2 focus:ring-[#B62A2D] focus:border-transparent transition-all cursor-pointer"
          >
            <option value="">{{ __('admin.allStatus') }}</option>
            <option value="verified">{{ __('results.verified') }}</option>
            <option value="suspicious">{{ __('results.suspicious') }}</option>
            <option value="not_verified">{{ __('results.notVerified') }}</option>
          </select>
        </div>
      </div>

      <!-- No Results Message (Hidden by default) -->
      <div id="noResultsMessage" class="hidden mb-6 p-4 bg-gray-100 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 rounded-lg text-center">
        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ __('admin.noSearchResults') }}
      </div>

      <!-- History Table -->
      <div class="glass-card-strong rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full table-auto">
            <thead class="bg-gray-100 dark:bg-[#333334]">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.user') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('form.name') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('form.eventName') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('form.organizer') }}</th>
                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('results.status') }}</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.verifiedAt') }}</th>
                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('admin.actions') }}</th>
              </tr>
            </thead>
            <tbody id="historyTableBody" class="divide-y divide-gray-200 dark:divide-[#3D3D3E]">
              @forelse($certificates as $index => $certificate)
                @php
                  $status = $certificate->overall_status ?? 'pending';
                  $statusKey = ($status === 'verified' || $status === 'valid') ? 'verified' : ($status === 'suspicious' ? 'suspicious' : 'not_verified');
                @endphp
                <tr class="history-row hover:bg-gray-50 dark:hover:bg-[#333334]/50 transition-colors"
                    data-user="{{ strtolower($certificate->user->name ?? '') }}"
                    data-email="{{ strtolower($certificate->user->email ?? '') }}"
                    data-nama="{{ strtolower($certificate->nama ?? '') }}"
                    data-kegiatan="{{ strtolower($certificate->nama_kegiatan ?? '') }}"
                    data-penyelenggara="{{ strtolower($certificate->penyelenggara ?? '') }}"
                    data-status="{{ $statusKey }}">
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400 row-number">
                    {{ ($certificates->currentPage() - 1) * $certificates->perPage() + $index + 1 }}
                  </td>
                  <td class="px-6 py-4">
                    <div class="font-medium text-[#222223] dark:text-[#FEFEFE]">{{ $certificate->user->name ?? '-' }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $certificate->user->email ?? '-' }}</div>
                  </td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $certificate->nama ?? '-' }}</td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $certificate->nama_kegiatan ?? '-' }}</td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $certificate->penyelenggara ?? '-' }}</td>
                  <td class="px-6 py-4 text-center">
                    @php
                      $status = $certificate->overall_status ?? 'pending';
                    @endphp
                    @if($status === 'verified' || $status === 'valid')
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                        {{ __('results.verified') }}
                      </span>
                    @elseif($status === 'suspicious')
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                        {{ __('results.suspicious') }}
                      </span>
                    @else
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                        {{ __('results.notVerified') }}
                      </span>
                    @endif
                  </td>
                  <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                    {{ $certificate->created_at->format('d M Y, H:i') }}
                  </td>
                  <td class="px-6 py-4 text-center">
                    <a href="{{ route('admin.result.show', $certificate->id) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors text-sm font-medium">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      {{ __('admin.viewDetails') }}
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center gap-3">
                      <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <p>{{ __('admin.noHistory') }}</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        @if($certificates->hasPages())
          <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $certificates->links('pagination::simple-tailwind') }}
          </div>
        @endif
      </div>
    </div>
  </div>
  
  @include('partials.footer')
</section>

@section('hide_footer', true)

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const tableBody = document.getElementById('historyTableBody');
  const noResultsMessage = document.getElementById('noResultsMessage');
  const rows = tableBody.querySelectorAll('.history-row');
  
  let debounceTimer;
  
  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const statusValue = statusFilter.value;
    let visibleCount = 0;
    let rowNumber = 1;
    
    rows.forEach(row => {
      const user = row.dataset.user || '';
      const email = row.dataset.email || '';
      const nama = row.dataset.nama || '';
      const kegiatan = row.dataset.kegiatan || '';
      const penyelenggara = row.dataset.penyelenggara || '';
      const status = row.dataset.status || '';
      
      // Check search match (search across multiple fields)
      const searchMatch = !searchTerm || 
        user.includes(searchTerm) || 
        email.includes(searchTerm) || 
        nama.includes(searchTerm) || 
        kegiatan.includes(searchTerm) || 
        penyelenggara.includes(searchTerm);
      
      // Check status match
      const statusMatch = !statusValue || status === statusValue;
      
      // Show/hide row
      if (searchMatch && statusMatch) {
        row.style.display = '';
        // Update row number
        const rowNumberCell = row.querySelector('.row-number');
        if (rowNumberCell) {
          rowNumberCell.textContent = rowNumber;
        }
        rowNumber++;
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });
    
    // Show/hide no results message
    if (visibleCount === 0 && (searchTerm || statusValue)) {
      noResultsMessage.classList.remove('hidden');
    } else {
      noResultsMessage.classList.add('hidden');
    }
  }
  
  // Debounced search for better performance
  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(filterTable, 150);
  });
  
  // Immediate filter on status change
  statusFilter.addEventListener('change', filterTable);
});
</script>
@endsection
