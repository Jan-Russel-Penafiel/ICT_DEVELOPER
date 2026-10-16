<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$requests = $requests ?? [];
$equipment = $equipment ?? [];
?>
<header class="page-heading">
    <div>
        <h1>Hardware repair queue</h1>
        <p class="muted">Track assessment, external service, and equipment release.</p>
    </div>
</header>
<section class="panel filter-bar" aria-label="Hardware repair queue filters">
    <label>Status<select data-table-filter="repair-requests-table" data-column="3">
            <option value="">All statuses</option>
            <option>assessed</option>
            <option>sent</option>
            <option>returned</option>
            <option>released</option>
            <option>rejected</option>
        </select></label>
    <label>Office<select data-table-filter="repair-requests-table" data-column="2">
            <option value="">All offices</option><?php foreach ($equipment as $item): ?><option value="<?= esc($item['equipment']) ?>"><?= esc($item['equipment']) ?></option><?php endforeach ?>
        </select></label>
    <div class="filter-summary"><span class="filter-summary-label">Matching repairs</span><strong data-filter-count="repair-requests-table"><?= count($requests) ?> shown</strong></div>
    <div class="filter-actions"><button type="button" class="button" data-apply-filters="repair-requests-table">Filter</button><button type="button" class="button secondary" data-reset-filters="repair-requests-table">Reset</button></div>
</section>
<section class="table-workspace">
    <header class="table-toolbar">
        <div class="table-title">
            <h2>Repair requests</h2><span class="table-count" data-table-count="repair-requests-table"><?= count($requests) ?> shown</span>
        </div>
        <div class="table-tools"><input class="table-search" type="search" data-table-search="repair-requests-table" placeholder="Search repairs"></div>
    </header>
    <div class="table-wrap">
        <table id="repair-requests-table" class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Equipment</th>
                    <th>Office</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody><?php foreach ($requests as $item): ?><tr>
                        <td><?= esc($item['request_no']) ?><span class="row-detail"><a href="<?= site_url('repair-request/' . $item['request_no'] . '/print') ?>" target="_blank" rel="noopener">Print form</a></span></td>
                        <td><?= esc($item['equipment']) ?><span class="row-detail"><?= esc($item['serial_no']) ?></span></td>
                        <td><?= esc($item['office']) ?></td>
                        <td><span class="chip"><?= esc($item['status']) ?></span></td>
                        <td>
                            <div class="row-actions"><button type="button" class="button small outline" data-modal-open="repair-edit-<?= $item['id'] ?>">Edit</button><button type="button" class="button small outline" data-modal-open="repair-status-<?= $item['id'] ?>">Status</button><?php if (staff_can('admin')): ?><form method="post" action="<?= site_url('staff/repairs/' . $item['id'] . '/delete') ?>" data-confirm="Archive this repair request? Its audit history will be retained."><?= csrf_field() ?><button class="button small danger">Delete</button></form><?php endif ?></div>
                        </td>
                    </tr><?php endforeach ?><?php if ($requests === []): ?><tr class="empty-row">
                        <td colspan="5">No hardware repair requests found.</td>
                    </tr><?php endif ?></tbody>
        </table>
    </div>
</section>
<?php foreach ($requests as $item): ?><dialog id="repair-edit-<?= $item['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Edit repair request</h2><button type="button" class="modal-close" data-modal-close>×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/repairs/' . $item['id'] . '/edit') ?>"><?= csrf_field() ?><label>Requester<input name="requester" value="<?= esc($item['requester']) ?>" required></label><label>Contact<input name="contact" value="<?= esc($item['contact']) ?>" required></label><label>Office<input name="office" value="<?= esc($item['office']) ?>" required></label><label>Equipment<input name="equipment" value="<?= esc($item['equipment']) ?>" required></label><label>Serial number<input name="serial_no" value="<?= esc($item['serial_no'] ?? '') ?>"></label><label class="full">Issue<textarea name="issue" required><?= esc($item['issue']) ?></textarea></label>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Save changes</button></div>
            </form>
        </div>
    </dialog>
    <dialog id="repair-status-<?= $item['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Update repair status</h2><button type="button" class="modal-close" data-modal-close>×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/repairs/' . $item['id'] . '/status') ?>"><?= csrf_field() ?><label class="full">Next status<select name="status">
                        <option>assessed</option>
                        <option>sent</option>
                        <option>returned</option>
                        <option>released</option>
                        <option>rejected</option>
                    </select></label><label>Service destination<input name="sent_to"></label><label>Released to<input name="released_to"></label>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Update status</button></div>
            </form>
        </div>
    </dialog><?php endforeach ?>
<?= $this->endSection() ?>