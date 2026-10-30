<?php

namespace App\Support;

use App\Enums\UserPermission;
use App\Enums\UserRole;

/**
 * Simulates, for a given permission group, the menus and capabilities each
 * role ends up with. Group grants override role defaults; group denies win
 * over both, mirroring User::hasPermission().
 */
class RoleAccessPreview
{
    /**
     * Menus rendered by the admin sidebar and mobile drawer, grouped by the
     * section heading shown in the navigation.
     */
    private const ADMIN_MENUS = [
        'Overview' => [
            ['label' => 'Dashboard', 'requires' => []],
        ],
        'Service Desk' => [
            ['label' => 'Ticket Queue', 'requires' => ['view_all_tickets']],
            ['label' => 'Knowledge Base', 'requires' => ['manage_settings']],
        ],
        'Assets' => [
            ['label' => 'All Assets', 'requires' => ['view_assets']],
            [
                'label' => 'Asset Models',
                'requires' => ['view_assets', 'view_categories'],
                'note' => 'Route also requires View Categories',
            ],
        ],
        'Inventory' => [
            ['label' => 'Accessories', 'requires' => ['view_accessories']],
            ['label' => 'Consumables', 'requires' => ['view_consumables']],
            ['label' => 'Components', 'requires' => ['view_components']],
            ['label' => 'Licenses', 'requires' => ['view_licenses']],
        ],
        'Operations' => [
            ['label' => 'Asset Requests', 'requires' => ['update_assets']],
            ['label' => 'Maintenance', 'requires' => ['maintenance_assets']],
            ['label' => 'Audit', 'requires' => ['audit_assets']],
            ['label' => 'Handover Form', 'requires' => ['assign_assets']],
            ['label' => 'Documents', 'requires' => ['export_reports']],
        ],
        'Reports' => [
            ['label' => 'Maintenance Report', 'requires' => ['view_reports']],
            ['label' => 'Activity Log', 'requires' => ['view_reports'], 'adminOnly' => true],
            ['label' => 'Document Report', 'requires' => ['view_reports'], 'adminOnly' => true],
        ],
        'Organization' => [
            ['label' => 'Categories', 'requires' => ['view_categories']],
            ['label' => 'Locations', 'requires' => ['view_locations']],
            ['label' => 'Suppliers', 'requires' => ['view_suppliers']],
            ['label' => 'Manufacturers', 'requires' => ['view_manufacturers']],
            ['label' => 'Status Labels', 'requires' => ['view_categories'], 'adminOnly' => true],
            ['label' => 'Departments', 'requires' => ['view_departments'], 'adminOnly' => true],
            ['label' => 'Companies', 'requires' => [], 'adminOnly' => true],
            ['label' => 'Depreciation', 'requires' => [], 'adminOnly' => true],
        ],
        'People' => [
            ['label' => 'People (Users)', 'requires' => ['view_users']],
        ],
        'Settings' => [
            ['label' => 'General Settings', 'requires' => ['manage_settings'], 'adminOnly' => true],
            ['label' => 'Permission Groups', 'requires' => ['manage_settings'], 'adminOnly' => true],
            ['label' => 'Custom Fields', 'requires' => ['manage_settings'], 'adminOnly' => true],
            ['label' => 'Form Templates', 'requires' => ['manage_settings'], 'adminOnly' => true],
            [
                'label' => 'Bulk Import',
                'requiresAny' => [
                    'create_assets', 'create_users', 'manage_locations',
                    'create_accessories', 'create_consumables',
                    'create_components', 'create_licenses',
                ],
            ],
        ],
    ];

    /**
     * Portal and self-service navigation available to every authenticated user.
     */
    private const PORTAL_MENUS = [
        ['label' => 'Home', 'requires' => []],
        ['label' => 'Knowledge Base', 'requires' => []],
        ['label' => 'My Portal', 'requires' => []],
        ['label' => 'My Items', 'requires' => []],
        ['label' => 'My Requests', 'requires' => []],
        ['label' => 'Request Asset', 'requires' => ['request_assets']],
        ['label' => 'Create Ticket', 'requires' => ['create_tickets']],
        ['label' => 'My Profile', 'requires' => []],
        ['label' => 'My Signatures', 'requires' => []],
    ];

