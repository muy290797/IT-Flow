<?php

namespace App\Livewire\Pages\Settings\Groups;

use App\Enums\UserPermission;
use App\Models\PermissionGroup;
use App\Support\RoleAccessPreview;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public PermissionGroup $group;

    public string $name = '';

    public ?string $description = null;

    /**
     * Permission values: '1' = grant, '-1' = deny, '0' = none
     *
     * @var array<string, string>
     */
    public array $permissions = [];

    public function mount(PermissionGroup $group): void
    {
        $this->authorize('manage_settings');
        $this->group = $group;
        $this->name = $group->name;
        $this->description = $group->description;

        // Seed permissions from group's stored JSON blob
        $blob = $group->permissions ?? [];
        foreach (UserPermission::matrixGrouped() as $items) {
            foreach ($items as $item) {
                $raw = $blob[$item['value']] ?? 0;
                if ($raw === true) {
                    $raw = 1;
                }
                $this->permissions[$item['value']] = (string) $raw;
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

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permission_groups')->ignore($this->group->id)],
            'description' => 'nullable|string|max:500',
        ]);

        $permissionsBlob = collect($this->permissions)
            ->filter(fn ($v) => (int) $v !== 0)
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $validated['permissions'] = empty($permissionsBlob) ? null : $permissionsBlob;

        DB::transaction(function () use ($validated) {
            $this->group->update($validated);

            activity('permission_group')
                ->performedOn($this->group)
                ->causedBy(auth()->user())
                ->event('group_updated')
                ->log("Updated permission group: {$this->group->name}");
        });

        session()->flash('success', "Permission group '{$this->group->name}' updated successfully.");

        return redirect()->route('settings.groups.index');
    }

    public function delete()
    {
        $this->authorize('manage_settings');

        $name = $this->group->name;
        $this->group->users()->detach();
        $this->group->delete();

        activity('permission_group')
            ->causedBy(auth()->user())
            ->event('group_deleted')
            ->log("Deleted permission group: {$name}");

        session()->flash('success', "Permission group '{$name}' deleted successfully.");

        return redirect()->route('settings.groups.index');
    }

    public function render(): View
    {
        return view('livewire.pages.settings.groups.edit', [
            'permissionSections' => UserPermission::matrixGrouped(),
            'rolePreviews' => RoleAccessPreview::build($this->permissions),
        ])->layout('layouts.app', ['title' => 'Edit Permission Group']);
    }
}
