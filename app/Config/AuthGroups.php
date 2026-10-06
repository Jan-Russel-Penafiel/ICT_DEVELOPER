<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    public string $defaultGroup = 'registrar';

    public array $groups = [
        'admin' => ['title' => 'Administrator', 'description' => 'System configuration and all queues.'],
        'ict' => ['title' => 'ICT Staff', 'description' => 'ID production, accounts, resets and repairs.'],
        'registrar' => ['title' => 'Registrar', 'description' => 'Campus ID release work.'],
        'cashier' => ['title' => 'Cashier', 'description' => 'Campus replacement payments.'],
    ];

    public array $permissions = [
        'links.manage' => 'Manage registration links', 'ids.manage' => 'Manage ID requests',
        'accounts.manage' => 'Manage account batches', 'resets.manage' => 'Manage password resets',
        'repairs.manage' => 'Manage hardware repairs', 'settings.manage' => 'Manage system settings',
    ];

    public array $matrix = [
        'admin' => ['*'], 'ict' => ['ids.manage', 'accounts.manage', 'resets.manage', 'repairs.manage'],
        'registrar' => ['ids.manage'], 'cashier' => ['ids.manage'],
    ];
}
