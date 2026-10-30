<?php

namespace App\Enums;

enum UserPermission: string
{
    // ── Superuser ────────────────────────────────────────────────────────────
    case Superuser = 'superuser';

    // ── Assets ───────────────────────────────────────────────────────────────
    case ViewAssets = 'view_assets';
    case CreateAssets = 'create_assets';
    case UpdateAssets = 'update_assets';
    case DeleteAssets = 'delete_assets';
    case CheckoutAssets = 'checkout_assets';
    case CheckinAssets = 'checkin_assets';
    case AuditAssets = 'audit_assets';
    case MaintenanceAssets = 'maintenance_assets';

    // ── Licenses ─────────────────────────────────────────────────────────────
    case ViewLicenses = 'view_licenses';
    case CreateLicenses = 'create_licenses';
    case UpdateLicenses = 'update_licenses';
    case DeleteLicenses = 'delete_licenses';
    case CheckoutLicenses = 'checkout_licenses';
    case ViewLicenseKeys = 'view_license_keys';

    // ── Accessories ──────────────────────────────────────────────────────────
    case ViewAccessories = 'view_accessories';
    case CreateAccessories = 'create_accessories';
    case UpdateAccessories = 'update_accessories';
    case DeleteAccessories = 'delete_accessories';
    case CheckoutAccessories = 'checkout_accessories';

    // ── Consumables ──────────────────────────────────────────────────────────
    case ViewConsumables = 'view_consumables';
    case CreateConsumables = 'create_consumables';
    case UpdateConsumables = 'update_consumables';
    case DeleteConsumables = 'delete_consumables';
    case CheckoutConsumables = 'checkout_consumables';

    // ── Components ───────────────────────────────────────────────────────────
    case ViewComponents = 'view_components';
    case CreateComponents = 'create_components';
    case UpdateComponents = 'update_components';
    case DeleteComponents = 'delete_components';
    case CheckoutComponents = 'checkout_components';

    // ── Users / People ───────────────────────────────────────────────────────
    case ViewUsers = 'view_users';
    case CreateUsers = 'create_users';
    case UpdateUsers = 'update_users';
    case DeleteUsers = 'delete_users';

    // ── Reports ──────────────────────────────────────────────────────────────
    case ViewReports = 'view_reports';
    case ExportReports = 'export_reports';

    // ── Settings ─────────────────────────────────────────────────────────────
    case ManageSettings = 'manage_settings';

    // ── Master Data (Categories, Departments, Locations, Manufacturers, etc.) ─
    case ViewCategories = 'view_categories';
    case ManageCategories = 'manage_categories';
    case ViewDepartments = 'view_departments';
    case ManageDepartments = 'manage_departments';
    case ViewLocations = 'view_locations';
    case ManageLocations = 'manage_locations';
    case ViewManufacturers = 'view_manufacturers';
    case ManageManufacturers = 'manage_manufacturers';
    case ViewSuppliers = 'view_suppliers';
    case ManageSuppliers = 'manage_suppliers';

    // ── Deprecated/legacy aliases kept for backwards compat ──────────────────
    case AssignAssets = 'assign_assets';
    case UseAssets = 'use_assets';
    case RequestAssets = 'request_assets';
    case ViewTickets = 'view_tickets';
    case CreateTickets = 'create_tickets';
    case ResolveTickets = 'resolve_tickets';

    // Ticketing permissions
    case ViewAllTickets = 'view_all_tickets';
    case AssignTickets = 'assign_tickets';
    case UpdateTickets = 'update_tickets';
    case CloseTickets = 'close_tickets';
    case ViewInternalNotes = 'view_internal_notes';
    case DeleteTickets = 'delete_tickets';
    case MergeTickets = 'merge_tickets';

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Human-readable label for this permission.
     */
    public function label(): string
    {
        return match ($this) {
            self::Superuser          => 'Superuser (Full Access)',
            self::ViewAssets         => 'View Assets',
            self::CreateAssets       => 'Create Assets',
            self::UpdateAssets       => 'Edit Assets',
            self::DeleteAssets       => 'Delete Assets',
            self::CheckoutAssets     => 'Checkout Assets',
            self::CheckinAssets      => 'Checkin Assets',
            self::AuditAssets        => 'Audit Assets',
            self::MaintenanceAssets  => 'Asset Maintenance',
            self::ViewLicenses       => 'View Licenses',
            self::CreateLicenses     => 'Create Licenses',
            self::UpdateLicenses     => 'Edit Licenses',
            self::DeleteLicenses     => 'Delete Licenses',
            self::CheckoutLicenses   => 'Checkout Licenses',
            self::ViewLicenseKeys    => 'View License Keys',
            self::ViewAccessories    => 'View Accessories',
            self::CreateAccessories  => 'Create Accessories',
            self::UpdateAccessories  => 'Edit Accessories',
            self::DeleteAccessories  => 'Delete Accessories',
            self::CheckoutAccessories => 'Checkout Accessories',
            self::ViewConsumables    => 'View Consumables',
            self::CreateConsumables  => 'Create Consumables',
            self::UpdateConsumables  => 'Edit Consumables',
            self::DeleteConsumables  => 'Delete Consumables',
            self::CheckoutConsumables => 'Checkout Consumables',
            self::ViewComponents     => 'View Components',
            self::CreateComponents   => 'Create Components',
            self::UpdateComponents   => 'Edit Components',
            self::DeleteComponents   => 'Delete Components',
            self::CheckoutComponents => 'Checkout Components',
            self::ViewUsers          => 'View Users',
            self::CreateUsers        => 'Create Users',
            self::UpdateUsers        => 'Edit Users',
            self::DeleteUsers        => 'Delete Users',
            self::ViewReports        => 'View Reports',
            self::ExportReports      => 'Export Reports',
            self::ManageSettings     => 'Manage Settings',
            self::ViewCategories     => 'View Categories',
            self::ManageCategories   => 'Manage Categories',
            self::ViewDepartments    => 'View Departments',
            self::ManageDepartments  => 'Manage Departments',
            self::ViewLocations      => 'View Locations',
            self::ManageLocations    => 'Manage Locations',
            self::ViewManufacturers  => 'View Manufacturers',
            self::ManageManufacturers => 'Manage Manufacturers',
            self::ViewSuppliers      => 'View Suppliers',
            self::ManageSuppliers    => 'Manage Suppliers',
            self::AssignAssets       => 'Assign Assets',
            self::UseAssets          => 'Use Assets',
            self::RequestAssets      => 'Request Assets',
            self::ViewTickets        => 'Legacy: View Tickets',
            self::CreateTickets      => 'Create Tickets',
            self::ResolveTickets     => 'Legacy: Resolve Tickets',
            self::ViewAllTickets     => 'View All Tickets',
            self::AssignTickets      => 'Assign Tickets',
            self::UpdateTickets      => 'Update Tickets',
            self::CloseTickets       => 'Close Tickets',
            self::ViewInternalNotes  => 'View and Add Internal Notes',
            self::DeleteTickets      => 'Delete Tickets',
            self::MergeTickets      => 'Merge Tickets',
        };
    }

