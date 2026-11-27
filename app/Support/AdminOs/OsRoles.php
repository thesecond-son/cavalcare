<?php

namespace App\Support\AdminOs;

/**
 * Caval OS staff roles and what each may do (backend spec, section 3.3).
 *
 * Super admin and the legacy "admin" role pass every check (see the
 * Gate::before in AppServiceProvider). Every other role gets only the
 * permissions listed here. Run AdminRolesSeeder after changing this file.
 */
final class OsRoles
{
    public const SUPER_ADMIN   = 'super_admin';
    public const LEGACY_ADMIN  = 'admin';        // existing admins: full access, as before
    public const COORDINATOR   = 'coordinator';
    public const CLINICAL_LEAD = 'clinical_lead';
    public const COMPLIANCE    = 'compliance';
    public const FINANCE       = 'finance';
    public const SUPPORT       = 'support';

    public const LABELS = [
        self::SUPER_ADMIN   => 'Super admin',
        self::LEGACY_ADMIN  => 'Admin',
        self::COORDINATOR   => 'Coordinator',
        self::CLINICAL_LEAD => 'Clinical lead',
        self::COMPLIANCE    => 'Compliance officer',
        self::FINANCE       => 'Finance',
        self::SUPPORT       => 'Support',
    ];

    /** Permission name => plain description (shown in Settings). */
    public const PERMISSIONS = [
        'bookings.view'      => 'See website and app bookings',
        'bookings.manage'    => 'Cancel, reschedule and send app codes for bookings',
        'clients.view'       => 'See families, patients and their records',
        'clients.manage'     => 'Edit patients, care plans and normal ranges',
        'caregivers.view'    => 'See caregivers and their profiles',
        'caregivers.verify'  => 'Verify credentials and suspend caregivers',
        'schedule.manage'    => 'Assign shifts and send offers',
        'concerns.manage'    => 'Acknowledge and resolve concerns and check-in disputes',
        'catalogue.manage'   => 'Edit care types, plans and prices',
        'clinical.manage'    => 'Set default normal ranges and care-plan templates',
        'finance.view'       => 'See payments, refunds and payouts',
        'finance.manage'     => 'Approve refunds, payouts and revenue splits',
        'messages.manage'    => 'Read and reply to family and support threads',
        'analytics.view'     => 'See reports and export CSVs',
        'audit.view'         => 'Read the audit log',
        'settings.manage'    => 'Change system settings',
        'admins.manage'      => 'Add admins and change their roles',
    ];

    /** Role => permissions. Super admin / legacy admin are not listed: they get everything. */
    public const GRANTS = [
        self::COORDINATOR => [
            'bookings.view', 'bookings.manage', 'clients.view', 'clients.manage',
            'caregivers.view', 'schedule.manage', 'concerns.manage', 'messages.manage',
        ],
        self::CLINICAL_LEAD => [
            'bookings.view', 'clients.view', 'clients.manage', 'caregivers.view',
            'clinical.manage', 'catalogue.manage', 'concerns.manage',
        ],
        self::COMPLIANCE => [
            'caregivers.view', 'caregivers.verify', 'audit.view', 'bookings.view',
        ],
        self::FINANCE => [
            'bookings.view', 'finance.view', 'finance.manage', 'catalogue.manage', 'analytics.view',
        ],
        self::SUPPORT => [
            'bookings.view', 'clients.view', 'caregivers.view', 'messages.manage',
        ],
    ];

    /** Roles that pass every permission check. */
    public static function fullAccess(): array
    {
        return [self::SUPER_ADMIN, self::LEGACY_ADMIN];
    }

    /** Every role allowed into Caval OS. */
    public static function all(): array
    {
        return array_keys(self::LABELS);
    }
}
