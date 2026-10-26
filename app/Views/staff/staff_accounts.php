<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$groups = $groups ?? [];
$campuses = $campuses ?? [];
$users = $users ?? [];
?>
<header class="page-heading">
    <div>
        <h1>Staff accounts</h1>
        <p class="muted">Manage sign-in access, roles, and campus assignments.</p>
    </div>
    <button type="button" class="button" data-modal-open="create-account">Create staff account</button>
</header>
<?= view('components/filter_bar', [
    'tableId' => 'staff-users-table',
    'ariaLabel' => 'Staff account filters',
    'summary' => 'Matching accounts',
    'count' => count($users),
    'filters' => [
        ['label' => 'Role', 'column' => 2, 'options' => array_merge([['value' => '', 'label' => 'All roles']], array_map(static fn ($key, $group) => ['value' => $key, 'label' => $group['title']], array_keys($groups), $groups))],
        ['label' => 'Campus', 'column' => 3, 'options' => array_merge([['value' => '', 'label' => 'All campuses']], array_map(static fn ($campus) => ['value' => $campus['id'], 'label' => $campus['name']], $campuses))],
        ['label' => 'Status', 'column' => 4, 'options' => [['value' => '', 'label' => 'All statuses'], ['value' => '1', 'label' => 'Active'], ['value' => '0', 'label' => 'Inactive']]],
    ],
]) ?>
<dialog id="create-account" class="modal" data-open-on-error>
    <header class="modal-header">
        <h2>Create staff account</h2><button type="button" class="modal-close" data-modal-close>×</button>
    </header>
    <div class="modal-body">
        <form method="post" class="grid" action="<?= site_url('staff/users') ?>"><?= csrf_field() ?><label>Username<input name="username" required></label><label>Email address<input type="email" name="email" required></label><label>Password<input type="password" name="password" required></label><label>Confirm password<input type="password" name="password_confirm" required></label><label>Role<select name="group"><?php foreach ($groups as $key => $group): ?><option value="<?= esc($key) ?>"><?= esc($group['title']) ?></option><?php endforeach ?></select></label><label>Campus<select name="campus_id">
                    <option value="">No campus restriction</option><?php foreach ($campuses as $campus): ?><option value="<?= $campus['id'] ?>"><?= esc($campus['name']) ?></option><?php endforeach ?>
                </select></label>
            <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Create account</button></div>
        </form>
    </div>
</dialog>
<section class="table-workspace">
    <?= view('components/table_toolbar', ['tableId' => 'staff-users-table', 'title' => 'User accounts', 'count' => count($users), 'searchPlaceholder' => 'Search staff accounts']) ?>
    <div class="table-wrap">
        <table id="staff-users-table" class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Campus</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody><?php foreach ($users as $user): ?><tr>
                        <td><?= esc($user['username']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['groups'] ?: 'Unassigned') ?></td>
                        <td><?= esc($user['campus'] ?: 'All campuses') ?></td>
                        <td><span class="chip"><?= $user['active'] ? 'Active' : 'Inactive' ?></span></td>
                        <td>
                            <div class="row-actions"><button type="button" class="button small outline" data-modal-open="user-edit-<?= $user['id'] ?>">Edit</button><?php if ((int)$user['id'] !== (int)auth()->id()): ?><form method="post" action="<?= site_url('staff/users/' . $user['id'] . '/delete') ?>" data-confirm="Archive this staff account? The user will no longer be able to sign in."><?= csrf_field() ?><button class="button small danger">Delete</button></form><?php endif ?></div>
                        </td>
                    </tr><?php endforeach ?><?php if ($users === []): ?><tr class="empty-row">
                        <td colspan="6">No staff accounts found.</td>
                    </tr><?php endif ?></tbody>
        </table>
    </div>
</section>
<?php foreach ($users as $user): ?><dialog id="user-edit-<?= $user['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Edit staff account</h2><button type="button" class="modal-close" data-modal-close>×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/users/' . $user['id'] . '/edit') ?>"><?= csrf_field() ?><label>Username<input name="username" value="<?= esc($user['username']) ?>" required></label><label>Email<input type="email" name="email" value="<?= esc($user['email']) ?>" required></label><label>Password <span class="muted">Leave blank to keep current</span><input type="password" name="password"></label><label>Confirm password<input type="password" name="password_confirm"></label><label>Role<select name="group"><?php foreach ($groups as $key => $group): ?><option value="<?= esc($key) ?>" <?= in_array($key, array_map('trim', explode(',', $user['groups'] ?? '')), true) ? 'selected' : '' ?>><?= esc($group['title']) ?></option><?php endforeach ?></select></label><label>Campus<select name="campus_id">
                        <option value="">No campus restriction</option><?php foreach ($campuses as $campus): ?><option value="<?= $campus['id'] ?>" <?= (int)($user['campus_id'] ?? 0) === (int)$campus['id'] ? 'selected' : '' ?>><?= esc($campus['name']) ?></option><?php endforeach ?>
                    </select></label><label>Status<select name="active">
                        <option value="1" <?= $user['active'] ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ! $user['active'] ? 'selected' : '' ?>>Inactive</option>
                    </select></label>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Save changes</button></div>
            </form>
        </div>
    </dialog><?php endforeach ?>
<?= $this->endSection() ?>