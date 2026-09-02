@php
    $status = auth()->user()?->getDownloadLimitStatus();
@endphp

@auth
    @if($status && isset($status['limit']))
        <div class="download-limit-indicator p-3 rounded-xl mb-3" 
             style="background: {{ $status['color'] === 'red' ? 'rgba(239, 68, 68, 0.08)' : ($status['color'] === 'orange' ? 'rgba(251, 146, 60, 0.08)' : 'rgba(16, 185, 129, 0.08)') }};">
            
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="ti ti-download text-{{ $status['color'] }}-500"></i>
                    <span class="text-sm font-medium text-gray-700">Downloads Today</span>
                </div>
                <span class="text-sm font-bold text-{{ $status['color'] }}-600">
                    {{ $status['used'] }} / {{ $status['limit'] }}
                </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="h-2 rounded-full transition-all duration-500 
                    {{ $status['color'] === 'red' ? 'bg-red-500' : ($status['color'] === 'orange' ? 'bg-orange-500' : 'bg-green-500') }}"
                    style="width: {{ $status['progress'] }}%">
                </div>
            </div>
            
            <p class="text-xs mt-2 text-{{ $status['color'] }}-600">
                {{ $status['message'] }}
            </p>
            
            @if($status['status'] === 'warning')
                <div class="flex items-center gap-1 mt-1 text-xs text-orange-600">
                    <i class="ti ti-alert-triangle"></i>
                    <span>You're almost out of downloads!</span>
                </div>
            @endif
            
            @if($status['status'] === 'exhausted')
                <div class="flex items-center gap-1 mt-1 text-xs text-red-600">
                    <i class="ti ti-circle-x"></i>
                    <span>Limit reached. Try again tomorrow at midnight.</span>
                </div>
            @endif
        </div>
    @endif
@endauth