    /**
     * Build the preview payload for every role.
     *
     * @param  array<string, int|string>  $groupPermissions
     * @return array<string, array<string, mixed>>
     */
    public static function build(array $groupPermissions): array
    {
        $normalised = [];
        foreach ($groupPermissions as $key => $value) {
            $normalised[$key] = (int) $value;
        }

        $previews = [];

        foreach (UserRole::cases() as $role) {
            $permissionState = self::permissionStates($role, $normalised);

            $menuSections = [];
            $visibleAdminMenus = 0;

            foreach (self::ADMIN_MENUS as $section => $items) {
                $evaluated = [];
                foreach ($items as $item) {
                    $state = self::evaluateMenu($item, $role, $permissionState);
                    if ($state['visible']) {
                        $visibleAdminMenus++;
                    }
                    $evaluated[] = $item + $state;
                }
                $menuSections[$section] = $evaluated;
            }

            $portalMenus = [];
            $visiblePortalMenus = 0;
            foreach (self::PORTAL_MENUS as $item) {
                $state = self::evaluateMenu($item, $role, $permissionState);
                if ($state['visible']) {
                    $visiblePortalMenus++;
                }
                $portalMenus[] = $item + $state;
            }

            $capabilities = [];
            $granted = 0;
            $total = 0;
            foreach (UserPermission::matrixGrouped() as $section => $items) {
                $rows = [];
                foreach ($items as $item) {
                    $state = $permissionState[$item['value']];
                    $rows[] = [
                        'label' => $item['label'],
                        'allowed' => $state['allowed'],
                        'source' => $state['source'],
                    ];
                    $total++;
                    if ($state['allowed']) {
                        $granted++;
                    }
                }
                $capabilities[$section] = $rows;
            }

            $previews[$role->value] = [
                'label' => $role->label(),
                'description' => $role->description(),
                'badge_class' => $role->badgeClass(),
                'can_access_admin' => $role->canAccessAdmin(),
                'is_admin_role' => $role === UserRole::Admin,
                'menu_sections' => $menuSections,
                'portal_menus' => $portalMenus,
                'capabilities' => $capabilities,
                'summary' => [
                    'admin_menus' => $visibleAdminMenus,
                    'portal_menus' => $visiblePortalMenus,
                    'granted' => $granted,
                    'total' => $total,
                    'group_grants' => count(array_filter($normalised, fn (int $v) => $v === 1)),
                    'group_denies' => count(array_filter($normalised, fn (int $v) => $v === -1)),
                ],
            ];
        }

        return $previews;
    }

    /**
     * @param  array<string, int>  $groupPermissions
     * @return array<string, array{allowed: bool, source: string}>
     */
    private static function permissionStates(UserRole $role, array $groupPermissions): array
    {
        $states = [];

        foreach (UserPermission::matrixGrouped() as $items) {
            foreach ($items as $item) {
                $permission = UserPermission::from($item['value']);
                $states[$permission->value] = self::resolve($permission, $role, $groupPermissions);
            }
        }

        return $states;
    }

    /**
     * @param  array<string, int>  $groupPermissions
     * @return array{allowed: bool, source: string}
     */
    private static function resolve(UserPermission $permission, UserRole $role, array $groupPermissions): array
    {
        $raw = self::groupValue($permission, $groupPermissions);

        if ($raw === 1) {
            return ['allowed' => true, 'source' => 'group_grant'];
        }

        if ($raw === -1) {
            return ['allowed' => false, 'source' => 'group_deny'];
        }

        $allowed = $role->hasPermission($permission);

        return ['allowed' => $allowed, 'source' => $allowed ? 'role' : 'none'];
    }

    /**
     * Read a permission value from the editor map, honouring stored legacy keys
     * the same way PermissionGroup::permissionValue() does.
     *
     * @param  array<string, int>  $groupPermissions
     */
    private static function groupValue(UserPermission $permission, array $groupPermissions): int
    {
        if (array_key_exists($permission->value, $groupPermissions)) {
            return (int) $groupPermissions[$permission->value];
        }

        $legacy = match ($permission) {
            UserPermission::ViewAllTickets => UserPermission::ViewTickets,
            UserPermission::AssignTickets,
            UserPermission::UpdateTickets,
            UserPermission::CloseTickets,
            UserPermission::ViewInternalNotes => UserPermission::ResolveTickets,
            default => null,
        };

        return $legacy === null ? 0 : (int) ($groupPermissions[$legacy->value] ?? 0);
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, array{allowed: bool, source: string}>  $permissionState
     * @return array{visible: bool, reason: ?string, badge: ?string, note: ?string}
     */
    private static function evaluateMenu(array $item, UserRole $role, array $permissionState): array
    {
        $note = $item['note'] ?? null;

        if (($item['adminOnly'] ?? false) && $role !== UserRole::Admin) {
            return ['visible' => false, 'reason' => 'role', 'badge' => null, 'note' => $note];
        }

        foreach ($item['requires'] ?? [] as $key) {
            $state = $permissionState[$key] ?? ['allowed' => false, 'source' => 'none'];

            if (! $state['allowed']) {
                return [
                    'visible' => false,
                    'reason' => 'permission',
                    'badge' => self::badge($state['source']),
                    'note' => $note,
                ];
            }
        }

        $any = $item['requiresAny'] ?? [];
        if ($any !== [] && ! collect($any)->contains(fn (string $key) => $permissionState[$key]['allowed'] ?? false)) {
            return ['visible' => false, 'reason' => 'permission', 'badge' => null, 'note' => $note];
        }

        $badge = null;
        foreach ($item['requires'] ?? [] as $key) {
            $source = $permissionState[$key]['source'] ?? null;
            if ($source === 'group_grant' || $source === 'group_deny') {
                $badge = $source;
                break;
            }
        }

        return ['visible' => true, 'reason' => null, 'badge' => $badge, 'note' => $note];
    }

    private static function badge(string $source): ?string
    {
        return match ($source) {
            'group_grant', 'group_deny' => $source,
            default => null,
        };
    }
}
