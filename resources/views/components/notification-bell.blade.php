<div class="p-4">
    <div class="relative" x-data="{
        open: false,
        hasUnread: {{ $recentActivities->where('created_at', '>=', now()->subDays(1))->count() > 0 ? 'true' : 'false' }},
        markAsRead() {
            this.hasUnread = false;
            localStorage.setItem('notificationsViewed', new Date().toISOString());
        }
    }" x-init="const lastViewed = localStorage.getItem('notificationsViewed');
    const lastActivity = '{{ $recentActivities->first() ? $recentActivities->first()->created_at->toISOString() : '' }}';
    if (lastViewed && lastActivity && new Date(lastViewed) > new Date(lastActivity)) {
        hasUnread = false;
    }">
        <button @click="open = !open; if(open && hasUnread) markAsRead();"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-7 h-7">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
            <span x-show="hasUnread"
                class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-medium text-[10px] transform translate-x-1/2 -translate-y-1/2">
                {{ $recentActivities->where('created_at', '>=', now()->subDays(1))->count() > 9 ? '9+' : $recentActivities->where('created_at', '>=', now()->subDays(1))->count() }}
            </span>
        </button>
        <div x-show="open" @click.away="open = false"
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
            <div class="py-2">
                <div class="px-4 py-2 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Recent Activities</h3>
                </div>
                @if ($recentActivities->count() > 0)
                    <div class="max-h-64 overflow-y-auto">
                        @foreach ($recentActivities as $activity)
                            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center {{ $activity->action_color }}">
                                            <i class="iconify w-4 h-4" data-icon="{{ $activity->action_icon }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $activity->description }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            @if ($activity->pr_number)
                                                PR #{{ $activity->pr_number }}
                                            @endif
                                            @if ($activity->document_name)
                                                - {{ $activity->document_name }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- <div class="px-4 py-2 border-t border-gray-200">
                        <a href="{{ route('user.requests') }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View all activities →
                        </a>
                    </div> --}}
                @else
                    <div class="px-4 py-8 text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No recent activities</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
