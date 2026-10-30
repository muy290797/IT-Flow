{{-- Group Information Card --}}
<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Group Information') }}</flux:heading>
        <flux:subheading>{{ __('Basic details about this permission group') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-1 gap-5">
        <div>
            <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ __('Group Name') }} <span class="text-rose-600 dark:text-rose-400">*</span>
            </label>
            <input type="text" wire:model="name" id="name" placeholder="{{ __('e.g. IT Support, Inventory Officers, Asset Auditors') }}"
                   class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3.5 py-2 text-sm shadow-xs transition placeholder:text-zinc-400 focus:border-[var(--color-accent)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]/15 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-zinc-500">
            @error('name') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ __('Description') }}
            </label>
            <textarea wire:model="description" id="description" rows="3" placeholder="{{ __('Briefly describe what responsibilities and scope this group has') }}"
                      class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3.5 py-2 text-sm shadow-xs transition placeholder:text-zinc-400 focus:border-[var(--color-accent)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]/15 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-zinc-500"></textarea>
            @error('description') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
        </div>
    </div>
</flux:card>

{{-- Permissions Matrix Card --}}
<flux:card class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="lg">{{ __('Permissions Matrix') }}</flux:heading>
            <flux:subheading>{{ __('Configure what members assigned to this group can view, modify, or manage') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" wire:click="grantAll" class="inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('Grant All') }}
            </button>
            <button type="button" wire:click="clearAll" class="inline-flex items-center gap-1 rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ __('Clear All') }}
            </button>
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-4 rounded-xl border border-zinc-200 bg-zinc-50/80 p-3 text-xs dark:border-zinc-800 dark:bg-zinc-800/40">
        <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Legend:') }}</span>
        <div class="flex items-center gap-1.5">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300">0</span>
            <span class="text-zinc-600 dark:text-zinc-400">{{ __('None / Inherit (does not alter user permissions)') }}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300">+</span>
            <span class="text-zinc-600 dark:text-zinc-400">{{ __('Grant (allows permission)') }}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-rose-100 font-bold text-rose-700 dark:bg-rose-900/60 dark:text-rose-300">-</span>
            <span class="text-zinc-600 dark:text-zinc-400">{{ __('Deny (explicitly forbids permission)') }}</span>
        </div>
    </div>

    {{-- Matrix Sections --}}
    <div class="space-y-4">
        @foreach($permissionSections as $section => $items)
            @php
                $sectionGrantedCount = collect($items)->filter(fn($item) => (int) ($permissions[$item['value']] ?? 0) === 1)->count();
                $sectionDeniedCount = collect($items)->filter(fn($item) => (int) ($permissions[$item['value']] ?? 0) === -1)->count();
            @endphp
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 shadow-xs">
                {{-- Section Header --}}
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 bg-zinc-50/90 px-4 py-3 dark:border-zinc-800 dark:bg-zinc-800/60">
                    <div class="flex items-center gap-2.5">
                        <span class="font-semibold text-sm text-zinc-900 dark:text-white">{{ $section }}</span>
                        <div class="flex items-center gap-1.5 text-[11px]">
                            @if($sectionGrantedCount > 0)
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 font-bold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">{{ $sectionGrantedCount }} granted</span>
                            @endif
                            @if($sectionDeniedCount > 0)
                                <span class="rounded-full bg-rose-100 px-2 py-0.5 font-bold text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">{{ $sectionDeniedCount }} denied</span>
                            @endif
                            @if($sectionGrantedCount === 0 && $sectionDeniedCount === 0)
                                <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ __('Default (none)') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click="grantSection('{{ $section }}')" class="rounded-md border border-zinc-200 bg-white px-2 py-1 text-[11px] font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                            {{ __('Grant section') }}
                        </button>
                        <button type="button" wire:click="clearSection('{{ $section }}')" class="rounded-md border border-zinc-200 bg-white px-2 py-1 text-[11px] font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                            {{ __('Clear') }}
                        </button>
                    </div>
                </div>

                {{-- Permissions List in Section --}}
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @foreach($items as $item)
                        @php
                            $val = (string) ($permissions[$item['value']] ?? '0');
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 transition hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                            <div class="min-w-0 flex-1">
                                <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $item['label'] }}</span>
                                <span class="ml-2 font-mono text-[11px] text-zinc-400">{{ $item['value'] }}</span>
                            </div>

                            <div class="flex items-center gap-1 self-start sm:self-auto">
                                {{-- None / Inherit (0) --}}
                                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-semibold transition {{ $val === '0' ? 'border-zinc-400 bg-zinc-100 text-zinc-800 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 shadow-xs ring-1 ring-zinc-400/30' : 'border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800' }}">
                                    <input type="radio" wire:model.live="permissions.{{ $item['value'] }}" value="0" class="sr-only">
                                    <span>{{ __('None') }}</span>
                                </label>

                                {{-- Grant (1) --}}
                                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-semibold transition {{ $val === '1' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 dark:border-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-200 shadow-xs ring-1 ring-emerald-500/30' : 'border-zinc-200 bg-white text-zinc-600 hover:bg-emerald-50/50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-emerald-950/20' }}">
                                    <input type="radio" wire:model.live="permissions.{{ $item['value'] }}" value="1" class="sr-only">
                                    <svg class="h-3 w-3 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ __('Grant') }}</span>
                                </label>

                                {{-- Deny (-1) --}}
                                <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-semibold transition {{ $val === '-1' ? 'border-rose-500 bg-rose-50 text-rose-800 dark:border-rose-700 dark:bg-rose-950/50 dark:text-rose-200 shadow-xs ring-1 ring-rose-500/30' : 'border-zinc-200 bg-white text-zinc-600 hover:bg-rose-50/50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-rose-950/20' }}">
                                    <input type="radio" wire:model.live="permissions.{{ $item['value'] }}" value="-1" class="sr-only">
                                    <svg class="h-3 w-3 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>{{ __('Deny') }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</flux:card>

{{-- Role & Menu Preview Card --}}
@php
    $sourceLabel = fn (string $source) => match ($source) {
        'group_grant' => __('Granted by group'),
        'group_deny' => __('Denied by group'),
        'role' => __('Role default'),
        default => __('Not granted'),
    };

    $capabilityChip = function (array $row): string {
        $base = 'inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-medium leading-tight ';
        $allowed = $row['allowed'] ?? false;
        $source = $row['source'] ?? 'none';

        if ($allowed && $source === 'group_grant') {
            return $base.'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300';
        }
        if ($allowed) {
            return $base.'border-zinc-200 bg-zinc-50 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300';
        }
        if ($source === 'group_deny') {
            return $base.'border-rose-300 bg-rose-50 text-rose-700 line-through dark:border-rose-800 dark:bg-rose-950/40 dark:text-rose-300';
        }

        return $base.'border-zinc-200 bg-white text-zinc-400 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-500';
    };

    $roleOnlyMenus = [
        'Companies', 'Depreciation', 'Activity Log', 'Document Report',
        'General Settings', 'Permission Groups', 'Custom Fields',
    ];
@endphp

<flux:card class="space-y-5" x-data="{ roleTab: 'admin' }">
    <div>
        <flux:heading size="lg">{{ __('Role & Menu Preview') }}</flux:heading>
        <flux:subheading>{{ __('What members of this group see and can do, per role. Updates as you change the matrix above.') }}</flux:subheading>
    </div>

    {{-- Role tabs --}}
    <div class="flex flex-wrap gap-1.5 rounded-xl border border-zinc-200 bg-zinc-50/80 p-1 dark:border-zinc-800 dark:bg-zinc-900/60">
        @foreach($rolePreviews as $roleKey => $preview)
            <button type="button" @click="roleTab = '{{ $roleKey }}'"
                    :class="roleTab === '{{ $roleKey }}' ? 'bg-white text-zinc-900 shadow-sm dark:bg-zinc-800 dark:text-white' : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200'"
                    class="flex flex-1 items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition">
                {{ $preview['label'] }}
                <span class="text-[10px] font-medium text-zinc-400">{{ $preview['summary']['granted'] }}/{{ $preview['summary']['total'] }}</span>
            </button>
        @endforeach
    </div>

    @foreach($rolePreviews as $roleKey => $preview)
        <div x-show="roleTab === '{{ $roleKey }}'" x-cloak class="space-y-4">

            {{-- Summary strip --}}
            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                @if($preview['can_access_admin'])
                    <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 font-semibold text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/40 dark:text-blue-300">
                        {{ $preview['summary']['admin_menus'] }} {{ __('admin menus') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 font-semibold text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
                        {{ __('No /admin access') }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1 rounded-full border border-zinc-200 bg-white px-2.5 py-1 font-semibold text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                    {{ $preview['summary']['portal_menus'] }} {{ __('portal menus') }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-full border border-zinc-200 bg-white px-2.5 py-1 font-semibold text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                    {{ $preview['summary']['granted'] }}/{{ $preview['summary']['total'] }} {{ __('permissions granted') }}
                </span>
                @if($preview['summary']['group_grants'] > 0)
                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300">
                        +{{ $preview['summary']['group_grants'] }} {{ __('by this group') }}
                    </span>
                @endif
                @if($preview['summary']['group_denies'] > 0)
                    <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 font-semibold text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
                        −{{ $preview['summary']['group_denies'] }} {{ __('by this group') }}
                    </span>
                @endif
            </div>

            @unless($preview['can_access_admin'])
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-[11px] text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                    {{ __('The User role cannot open /admin (middleware admin.access). Grants from this group only affect portal and self-service abilities; move the user to Technician or Administrator to use admin menus.') }}
                </div>
            @endunless

            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                {{-- Admin panel menus --}}
                @if($preview['can_access_admin'])
                    <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="mb-2.5 flex items-center justify-between">
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Admin Panel Menus') }}</h4>
                            <span class="text-[10px] font-medium text-zinc-400">{{ $preview['summary']['admin_menus'] }} {{ __('visible') }}</span>
                        </div>

                        <div class="space-y-2.5">
                            @foreach($preview['menu_sections'] as $sectionName => $items)
                                <div>
                                    <p class="mb-1 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ $sectionName }}</p>
                                    <div class="space-y-0.5">
                                        @foreach($items as $item)
                                            <div class="flex items-center justify-between gap-2 rounded-md px-1.5 py-1 {{ $item['visible'] ? 'bg-emerald-50/50 dark:bg-emerald-950/20' : 'bg-zinc-50/70 dark:bg-zinc-900/60' }}"
                                                 @if($item['note']) title="{{ $item['note'] }}" @endif>
                                                <div class="flex min-w-0 items-center gap-1.5">
                                                    @if($item['visible'])
                                                        <flux:icon name="check-circle" class="h-3.5 w-3.5 shrink-0 text-emerald-500" />
                                                    @else
                                                        <flux:icon name="lock-closed" class="h-3.5 w-3.5 shrink-0 text-zinc-400" />
                                                    @endif
                                                    <span class="truncate text-[11px] {{ $item['visible'] ? 'font-medium text-zinc-800 dark:text-zinc-200' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $item['label'] }}</span>
                                                </div>

                                                @if($item['visible'] && $item['badge'] === 'group_grant')
                                                    <span class="shrink-0 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">{{ __('by group') }}</span>
                                                @elseif(!$item['visible'] && $item['reason'] === 'role')
                                                    <span class="shrink-0 rounded-full bg-zinc-200 px-1.5 py-0.5 text-[9px] font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">{{ __('admin only') }}</span>
                                                @elseif(!$item['visible'] && $item['badge'] === 'group_deny')
                                                    <span class="shrink-0 rounded-full bg-rose-100 px-1.5 py-0.5 text-[9px] font-bold text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">{{ __('denied') }}</span>
                                                @elseif(!$item['visible'])
                                                    <span class="shrink-0 rounded-full bg-zinc-200 px-1.5 py-0.5 text-[9px] font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ __('no permission') }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Portal & self-service menus --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900 {{ $preview['can_access_admin'] ? '' : 'lg:col-span-2' }}">
                    <div class="mb-2.5 flex items-center justify-between">
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Portal & Self-Service') }}</h4>
                        <span class="text-[10px] font-medium text-zinc-400">{{ $preview['summary']['portal_menus'] }} {{ __('visible') }}</span>
                    </div>

                    <div class="space-y-0.5">
                        @foreach($preview['portal_menus'] as $item)
                            <div class="flex items-center justify-between gap-2 rounded-md px-1.5 py-1 {{ $item['visible'] ? 'bg-emerald-50/50 dark:bg-emerald-950/20' : 'bg-zinc-50/70 dark:bg-zinc-900/60' }}">
                                <div class="flex min-w-0 items-center gap-1.5">
                                    @if($item['visible'])
                                        <flux:icon name="check-circle" class="h-3.5 w-3.5 shrink-0 text-emerald-500" />
                                    @else
                                        <flux:icon name="lock-closed" class="h-3.5 w-3.5 shrink-0 text-zinc-400" />
                                    @endif
                                    <span class="truncate text-[11px] {{ $item['visible'] ? 'font-medium text-zinc-800 dark:text-zinc-200' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $item['label'] }}</span>
                                </div>

                                @if($item['visible'] && $item['badge'] === 'group_grant')
                                    <span class="shrink-0 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[9px] font-bold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">{{ __('by group') }}</span>
                                @elseif(!$item['visible'] && $item['badge'] === 'group_deny')
                                    <span class="shrink-0 rounded-full bg-rose-100 px-1.5 py-0.5 text-[9px] font-bold text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">{{ __('denied') }}</span>
                                @elseif(!$item['visible'])
                                    <span class="shrink-0 rounded-full bg-zinc-200 px-1.5 py-0.5 text-[9px] font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ __('no permission') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Effective capabilities --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-3.5 dark:border-zinc-800 dark:bg-zinc-900">
                <div class="mb-2.5 flex flex-wrap items-center justify-between gap-2">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Effective Capabilities') }}</h4>
                    <p class="text-[10px] text-zinc-400">{{ __('Hover a chip to see where the permission comes from.') }}</p>
                </div>

                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                    @foreach($preview['capabilities'] as $sectionName => $rows)
                        @php
                            $sectionGranted = collect($rows)->where('allowed', true)->count();
                        @endphp
                        <div class="rounded-lg border border-zinc-200 bg-zinc-50/60 p-2.5 dark:border-zinc-800 dark:bg-zinc-900/60">
                            <div class="mb-1.5 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">{{ $sectionName }}</span>
                                <span class="text-[10px] font-semibold {{ $sectionGranted === count($rows) ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400' }}">{{ $sectionGranted }}/{{ count($rows) }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($rows as $row)
                                    <span class="{{ $capabilityChip($row) }}" title="{{ $sourceLabel($row['source']) }}">{{ $row['label'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    {{-- Coverage notes --}}
    <div class="rounded-xl border border-zinc-200 bg-zinc-50/80 p-3.5 text-[11px] leading-relaxed text-zinc-600 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400">
        <p class="mb-1 font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Menus not controlled by this matrix') }}</p>
        <ul class="list-inside list-disc space-y-0.5">
            <li>{{ __('The /admin area is restricted to Administrator and Technician roles. User-role grants only affect portal abilities.') }}</li>
            <li>{{ __('Role-only menus:') }} {{ implode(', ', $roleOnlyMenus) }}.</li>
            <li>{{ __('Status Labels and Departments are rendered only for Administrators, even though their routes check View Categories and View Departments.') }}</li>
            <li>{{ __('Bulk Import appears when any create/manage permission for assets, users, locations, or inventory is granted.') }}</li>
        </ul>
    </div>
</flux:card>

{{-- Assigned Members Preview (when editing) --}}
@if(isset($group) && $group->exists)
    <flux:card class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="lg">{{ __('Assigned Users') }}</flux:heading>
                <flux:subheading>{{ $group->users()->count() }} {{ Str::plural('user', $group->users()->count()) }} currently assigned to this group</flux:subheading>
            </div>
        </div>

        @if($group->users()->count() > 0)
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3">
                @foreach($group->users()->take(12)->get() as $u)
                    <a href="{{ route('users.show', $u) }}" wire:navigate class="flex items-center gap-2.5 rounded-lg border border-zinc-200 p-2.5 transition hover:border-[var(--color-accent)] hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:bg-zinc-800">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-zinc-200 text-xs font-bold text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200">
                            {{ $u->initials() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-zinc-900 dark:text-white">{{ $u->name }}</p>
                            <p class="truncate text-[10px] text-zinc-500 dark:text-zinc-400">{{ $u->email }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            @if($group->users()->count() > 12)
                <p class="text-xs text-zinc-500 dark:text-zinc-400">...and {{ $group->users()->count() - 12 }} more members.</p>
            @endif
        @else
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('No users have been assigned to this group yet. You can assign users from the User Create / Edit page.') }}</p>
        @endif
    </flux:card>
@endif
