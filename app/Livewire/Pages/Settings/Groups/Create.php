<?php

namespace App\Livewire\Pages\Settings\Groups;

use App\Enums\UserPermission;
use App\Models\PermissionGroup;
use App\Support\RoleAccessPreview;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:255|unique:permission_groups,name')]
    public string $name = '';

    #[Validate('nullable|string|max:500')]
    public ?string $description = null;

    /**
     * Permission values: '1' = grant, '-1' = deny, '0' = none
     *
     * @var array<string, string>
     */
    public array $permissions = [];

    public function mount(): void
    {
        $this->authorize('manage_settings');

        // Initialise all matrix permissions to '0'
        foreach (UserPermission::matrixGrouped() as $items) {
            foreach ($items as $item) {
                $this->permissions[$item['value']] = '0';
            }
        }
    }

    public function grantAll(): void
    {
        foreach (UserPermission::matrixGrouped() as $items) {
            foreach ($items as $item) {
                $this->permissions[$item['value']] = '1';
            }
        }
    }

    public function clearAll(): void
    {
        foreach (UserPermission::matrixGrouped() as $items) {
            foreach ($items as $item) {
                $this->permissions[$item['value']] = '0';
            }
        }
    }

    public function grantSection(string $section): void
    {
        $grouped = UserPermission::matrixGrouped();
        if (isset($grouped[$section])) {
            foreach ($grouped[$section] as $item) {
                $this->permissions[$item['value']] = '1';
            }
        }
    }

    public function clearSection(string $section): void
    {
        $grouped = UserPermission::matrixGrouped();
        if (isset($grouped[$section])) {
            foreach ($grouped[$section] as $item) {
                $this->permissions[$item['value']] = '0';
            }
        }
    }

    public function save()
    {
        $this->authorize('manage_settings');

        $validated = $this->validate();

        // Build permissions blob: only non-zero values (1 = grant, -1 = deny)
        $permissionsBlob = collect($this->permissions)
            ->filter(fn ($v) => (int) $v !== 0)
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $validated['permissions'] = empty($permissionsBlob) ? null : $permissionsBlob;

        $group = DB::transaction(function () use ($validated) {
            $group = PermissionGroup::create($validated);

            activity('permission_group')
                ->performedOn($group)
                ->causedBy(auth()->user())
                ->event('group_created')
                ->log("Created permission group: {$group->name}");

            return $group;
        });

        session()->flash('success', "Permission group '{$group->name}' created successfully.");

        return redirect()->route('settings.groups.index');
    }

    public function render(): View
    {
        return view('livewire.pages.settings.groups.create', [
            'permissionSections' => UserPermission::matrixGrouped(),
            'rolePreviews' => RoleAccessPreview::build($this->permissions),
        ])->layout('layouts.app', ['title' => 'Create Permission Group']);
    }
}
