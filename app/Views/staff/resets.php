<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$requests = $requests ?? [];
?>
<header class="page-heading">
    <div>
        <h1>Password reset tickets</h1>
        <p class="muted">Verify identity before completing a reset.</p>
    </div>
</header>
<?= view('components/filter_bar', [
    'tableId' => 'reset-tickets-table',
    'ariaLabel' => 'Password reset ticket filters',
    'summary' => 'Matching tickets',
    'count' => count($requests),
    'filters' => [
        ['label' => 'Channel', 'column' => 2, 'options' => [['value' => '', 'label' => 'All channels'], ['value' => 'online', 'label' => 'Online'], ['value' => 'walk-in', 'label' => 'Walk-in'], ['value' => 'email', 'label' => 'Email'], ['value' => 'phone', 'label' => 'Phone']]],
        ['label' => 'Status', 'column' => 3, 'options' => array_merge([['value' => '', 'label' => 'All statuses']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], ['verified', 'reset', 'closed', 'rejected']))],
    ],
]) ?>
<section class="table-workspace">
    <?= view('components/table_toolbar', ['tableId' => 'reset-tickets-table', 'title' => 'Reset requests', 'count' => count($requests), 'searchPlaceholder' => 'Search reset tickets']) ?>
    <div class="table-wrap">
        <table id="reset-tickets-table" class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Requester</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody><?php foreach ($requests as $item): ?><tr>
                        <td><?= esc($item['ref_no']) ?></td>
                        <td><?= esc($item['requester_name']) ?><span class="row-detail"><?= esc($item['student_no']) ?></span></td>
                        <td><?= esc($item['channel']) ?></td>
                        <td><span class="chip"><?= esc($item['status']) ?></span></td>
                        <td>
                            <div class="row-actions"><button type="button" class="button small outline" data-modal-open="reset-edit-<?= $item['id'] ?>">Edit</button><button type="button" class="button small outline" data-modal-open="reset-status-<?= $item['id'] ?>">Status</button><?php if (staff_can('admin')): ?><form method="post" action="<?= site_url('staff/resets/' . $item['id'] . '/delete') ?>" data-confirm="Archive this reset ticket? Its audit history will be retained."><?= csrf_field() ?><button class="button small danger">Delete</button></form><?php endif ?></div>
                        </td>
                    </tr><?php endforeach ?><?php if ($requests === []): ?><tr class="empty-row">
                        <td colspan="5">No password reset requests found.</td>
                    </tr><?php endif ?></tbody>
        </table>
    </div>
</section>
<?php foreach ($requests as $item): ?><dialog id="reset-edit-<?= $item['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Edit reset ticket</h2><button type="button" class="modal-close" data-modal-close>×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/resets/' . $item['id'] . '/edit') ?>"><?= csrf_field() ?><label>Student number<input name="student_no" value="<?= esc($item['student_no']) ?>" required></label><label>Requester<input name="requester_name" value="<?= esc($item['requester_name']) ?>" required></label><label>Contact<input name="contact" value="<?= esc($item['contact']) ?>" required></label><label>Channel<select name="channel"><?php foreach (['online' => 'Online', 'walk-in' => 'Walk-in', 'email' => 'Email', 'phone' => 'Phone'] as $value => $label): ?><option value="<?= $value ?>" <?= $item['channel'] === $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach ?></select></label>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Save changes</button></div>
            </form>
        </div>
    </dialog>
    <dialog id="reset-status-<?= $item['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Update reset status</h2><button type="button" class="modal-close" data-modal-close>×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/resets/' . $item['id'] . '/status') ?>"><?= csrf_field() ?><label class="full">Next status<select name="status">
                        <option>verified</option>
                        <option>reset</option>
                        <option>closed</option>
                        <option>rejected</option>
                    </select></label>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Update status</button></div>
            </form>
        </div>
    </dialog><?php endforeach ?>
<?= $this->endSection() ?>