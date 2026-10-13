<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<header class="page-heading">
    <div>
        <h1>Password reset tickets</h1>
        <p class="muted">Verify identity before completing a reset.</p>
    </div>
</header>
<section class="panel filter-bar" aria-label="Password reset ticket filters">
    <label>Channel<select data-table-filter="reset-tickets-table" data-column="2">
            <option value="">All channels</option>
            <option value="online">Online</option>
            <option value="walk-in">Walk-in</option>
            <option value="email">Email</option>
            <option value="phone">Phone</option>
        </select></label>
    <label>Status<select data-table-filter="reset-tickets-table" data-column="3">
            <option value="">All statuses</option>
            <option>verified</option>
            <option>reset</option>
            <option>closed</option>
            <option>rejected</option>
        </select></label>
    <div class="filter-summary"><span class="filter-summary-label">Matching tickets</span><strong data-filter-count="reset-tickets-table"><?= count($requests) ?> shown</strong></div>
    <div class="filter-actions"><button type="button" class="button" data-apply-filters="reset-tickets-table">Filter</button><button type="button" class="button secondary" data-reset-filters="reset-tickets-table">Reset</button></div>
</section>
<section class="table-workspace">
    <header class="table-toolbar">
        <div class="table-title">
            <h2>Reset requests</h2><span class="table-count" data-table-count="reset-tickets-table"><?= count($requests) ?> shown</span>
        </div>
        <div class="table-tools"><input class="table-search" type="search" data-table-search="reset-tickets-table" placeholder="Search reset tickets"></div>
    </header>
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