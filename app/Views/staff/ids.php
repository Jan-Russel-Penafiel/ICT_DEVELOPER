<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$requests = $requests ?? [];
$campuses = $campuses ?? [];
?>
<header class="page-heading">
    <div>
        <h1>ID queue</h1>
        <p class="muted">Review, produce, and release student identification requests.</p>
    </div>
    <?php if (staff_can('admin', 'ict')): ?><form method="get" action="<?= site_url('staff/ids/export') ?>"><button type="submit" class="button">Export production CSV</button></form><?php endif ?>
</header>
<section class="panel filter-bar" aria-label="ID request filters">
    <label>Status<select data-table-filter="id-requests-table" data-column="3">
            <option value="">All statuses</option>
            <option>Submitted</option>
            <option>Verified</option>
            <option>Needs correction</option>
            <option>Printed</option>
            <option>Released</option>
            <option>Rejected</option>
        </select></label>
    <label>Request type<select data-table-filter="id-requests-table" data-column="0">
            <option value="">All request types</option>
            <option>new</option>
            <option>replacement</option>
        </select></label>
    <label>Campus<select data-table-filter="id-requests-table" data-column="2">
            <option value="">All campuses</option><?php foreach ($campuses as $campus): ?><option value="<?= esc($campus['name']) ?>"><?= esc($campus['name']) ?></option><?php endforeach ?>
        </select></label>
    <div class="filter-summary"><span class="filter-summary-label">Matching requests</span><strong data-filter-count="id-requests-table"><?= count($requests) ?> shown</strong></div>
    <div class="filter-actions"><button type="button" class="button" data-apply-filters="id-requests-table">Filter</button><button type="button" class="button secondary" data-reset-filters="id-requests-table">Reset</button></div>
</section>
<section class="table-workspace">
    <header class="table-toolbar">
        <div class="table-title">
            <h2>ID requests</h2><span class="table-count" data-table-count="id-requests-table"><?= count($requests) ?> shown</span>
        </div>
        <div class="table-tools"><input class="table-search" type="search" placeholder="Search reference or student" data-table-search="id-requests-table" aria-label="Search ID requests"></div>
    </header>
    <div class="table-wrap">
        <table id="id-requests-table" class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Campus</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $item): ?>
                    <tr>
                        <td><span class="row-main"><?= esc($item['reference_no']) ?></span><span class="row-detail"><?= esc($item['type']) ?></span></td>
                        <td><span class="row-main"><?= esc($item['last_name'] . ', ' . $item['first_name']) ?></span><span class="row-detail"><?= esc($item['student_no']) ?></span></td>
                        <td><?= esc($item['campus']) ?></td>
                        <td><span class="chip"><?= esc($item['status']) ?></span></td>
                        <td>
                            <div class="row-actions"><?php if (staff_can('admin', 'ict')): ?><button type="button" class="button small outline" data-modal-open="id-edit-<?= esc((string) $item['id']) ?>">Edit</button><?php endif ?><button type="button" class="button small outline" data-modal-open="id-status-<?= esc((string) $item['id']) ?>">Status</button><?php if (staff_can('admin')): ?><form method="post" action="<?= site_url('staff/ids/' . $item['id'] . '/delete') ?>" data-confirm="Archive this ID request? Its audit history will be retained."><?= csrf_field() ?><button class="button small danger">Delete</button></form><?php endif ?></div>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($requests === []): ?><tr class="empty-row">
                        <td colspan="5">No ID requests found.</td>
                    </tr><?php endif ?>
            </tbody>
        </table>
    </div>
</section>
<?php foreach ($requests as $item): ?>
    <dialog id="id-status-<?= esc((string) $item['id']) ?>" class="modal">
        <header class="modal-header">
            <div>
                <h2>Update ID request</h2>
                <p class="muted"><?= esc($item['reference_no']) ?> · <?= esc($item['last_name'] . ', ' . $item['first_name']) ?></p>
            </div><button type="button" class="modal-close" data-modal-close aria-label="Close">×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/ids/' . $item['id'] . '/status') ?>"><?= csrf_field() ?><label class="full">Next status<select name="status"><?php foreach (staff_can('registrar') && ! staff_can('admin', 'ict') ? ['released'] : ['verified', 'needs_correction', 'printed', 'released', 'rejected'] as $status): ?><option value="<?= esc($status) ?>"><?= esc(ucwords(str_replace('_', ' ', $status))) ?></option><?php endforeach ?></select></label><label class="full">Receiver name <span class="muted">Required only when releasing</span><input name="received_by" placeholder="Name of receiver"></label>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Update request</button></div>
            </form>
        </div>
    </dialog>
<?php endforeach ?>
<?php foreach ($requests as $item): ?>
    <?php if (staff_can('admin', 'ict')): ?><dialog id="id-edit-<?= esc((string)$item['id']) ?>" class="modal">
            <header class="modal-header">
                <h2>Edit ID request</h2><button type="button" class="modal-close" data-modal-close aria-label="Close">×</button>
            </header>
            <div class="modal-body">
                <form method="post" class="grid" action="<?= site_url('staff/ids/' . $item['id'] . '/edit') ?>"><?= csrf_field() ?><label>Student number<input name="student_no" value="<?= esc($item['student_no']) ?>" required></label><label>First name<input name="first_name" value="<?= esc($item['first_name']) ?>" required></label><label>Last name<input name="last_name" value="<?= esc($item['last_name']) ?>" required></label><label>Middle name<input name="middle_name" value="<?= esc($item['middle_name'] ?? '') ?>"></label><label>Program or strand<input name="program_strand" value="<?= esc($item['program_strand']) ?>" required></label><label>Year level<input name="year_level" value="<?= esc($item['year_level']) ?>" required></label><label>Section<input name="section" value="<?= esc($item['section']) ?>" required></label><label>Contact number<input name="contact_no" value="<?= esc($item['contact_no']) ?>" required></label><label class="full">Email address<input type="email" name="email" value="<?= esc($item['email'] ?? '') ?>"></label>
                    <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Save changes</button></div>
                </form>
            </div>
        </dialog><?php endif ?>
<?php endforeach ?>
<?= $this->endSection() ?>