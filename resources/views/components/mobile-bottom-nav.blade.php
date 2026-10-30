@props(['currentRoute' => null])

@php
    $navigationMetrics = request()->attributes->get('sidebar.navigation.metrics', []);
    $pendingRequests = $navigationMetrics['pendingRequests'] ?? \App\Models\AssetRequest::where('status', 'pending')->count();
    $isMenuRoute = request()->routeIs('categories.*', 'locations.*', 'suppliers.*', 'manufacturers.*', 'departments.*', 'companies.*', 'depreciations.*', 'reports.*', 'users.*', 'settings.*', 'profile.*', 'activity-log.*', 'audit.*', 'import.*');

    $inventoryRoute = null;
    foreach ([
        'view_accessories' => 'accessories.index',
        'view_consumables' => 'consumables.index',
        'view_components' => 'components.index',
        'view_licenses' => 'licenses.index',
    ] as $permission => $routeName) {
        if (auth()->user()->can($permission)) {
            $inventoryRoute = $routeName;
            break;
        }
    }
@endphp

<!-- Mobile Bottom Navigation - 5 High-Utility Thumb Tabs (Persistent, Always Visible) -->
<nav 
    data-bottom-nav
    class="fixed bottom-0 left-0 right-0 z-50 border-t backdrop-blur-xl lg:hidden bg-white/95 dark:bg-zinc-900/95 border-slate-200/80 dark:border-zinc-800 transition-colors shadow-lg"
    x-data="{ 
        isStandalone: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
        get bottomPadding() {
            return this.isStandalone && /iPhone|iPad|iPod/.test(navigator.userAgent) ? 'pb-6' : 'pb-2';
        }
    }"
    :class="bottomPadding"
>
    <div class="mx-auto flex max-w-md items-center justify-around px-2 pt-1.5">
        
        <!-- 1. Dashboard -->
        <a 
            href="{{ route('dashboard') }}" 
            wire:navigate
            @click="mobileMenuOpen = false"
            class="flex flex-1 flex-col items-center gap-1 rounded-xl px-2 py-1.5 transition-all duration-150"
            :class="!mobileMenuOpen && {{ request()->routeIs('dashboard') ? 'true' : 'false' }} ? 'text-[var(--primary-color)] font-semibold' : 'text-slate-500 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
        >
            <div class="relative">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :stroke-width="!mobileMenuOpen && {{ request()->routeIs('dashboard') ? 'true' : 'false' }} ? '2.5' : '2'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <span class="text-[11px] leading-tight">{{ __('Home') }}</span>
        </a>

        <!-- 2. Assets -->
        @can('view_assets')
        <a 
            href="{{ route('assets.index') }}" 
            wire:navigate
            @click="mobileMenuOpen = false"
            class="flex flex-1 flex-col items-center gap-1 rounded-xl px-2 py-1.5 transition-all duration-150"
            :class="!mobileMenuOpen && {{ request()->routeIs('assets.*', 'asset-models.*', 'status-labels.*') ? 'true' : 'false' }} ? 'text-[var(--primary-color)] font-semibold' : 'text-slate-500 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
        >
            <div class="relative">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :stroke-width="!mobileMenuOpen && {{ request()->routeIs('assets.*', 'asset-models.*', 'status-labels.*') ? 'true' : 'false' }} ? '2.5' : '2'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="text-[11px] leading-tight">{{ __('Assets') }}</span>
        </a>
        @endcan

        <!-- 3. Requests (Priority with live Badge) -->
        @can('update_assets')
        <a 
            href="{{ route('asset-requests.index') }}" 
            wire:navigate
            @click="mobileMenuOpen = false"
            class="flex flex-1 flex-col items-center gap-1 rounded-xl px-2 py-1.5 transition-all duration-150"
            :class="!mobileMenuOpen && {{ request()->routeIs('asset-requests.*', 'maintenances.*') ? 'true' : 'false' }} ? 'text-[var(--primary-color)] font-semibold' : 'text-slate-500 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
        >
            <div class="relative">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :stroke-width="!mobileMenuOpen && {{ request()->routeIs('asset-requests.*', 'maintenances.*') ? 'true' : 'false' }} ? '2.5' : '2'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                @if($pendingRequests > 0)
                    <span class="absolute -top-1 -right-2 flex h-3.5 min-w-3.5 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold text-white shadow-2xs">
                        {{ $pendingRequests > 9 ? '9+' : $pendingRequests }}
                    </span>
                @endif
            </div>
            <span class="text-[11px] leading-tight">{{ __('Requests') }}</span>
        </a>
        @endcan

        <!-- 4. Inventory -->
        @if($inventoryRoute)
        <a 
            href="{{ route($inventoryRoute) }}" 
            wire:navigate
            @click="mobileMenuOpen = false"
            class="flex flex-1 flex-col items-center gap-1 rounded-xl px-2 py-1.5 transition-all duration-150"
            :class="!mobileMenuOpen && {{ request()->routeIs('accessories.*', 'consumables.*', 'components.*', 'licenses.*') ? 'true' : 'false' }} ? 'text-[var(--primary-color)] font-semibold' : 'text-slate-500 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
        >
            <div class="relative">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :stroke-width="!mobileMenuOpen && {{ request()->routeIs('accessories.*', 'consumables.*', 'components.*', 'licenses.*') ? 'true' : 'false' }} ? '2.5' : '2'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <span class="text-[11px] leading-tight">{{ __('Inventory') }}</span>
        </a>
        @endif

        <!-- 5. All Menus (Opens Full-Page Settings View) -->
        <button 
            type="button"
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="flex flex-1 flex-col items-center gap-1 rounded-xl px-2 py-1.5 transition-all duration-150"
            :class="mobileMenuOpen ? 'text-[var(--primary-color)] font-semibold' : 'text-slate-500 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
        >
            <div class="relative">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" :stroke-width="mobileMenuOpen ? '2.5' : '2'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
            <span class="text-[11px] leading-tight">{{ __('Menu') }}</span>
        </button>

    </div>

    <!-- Safe area for iOS PWA -->
    <div class="h-[env(safe-area-inset-bottom)]"></div>
</nav>
