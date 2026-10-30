@php
    $itemClass = fn (bool $active) => 'flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[13px] font-medium transition-all duration-150 '.($active
        ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-semibold shadow-2xs'
        : 'text-slate-700 dark:text-zinc-200 hover:bg-slate-100/70 dark:hover:bg-zinc-900 active:bg-slate-200/60');
    
    $iconClass = fn (bool $active) => 'h-[18px] w-[18px] shrink-0 transition-colors '.($active
        ? 'text-blue-600 dark:text-blue-400'
        : 'text-slate-400 dark:text-zinc-500');

    $chevronIcon = '<svg class="w-4 h-4 text-slate-300 dark:text-zinc-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>';

    $sectionHeader = 'px-3 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500 select-none';

    $metrics = request()->attributes->get('sidebar.navigation.metrics') ?? [];
    if (!isset($metrics['statusLabels'])) {
        $metrics['statusLabels'] = \App\Models\StatusLabel::shownInNav()->withCount('assets')->orderBy('name')->get();
    }
    if (!isset($metrics['totalAssets'])) {
        $metrics['totalAssets'] = \App\Models\Asset::count();
    }
    if (!isset($metrics['auditOverdue'])) {
        $metrics['auditOverdue'] = \App\Models\Asset::whereNotNull('next_audit_date')->whereDate('next_audit_date', '<', now()->toDateString())->count();
    }
    if (!isset($metrics['lowStockConsumables'])) {
        $metrics['lowStockConsumables'] = \App\Models\Consumable::whereColumn('qty', '<=', 'min_qty')->count();
    }
    if (!isset($metrics['pendingRequests'])) {
        $metrics['pendingRequests'] = \App\Models\AssetRequest::where('status', 'pending')->count();
    }
    if (!isset($metrics['totalUsers'])) {
        $metrics['totalUsers'] = \App\Models\User::count();
    }
    foreach ([
        'Accessories' => ['view_accessories', \App\Models\Accessory::class],
        'Consumables' => ['view_consumables', \App\Models\Consumable::class],
        'Components' => ['view_components', \App\Models\Component::class],
        'Licenses' => ['view_licenses', \App\Models\License::class],
    ] as $inventoryKey => [$permission, $model]) {
        $metricKey = 'inventory'. $inventoryKey;
        if (!isset($metrics[$metricKey])) {
            $metrics[$metricKey] = auth()->user()->can($permission) ? $model::count() : 0;
        }
    }
    $metrics['totalInventory'] = collect(['Accessories', 'Consumables', 'Components', 'Licenses'])
        ->sum(fn ($key) => $metrics['inventory'.$key]);
    request()->attributes->set('sidebar.navigation.metrics', $metrics);
@endphp

{{-- 1. Direct Item: Dashboard --}}
<div class="py-1 space-y-0.5">
    @php $active = request()->routeIs('dashboard'); @endphp
    <a href="{{ route('dashboard') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
        <div class="flex items-center gap-3 min-w-0">
            <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
            <span class="truncate">{{ __('Dashboard') }}</span>
        </div>
        {!! $chevronIcon !!}
    </a>
</div>