    /**
     * Permission section/group name for UI grouping.
     */
    public function group(): string
    {
        return match ($this) {
            self::Superuser => 'Admin',
            self::ViewAssets, self::CreateAssets, self::UpdateAssets,
            self::DeleteAssets, self::CheckoutAssets, self::CheckinAssets,
            self::AuditAssets, self::MaintenanceAssets,
            self::AssignAssets, self::UseAssets, self::RequestAssets => 'Assets',

            self::ViewLicenses, self::CreateLicenses, self::UpdateLicenses,
            self::DeleteLicenses, self::CheckoutLicenses, self::ViewLicenseKeys => 'Licenses',

            self::ViewAccessories, self::CreateAccessories, self::UpdateAccessories,
            self::DeleteAccessories, self::CheckoutAccessories => 'Accessories',

            self::ViewConsumables, self::CreateConsumables, self::UpdateConsumables,
            self::DeleteConsumables, self::CheckoutConsumables => 'Consumables',

            self::ViewComponents, self::CreateComponents, self::UpdateComponents,
            self::DeleteComponents, self::CheckoutComponents => 'Components',

            self::ViewUsers, self::CreateUsers, self::UpdateUsers, self::DeleteUsers => 'Users',

            self::ViewReports, self::ExportReports => 'Reports',

            self::ManageSettings => 'Settings',

            self::ViewCategories, self::ManageCategories => 'Categories',
            self::ViewDepartments, self::ManageDepartments => 'Departments',
            self::ViewLocations, self::ManageLocations => 'Locations',
            self::ViewManufacturers, self::ManageManufacturers => 'Manufacturers',
            self::ViewSuppliers, self::ManageSuppliers => 'Suppliers',

            self::ViewTickets, self::CreateTickets, self::ResolveTickets,
            self::ViewAllTickets, self::AssignTickets, self::UpdateTickets,
            self::CloseTickets, self::ViewInternalNotes, self::DeleteTickets,
            self::MergeTickets => 'Tickets',
        };
    }

    /**
     * Heroicon name for UI display.
     */
    public function icon(): string
    {
        return match ($this->group()) {
            'Admin'         => 'shield-exclamation',
            'Assets'        => 'computer-desktop',
            'Licenses'      => 'document-text',
            'Accessories'   => 'puzzle-piece',
            'Consumables'   => 'shopping-bag',
            'Components'    => 'cpu-chip',
            'Users'         => 'user-group',
            'Reports'       => 'chart-bar',
            'Settings'      => 'cog-6-tooth',
            'Categories'    => 'tag',
            'Departments'   => 'building-office',
            'Locations'     => 'map-pin',
            'Manufacturers' => 'building-storefront',
            'Suppliers'     => 'truck',
            'Tickets'       => 'ticket',
            default         => 'key',
        };
    }

    /**
     * All permissions grouped by section for use in the UI permission matrix.
     *
     * Returns: ['SectionName' => [['value' => ..., 'label' => ...], ...], ...]
     *
     * @return array<string, array<int, array{value: string, label: string}>>
     */
    public static function grouped(): array
    {
        $permissions = collect(self::cases());

        return $permissions
            ->groupBy(fn ($permission) => $permission->group())
            ->map(fn ($group) => $group->map(fn ($permission) => [
                'value' => $permission->value,
                'label' => $permission->label(),
            ]))
            ->toArray();
    }

    /**
     * Returns permissions available in the user and group matrices. Legacy
     * ticket aliases stay available to runtime permission resolution for
     * compatibility, but are omitted from the editable matrix. The Superuser
     * flag is managed separately on administrator accounts only, and UseAssets
     * has no runtime gate.
     *
     * @return array<string, array<int, array{value: string, label: string}>>
     */
    public static function matrixGrouped(): array
    {
        $exclude = [
            self::UseAssets,
            self::Superuser, self::ViewTickets, self::ResolveTickets,
        ];

        return collect(self::cases())
            ->reject(fn ($p) => in_array($p, $exclude, true))
            ->groupBy(fn ($p) => $p->group())
            ->map(fn ($group) => $group->map(fn ($p) => [
                'value' => $p->value,
                'label' => $p->label(),
                'icon'  => $p->icon(),
            ])->values()->toArray())
            ->toArray();
    }
}
