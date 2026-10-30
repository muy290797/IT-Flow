@php
    $topItemLink = fn (bool $active) => 'sb-item group flex items-center justify-between w-full px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 '.($active
        ? 'sb-nav-active bg-blue-50/90 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 font-semibold shadow-2xs'
        : 'text-slate-600 dark:text-zinc-300 hover:bg-slate-100/80 dark:hover:bg-zinc-900 hover:text-slate-900 dark:hover:text-white');

    $groupButton = fn (bool $active) => 'sb-group-btn group flex items-center justify-between w-full px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 select-none cursor-pointer '.($active
        ? 'text-blue-700 dark:text-blue-300 font-semibold bg-blue-50/50 dark:bg-blue-950/30'
        : 'text-slate-600 dark:text-zinc-300 hover:bg-slate-100/80 dark:hover:bg-zinc-900 hover:text-slate-900 dark:hover:text-white');

    $subItemLink = fn (bool $active) => 'sb-sub-item flex items-center justify-between w-full px-2.5 py-1.5 rounded-lg text-[12.5px] font-medium transition-all duration-150 '.($active
        ? 'sb-nav-active bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 font-semibold'
        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-100 hover:bg-slate-100/60 dark:hover:bg-zinc-800/60');

    $navIcon = fn (bool $active) => 'h-[17px] w-[17px] shrink-0 transition-colors '.($active
        ? 'text-blue-600 dark:text-blue-400'
        : 'text-slate-400 dark:text-zinc-500 group-hover:text-slate-700 dark:group-hover:text-zinc-300');

    $metrics = request()->attributes->get('sidebar.navigation.metrics');
    if ($metrics === null) {
        $metrics = app(\App\Services\SidebarMetrics::class)->for(auth()->user());
    }
    request()->attributes->set('sidebar.navigation.metrics', $metrics);
@endphp

<div class="space-y-1">

    {{-- 1. Direct Item: Dashboard --}}
    @php $active = request()->routeIs('dashboard'); @endphp
    <a href="{{ route('dashboard') }}" wire:navigate class="{{ $topItemLink($active) }}" title="{{ __('Dashboard') }}">
        <div class="flex items-center gap-2.5 min-w-0">
            <svg class="{{ $navIcon($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
            <span class="sb-label truncate">{{ __('Dashboard') }}</span>
        </div>
    </a>

    {{-- 2. Dropdown Group: Service Desk (if permitted) --}}
    @if(auth()->user()->can('view_all_tickets') || auth()->user()->can('manage_settings'))
        @php
            $isDeskActive = request()->routeIs('admin.tickets.*', 'admin.knowledge-base.*');
        @endphp
        <div class="sb-group" x-data="{ open: {{ $isDeskActive ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="{{ $groupButton($isDeskActive) }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="{{ $navIcon($isDeskActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m-7.5 8.25 3-3h9A2.25 2.25 0 0020.25 15V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v14.25z"/>
                    </svg>
                    <span class="sb-label truncate">{{ __('Service Desk') }}</span>
                </div>
                <svg class="sb-group-chevron w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="sb-group-sub border-l-2 border-slate-200/80 dark:border-zinc-800 ml-5 pl-2.5 my-1 space-y-0.5">
                <div class="sb-flyout-header hidden px-2.5 py-1 mb-1 border-b border-slate-100 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ __('Service Desk') }}
                </div>
                @can('view_all_tickets')
                    @if(auth()->user()->isTicketStaff())
                        @php $active = request()->routeIs('admin.tickets.*'); @endphp
                        <a href="{{ route('admin.tickets.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                            <span class="truncate">{{ __('Ticket Queue') }}</span>
                        </a>
                    @endif
                @endcan
                @can('manage_settings')
                    @php $active = request()->routeIs('admin.knowledge-base.*'); @endphp
                    <a href="{{ route('admin.knowledge-base.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Knowledge Base') }}</span>
                    </a>
                @endcan
            </div>
        </div>
    @endif

    {{-- 3. Dropdown Group: Assets --}}
    @can('view_assets')
        @php
            $isAssetsActive = request()->routeIs('assets.*', 'asset-models.*');
        @endphp
        <div class="sb-group" x-data="{ open: {{ $isAssetsActive ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="{{ $groupButton($isAssetsActive) }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="{{ $navIcon($isAssetsActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="sb-label truncate">{{ __('Assets') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="sb-badge text-[10px] font-semibold px-1.5 py-0.2 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">
                        {{ $metrics['totalAssets'] }}
                    </span>
                    <svg class="sb-group-chevron w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
            <div x-show="open" x-cloak class="sb-group-sub border-l-2 border-slate-200/80 dark:border-zinc-800 ml-5 pl-2.5 my-1 space-y-0.5">
                <div class="sb-flyout-header hidden px-2.5 py-1 mb-1 border-b border-slate-100 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ __('Assets') }}
                </div>
                @php
                    $currentStatusFilter = request()->get('statusFilter') ?? request()->get('status_label');
                    $allAssetsActive = request()->routeIs('assets.index') && empty($currentStatusFilter);
                @endphp
                <a href="{{ route('assets.index') }}" wire:navigate class="{{ $subItemLink($allAssetsActive) }}">
                    <span class="truncate">{{ __('All Assets') }}</span>
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-zinc-500">{{ $metrics['totalAssets'] }}</span>
                </a>

                {{-- Status Labels (Filtered shortcuts) --}}
                @foreach(($metrics['statusLabels'] ?? []) as $statusLabel)
                    @php
                        $isLabelActive = request()->routeIs('assets.index') && (
                            (string)$currentStatusFilter === (string)$statusLabel->id ||
                            ($currentStatusFilter === 'active' && ($statusLabel->type === 'deployable' || $statusLabel->name === 'Active')) ||
                            ($currentStatusFilter === 'stock' && ($statusLabel->type === 'pending' || $statusLabel->name === 'Stock')) ||
                            ($currentStatusFilter === 'archived' && $statusLabel->type === 'archived')
                        );
                    @endphp
                    <a href="{{ route('assets.index', ['statusFilter' => $statusLabel->id]) }}" wire:navigate class="{{ $subItemLink($isLabelActive) }}">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="h-1.5 w-1.5 rounded-full shrink-0 {{ match($statusLabel->type) {
                                'deployable'   => 'bg-emerald-500',
                                'pending'      => 'bg-amber-500',
                                'undeployable' => 'bg-rose-500',
                                'archived'     => 'bg-slate-400',
                                default        => 'bg-blue-500',
                            } }}"></span>
                            <span class="truncate">{{ $statusLabel->name }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $statusLabel->assets_count }}</span>
                    </a>
                @endforeach

                @php $active = request()->routeIs('asset-models.*'); @endphp
                <a href="{{ route('asset-models.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                    <span class="truncate">{{ __('Asset Models') }}</span>
                </a>
            </div>
        </div>
    @endcan

    {{-- 4. Dropdown Group: Inventory --}}
    @if(auth()->user()->can('view_accessories') || auth()->user()->can('view_consumables') || auth()->user()->can('view_components') || auth()->user()->can('view_licenses'))
        @php
            $isInvActive = request()->routeIs('accessories.*', 'consumables.*', 'components.*', 'licenses.*');
        @endphp
        <div class="sb-group" x-data="{ open: {{ $isInvActive ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="{{ $groupButton($isInvActive) }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="{{ $navIcon($isInvActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                    </svg>
                    <span class="sb-label truncate">{{ __('Inventory') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="sb-badge text-[10px] font-semibold px-1.5 py-0.2 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">
                        {{ $metrics['totalInventory'] }}
                    </span>
                    @if($metrics['lowStockConsumables'] > 0)
                        <span class="sb-badge text-[10px] font-semibold px-1.5 py-0.2 rounded-md bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                            {{ $metrics['lowStockConsumables'] }}
                        </span>
                    @endif
                    <svg class="sb-group-chevron w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
            <div x-show="open" x-cloak class="sb-group-sub border-l-2 border-slate-200/80 dark:border-zinc-800 ml-5 pl-2.5 my-1 space-y-0.5">
                <div class="sb-flyout-header hidden px-2.5 py-1 mb-1 border-b border-slate-100 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ __('Inventory') }}
                </div>
                @can('view_accessories')
                    @php $active = request()->routeIs('accessories.*'); @endphp
                    <a href="{{ route('accessories.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Accessories') }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $metrics['inventoryAccessories'] }}</span>
                    </a>
                @endcan

                @can('view_consumables')
                    @php $active = request()->routeIs('consumables.*'); @endphp
                    <a href="{{ route('consumables.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Consumables') }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $metrics['inventoryConsumables'] }}</span>
                    </a>
                @endcan

                @can('view_components')
                    @php $active = request()->routeIs('components.*'); @endphp
                    <a href="{{ route('components.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Components') }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $metrics['inventoryComponents'] }}</span>
                    </a>
                @endcan

                @can('view_licenses')
                    @php $active = request()->routeIs('licenses.*'); @endphp
                    <a href="{{ route('licenses.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Licenses') }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $metrics['inventoryLicenses'] }}</span>
                    </a>
                @endcan
            </div>
        </div>
    @endif

    {{-- 5. Dropdown Group: Operations --}}
    @php
        $isOpsActive = request()->routeIs('asset-requests.*', 'maintenances.*', 'audit.*', 'forms.handover', 'handovers.create', 'documents.*');
    @endphp
    <div class="sb-group" x-data="{ open: {{ $isOpsActive ? 'true' : 'false' }} }">
        <button type="button" @click="open = !open" class="{{ $groupButton($isOpsActive) }}">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="{{ $navIcon($isOpsActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="sb-label truncate">{{ __('Operations') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                @if($metrics['pendingRequests'] > 0)
                    <span class="sb-badge text-[10px] font-semibold px-1.5 py-0.2 rounded-md bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                        {{ $metrics['pendingRequests'] }}
                    </span>
                @endif
                <svg class="sb-group-chevron w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </button>
        <div x-show="open" x-cloak class="sb-group-sub border-l-2 border-slate-200/80 dark:border-zinc-800 ml-5 pl-2.5 my-1 space-y-0.5">
            <div class="sb-flyout-header hidden px-2.5 py-1 mb-1 border-b border-slate-100 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                {{ __('Operations') }}
            </div>
            @can('update_assets')
                @php $active = request()->routeIs('asset-requests.*'); @endphp
                <a href="{{ route('asset-requests.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                    <span class="truncate">{{ __('Asset Requests') }}</span>
                    @if($metrics['pendingRequests'] > 0)
                        <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">{{ $metrics['pendingRequests'] }}</span>
                    @endif
                </a>
            @endcan

            @can('maintenance_assets')
                @php $active = request()->routeIs('maintenances.*'); @endphp
                <a href="{{ route('maintenances.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                    <span class="truncate">{{ __('Maintenance') }}</span>
                </a>
            @endcan

            @can('audit_assets')
                @php $active = request()->routeIs('audit.*'); @endphp
                <a href="{{ route('audit.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                    <span class="truncate">{{ __('Audit') }}</span>
                    @if($metrics['auditOverdue'] > 0)
                        <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400">{{ $metrics['auditOverdue'] }}</span>
                    @endif
                </a>
            @endcan

            @can('assign_assets')
                @php $active = request()->routeIs('forms.handover', 'handovers.create'); @endphp
                <a href="{{ route('forms.handover') }}" wire:navigate class="{{ $subItemLink($active) }}">
                    <span class="truncate">{{ __('Handover Form') }}</span>
                </a>
            @endcan

            @can('export_reports')
                @php $active = request()->routeIs('documents.*'); @endphp
                <a href="{{ route('documents.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                    <span class="truncate">{{ __('Documents') }}</span>
                </a>
            @endcan
        </div>
    </div>

    {{-- 6. Dropdown Group: Reports --}}
    @can('view_reports')
        @php
            $isReportsActive = request()->routeIs('maintenance-report.*', 'activity-log.*', 'document-report.*');
        @endphp
        <div class="sb-group" x-data="{ open: {{ $isReportsActive ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="{{ $groupButton($isReportsActive) }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="{{ $navIcon($isReportsActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sb-label truncate">{{ __('Reports') }}</span>
                </div>
                <svg class="sb-group-chevron w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="sb-group-sub border-l-2 border-slate-200/80 dark:border-zinc-800 ml-5 pl-2.5 my-1 space-y-0.5">
                <div class="sb-flyout-header hidden px-2.5 py-1 mb-1 border-b border-slate-100 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ __('Reports') }}
                </div>
                @php $active = request()->routeIs('maintenance-report.*'); @endphp
                <a href="{{ route('maintenance-report.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                    <span class="truncate">{{ __('Maintenance') }}</span>
                </a>

                @if(auth()->user()->isAdmin())
                    @php $active = request()->routeIs('activity-log.*'); @endphp
                    <a href="{{ route('activity-log.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Activity Log') }}</span>
                    </a>

                    @php $active = request()->routeIs('document-report.*'); @endphp
                    <a href="{{ route('document-report.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Documents') }}</span>
                    </a>
                @endif
            </div>
        </div>
    @endcan

    {{-- 7. Dropdown Group: Organization --}}
    @if(auth()->user()->can('view_categories') || auth()->user()->can('view_locations') || auth()->user()->can('view_suppliers') || auth()->user()->can('view_manufacturers') || auth()->user()->isAdmin())
        @php
            $isOrgActive = request()->routeIs('categories.*', 'locations.*', 'suppliers.*', 'manufacturers.*', 'status-labels.*', 'departments.*', 'companies.*', 'depreciations.*');
        @endphp
        <div class="sb-group" x-data="{ open: {{ $isOrgActive ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="{{ $groupButton($isOrgActive) }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="{{ $navIcon($isOrgActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="sb-label truncate">{{ __('Organization') }}</span>
                </div>
                <svg class="sb-group-chevron w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="sb-group-sub border-l-2 border-slate-200/80 dark:border-zinc-800 ml-5 pl-2.5 my-1 space-y-0.5">
                <div class="sb-flyout-header hidden px-2.5 py-1 mb-1 border-b border-slate-100 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ __('Organization') }}
                </div>
                @can('view_categories')
                    @php $active = request()->routeIs('categories.*'); @endphp
                    <a href="{{ route('categories.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Categories') }}</span>
                    </a>
                @endcan

                @can('view_locations')
                    @php $active = request()->routeIs('locations.*'); @endphp
                    <a href="{{ route('locations.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Locations') }}</span>
                    </a>
                @endcan

                @can('view_suppliers')
                    @php $active = request()->routeIs('suppliers.*'); @endphp
                    <a href="{{ route('suppliers.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Suppliers') }}</span>
                    </a>
                @endcan

                @can('view_manufacturers')
                    @php $active = request()->routeIs('manufacturers.*'); @endphp
                    <a href="{{ route('manufacturers.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Manufacturers') }}</span>
                    </a>
                @endcan

                @if(auth()->user()->isAdmin())
                    @php $active = request()->routeIs('status-labels.*'); @endphp
                    <a href="{{ route('status-labels.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Status Labels') }}</span>
                    </a>

                    @php $active = request()->routeIs('departments.*'); @endphp
                    <a href="{{ route('departments.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Departments') }}</span>
                    </a>

                    @php $active = request()->routeIs('companies.*'); @endphp
                    <a href="{{ route('companies.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Companies') }}</span>
                    </a>

                    @php $active = request()->routeIs('depreciations.*'); @endphp
                    <a href="{{ route('depreciations.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Depreciation') }}</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- 8. Main Menu: People / Users --}}
    @can('view_users')
        @php $active = request()->routeIs('users.*'); @endphp
        <a href="{{ route('users.index') }}" wire:navigate class="{{ $topItemLink($active) }}" title="{{ __('People') }}">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="{{ $navIcon($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="sb-label truncate">{{ __('People') }}</span>
            </div>
            <span class="sb-badge text-[10px] font-semibold px-1.5 py-0.2 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">
                {{ $metrics['totalUsers'] }}
            </span>
        </a>
    @else
        @php $active = request()->routeIs('users.show') && (int) request()->route('user')?->id === (int) auth()->id(); @endphp
        <a href="{{ route('users.show', auth()->user()) }}" wire:navigate class="{{ $topItemLink($active) }}" title="{{ __('My Profile') }}">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="{{ $navIcon($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="sb-label truncate">{{ __('My Profile & Items') }}</span>
            </div>
        </a>
    @endcan

    {{-- 9. Dropdown Group: Settings --}}
    @php
        $canImport = auth()->user()->canAny(['create_assets', 'create_users', 'manage_locations', 'create_accessories', 'create_consumables', 'create_components', 'create_licenses']);
        $isSettingsActive = request()->routeIs('settings.*', 'import.*');
    @endphp
    @if(auth()->user()->isAdmin() || $canImport)
        <div class="sb-group" x-data="{ open: {{ $isSettingsActive ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="{{ $groupButton($isSettingsActive) }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="{{ $navIcon($isSettingsActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sb-label truncate">{{ __('Settings') }}</span>
                </div>
                <svg class="sb-group-chevron w-3.5 h-3.5 text-slate-400 dark:text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="sb-group-sub border-l-2 border-slate-200/80 dark:border-zinc-800 ml-5 pl-2.5 my-1 space-y-0.5">
                <div class="sb-flyout-header hidden px-2.5 py-1 mb-1 border-b border-slate-100 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                    {{ __('Settings') }}
                </div>
                @if(auth()->user()->isAdmin())
                    @php $active = request()->routeIs('settings.index'); @endphp
                    <a href="{{ route('settings.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('General Settings') }}</span>
                    </a>

                    @php $active = request()->routeIs('settings.groups.*'); @endphp
                    <a href="{{ route('settings.groups.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Permission Groups') }}</span>
                    </a>

                    @php $active = request()->routeIs('settings.custom-fields'); @endphp
                    <a href="{{ route('settings.custom-fields') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Custom Fields') }}</span>
                    </a>

                    @can('manage_settings')
                        @php $active = request()->routeIs('settings.handover-templates'); @endphp
                        <a href="{{ route('settings.handover-templates') }}" wire:navigate class="{{ $subItemLink($active) }}">
                            <span class="truncate">{{ __('Form Templates') }}</span>
                        </a>
                    @endcan
                @endif

                @if($canImport)
                    @php $active = request()->routeIs('import.*'); @endphp
                    <a href="{{ route('import.index') }}" wire:navigate class="{{ $subItemLink($active) }}">
                        <span class="truncate">{{ __('Bulk Import') }}</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

</div>