{{-- 2. Section: Service Desk --}}
@if(auth()->user()->can('view_all_tickets') || auth()->user()->can('manage_settings'))
<div class="py-1 space-y-0.5">
    <p class="{{ $sectionHeader }}">{{ __('Service Desk') }}</p>

    @can('view_all_tickets')
        @if(auth()->user()->isTicketStaff())
            @php $active = request()->routeIs('admin.tickets.*'); @endphp
            <a href="{{ route('admin.tickets.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m-7.5 8.25 3-3h9A2.25 2.25 0 0020.25 15V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v14.25z"/>
                    </svg>
                    <span class="truncate">{{ __('Ticket Queue') }}</span>
                </div>
                {!! $chevronIcon !!}
            </a>
        @endif
    @endcan

    @can('manage_settings')
        @php $active = request()->routeIs('admin.knowledge-base.*'); @endphp
        <a href="{{ route('admin.knowledge-base.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
                <span class="truncate">{{ __('Knowledge Base') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan
</div>
@endif

{{-- 3. Section: Assets --}}
@can('view_assets')
<div class="py-1 space-y-0.5">
    <p class="{{ $sectionHeader }}">{{ __('Assets') }}</p>

    @php
        $currentStatusFilter = request()->get('statusFilter') ?? request()->get('status_label');
        $allAssetsActive = request()->routeIs('assets.index') && empty($currentStatusFilter);
    @endphp
    <a href="{{ route('assets.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($allAssetsActive) }}">
        <div class="flex items-center gap-3 min-w-0">
            <svg class="{{ $iconClass($allAssetsActive) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="truncate">{{ __('All Assets') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">
                {{ $metrics['totalAssets'] }}
            </span>
            {!! $chevronIcon !!}
        </div>
    </a>

    {{-- Status Labels (Filtered Shortcuts) --}}
    @foreach(($metrics['statusLabels'] ?? []) as $statusLabel)
        @php
            $isLabelActive = request()->routeIs('assets.index') && (
                (string)$currentStatusFilter === (string)$statusLabel->id ||
                ($currentStatusFilter === 'active' && ($statusLabel->type === 'deployable' || $statusLabel->name === 'Active')) ||
                ($currentStatusFilter === 'stock' && ($statusLabel->type === 'pending' || $statusLabel->name === 'Stock')) ||
                ($currentStatusFilter === 'archived' && $statusLabel->type === 'archived')
            );
        @endphp
        <a href="{{ route('assets.index', ['statusFilter' => $statusLabel->id]) }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($isLabelActive) }} pl-7">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="h-2 w-2 rounded-full shrink-0 {{ match($statusLabel->type) {
                    'deployable'   => 'bg-emerald-500',
                    'pending'      => 'bg-amber-500',
                    'undeployable' => 'bg-rose-500',
                    'archived'     => 'bg-slate-400',
                    default        => 'bg-blue-500',
                } }}"></span>
                <span class="truncate text-xs">{{ $statusLabel->name }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if($statusLabel->assets_count > 0)
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-zinc-500">{{ $statusLabel->assets_count }}</span>
                @endif
                {!! $chevronIcon !!}
            </div>
        </a>
    @endforeach

    @php $active = request()->routeIs('asset-models.*'); @endphp
    <a href="{{ route('asset-models.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
        <div class="flex items-center gap-3 min-w-0">
            <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
            </svg>
            <span class="truncate">{{ __('Asset Models') }}</span>
        </div>
        {!! $chevronIcon !!}
    </a>
</div>
@endcan

{{-- 4. Section: Inventory --}}
@if(auth()->user()->can('view_accessories') || auth()->user()->can('view_consumables') || auth()->user()->can('view_components') || auth()->user()->can('view_licenses'))
<div class="py-1 space-y-0.5">
    <div class="flex items-center justify-between px-3 pt-3 pb-1">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500 select-none">{{ __('Inventory') }}</p>
        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">{{ $metrics['totalInventory'] }}</span>
    </div>

    @can('view_accessories')
        @php $active = request()->routeIs('accessories.*'); @endphp
        <a href="{{ route('accessories.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                </svg>
                <span class="truncate">{{ __('Accessories') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">{{ $metrics['inventoryAccessories'] }}</span>
                {!! $chevronIcon !!}
            </div>
        </a>
    @endcan

    @can('view_consumables')
        @php $active = request()->routeIs('consumables.*'); @endphp
        <a href="{{ route('consumables.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="truncate">{{ __('Consumables') }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if($metrics['lowStockConsumables'] > 0)
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300">
                        {{ $metrics['lowStockConsumables'] }}
                    </span>
                @endif
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">{{ $metrics['inventoryConsumables'] }}</span>
                {!! $chevronIcon !!}
            </div>
        </a>
    @endcan

    @can('view_components')
        @php $active = request()->routeIs('components.*'); @endphp
        <a href="{{ route('components.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                <span class="truncate">{{ __('Components') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">{{ $metrics['inventoryComponents'] }}</span>
                {!! $chevronIcon !!}
            </div>
        </a>
    @endcan

    @can('view_licenses')
        @php $active = request()->routeIs('licenses.*'); @endphp
        <a href="{{ route('licenses.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                <span class="truncate">{{ __('Licenses') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">{{ $metrics['inventoryLicenses'] }}</span>
                {!! $chevronIcon !!}
            </div>
        </a>
    @endcan
</div>
@endif

{{-- 5. Section: Operations --}}
<div class="py-1 space-y-0.5">
    <p class="{{ $sectionHeader }}">{{ __('Operations') }}</p>

    @can('update_assets')
        @php $active = request()->routeIs('asset-requests.*'); @endphp
        <a href="{{ route('asset-requests.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                </svg>
                <span class="truncate">{{ __('Asset Requests') }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if($metrics['pendingRequests'] > 0)
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300">
                        {{ $metrics['pendingRequests'] > 99 ? '99+' : $metrics['pendingRequests'] }}
                    </span>
                @endif
                {!! $chevronIcon !!}
            </div>
        </a>
    @endcan

    @can('maintenance_assets')
        @php $active = request()->routeIs('maintenances.*'); @endphp
        <a href="{{ route('maintenances.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                </svg>
                <span class="truncate">{{ __('Maintenance') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan

    @can('audit_assets')
        @php $active = request()->routeIs('audit.*'); @endphp
        <a href="{{ route('audit.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="truncate">{{ __('Audit') }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if($metrics['auditOverdue'] > 0)
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300">
                        {{ $metrics['auditOverdue'] }}
                    </span>
                @endif
                {!! $chevronIcon !!}
            </div>
        </a>
    @endcan

    @can('assign_assets')
        @php $active = request()->routeIs('forms.handover', 'handovers.create'); @endphp
        <a href="{{ route('forms.handover') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 4.086V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate">{{ __('Handover Form') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan

    @can('export_reports')
        @php $active = request()->routeIs('documents.*'); @endphp
        <a href="{{ route('documents.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 4.086V19a2 2 0 01-2 2z" />
                </svg>
                <span class="truncate">{{ __('Documents') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan
</div>

{{-- 6. Section: Reports --}}
@can('view_reports')
<div class="py-1 space-y-0.5">
    <p class="{{ $sectionHeader }}">{{ __('Reports') }}</p>

    @php $active = request()->routeIs('maintenance-report.*'); @endphp
    <a href="{{ route('maintenance-report.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
        <div class="flex items-center gap-3 min-w-0">
            <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="truncate">{{ __('Maintenance Report') }}</span>
        </div>
        {!! $chevronIcon !!}
    </a>

    @if(auth()->user()->isAdmin())
        @php $active = request()->routeIs('activity-log.*'); @endphp
        <a href="{{ route('activity-log.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="truncate">{{ __('Activity Log') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>

        @php $active = request()->routeIs('document-report.*'); @endphp
        <a href="{{ route('document-report.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <span class="truncate">{{ __('Document Report') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endif
</div>
@endcan

{{-- 7. Section: Organization --}}
@if(auth()->user()->can('view_categories') || auth()->user()->can('view_locations') || auth()->user()->can('view_suppliers') || auth()->user()->can('view_manufacturers') || auth()->user()->isAdmin())
<div class="py-1 space-y-0.5">
    <p class="{{ $sectionHeader }}">{{ __('Organization') }}</p>

    @can('view_categories')
        @php $active = request()->routeIs('categories.*'); @endphp
        <a href="{{ route('categories.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="truncate">{{ __('Categories') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan

    @can('view_locations')
        @php $active = request()->routeIs('locations.*'); @endphp
        <a href="{{ route('locations.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ __('Locations') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan

    @can('view_suppliers')
        @php $active = request()->routeIs('suppliers.*'); @endphp
        <a href="{{ route('suppliers.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                <span class="truncate">{{ __('Suppliers') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan

    @can('view_manufacturers')
        @php $active = request()->routeIs('manufacturers.*'); @endphp
        <a href="{{ route('manufacturers.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="truncate">{{ __('Manufacturers') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endcan

    @if(auth()->user()->isAdmin())
        @php $active = request()->routeIs('status-labels.*'); @endphp
        <a href="{{ route('status-labels.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span class="truncate">{{ __('Status Labels') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>

        @php $active = request()->routeIs('departments.*'); @endphp
        <a href="{{ route('departments.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="truncate">{{ __('Departments') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>

        @php $active = request()->routeIs('companies.*'); @endphp
        <a href="{{ route('companies.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                </svg>
                <span class="truncate">{{ __('Companies') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>

        @php $active = request()->routeIs('depreciations.*'); @endphp
        <a href="{{ route('depreciations.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="truncate">{{ __('Depreciation') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endif
</div>
@endif

{{-- 8. Main Menu: People --}}
@can('view_users')
<div class="py-1 space-y-0.5">
    @php $active = request()->routeIs('users.*'); @endphp
    <a href="{{ route('users.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
        <div class="flex items-center gap-3 min-w-0">
            <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate">{{ __('People') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">
                {{ $metrics['totalUsers'] }}
            </span>
            {!! $chevronIcon !!}
        </div>
    </a>


</div>
@else
<div class="py-1 space-y-0.5">
    <p class="{{ $sectionHeader }}">{{ __('Account') }}</p>

    @php $active = request()->routeIs('users.show') && (int) request()->route('user')?->id === (int) auth()->id(); @endphp
    <a href="{{ route('users.show', auth()->user()) }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
        <div class="flex items-center gap-3 min-w-0">
            <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="truncate">{{ __('My Profile & Items') }}</span>
        </div>
        {!! $chevronIcon !!}
    </a>
</div>
@endcan

{{-- 9. Section: Settings --}}
@php
    $canImport = auth()->user()->canAny(['create_assets', 'create_users', 'manage_locations', 'create_accessories', 'create_consumables', 'create_components', 'create_licenses']);
@endphp
@if(auth()->user()->isAdmin() || $canImport)
<div class="py-1 space-y-0.5">
    <p class="{{ $sectionHeader }}">{{ __('Settings') }}</p>

    @if(auth()->user()->isAdmin())
        @php $active = request()->routeIs('settings.index'); @endphp
        <a href="{{ route('settings.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ __('General Settings') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>

        @php $active = request()->routeIs('settings.groups.*'); @endphp
        <a href="{{ route('settings.groups.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span class="truncate">{{ __('Permission Groups') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>

        @php $active = request()->routeIs('settings.custom-fields'); @endphp
        <a href="{{ route('settings.custom-fields') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
                <span class="truncate">{{ __('Custom Fields') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>

        @can('manage_settings')
            @php $active = request()->routeIs('settings.handover-templates'); @endphp
            <a href="{{ route('settings.handover-templates') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span class="truncate">{{ __('Form Templates') }}</span>
                </div>
                {!! $chevronIcon !!}
            </a>
        @endcan
    @endif

    @if($canImport)
        @php $active = request()->routeIs('import.*'); @endphp
        <a href="{{ route('import.index') }}" wire:navigate @click="mobileMenuOpen = false" class="{{ $itemClass($active) }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="{{ $iconClass($active) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                </svg>
                <span class="truncate">{{ __('Bulk Import') }}</span>
            </div>
            {!! $chevronIcon !!}
        </a>
    @endif

    {{-- Theme Mode Toggle --}}
    <button type="button" onclick="toggleTheme()" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[13px] font-medium text-slate-700 dark:text-zinc-200 hover:bg-slate-100/70 dark:hover:bg-zinc-900 active:bg-slate-200/60 transition-all duration-150">
        <div class="flex items-center gap-3 min-w-0">
            <svg class="h-[18px] w-[18px] text-slate-400 dark:text-zinc-500 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg class="h-[18px] w-[18px] text-slate-400 dark:text-zinc-500 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="truncate">{{ __('Appearance') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] font-semibold text-slate-400 dark:text-zinc-500 uppercase dark:hidden">{{ __('Light') }}</span>
            <span class="text-[11px] font-semibold text-slate-400 dark:text-zinc-500 uppercase hidden dark:inline">{{ __('Dark') }}</span>
            {!! $chevronIcon !!}
        </div>
    </button>
</div>
@endif

{{-- 10. Account Actions: Log Out --}}
<div class="pt-2 pb-16">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-[13px] font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 active:bg-rose-100 transition-all duration-150">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="h-[18px] w-[18px] text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span class="truncate">{{ __('Log Out') }}</span>
            </div>
            <svg class="w-4 h-4 text-rose-400/60 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>
</div>
