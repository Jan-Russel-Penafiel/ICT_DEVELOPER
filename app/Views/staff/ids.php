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
<?= view('components/filter_bar', [
    'tableId' => 'id-requests-table',
    'ariaLabel' => 'ID request filters',
    'summary' => 'Matching requests',
    'count' => count($requests),
    'filters' => [
        ['label' => 'Status', 'column' => 3, 'options' => array_merge([['value' => '', 'label' => 'All statuses']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], ['Submitted', 'Verified', 'Needs correction', 'Printed', 'Released', 'Rejected']))],
        ['label' => 'Request type', 'column' => 0, 'options' => [['value' => '', 'label' => 'All request types'], ['value' => 'new', 'label' => 'new'], ['value' => 'replacement', 'label' => 'replacement']]],
        ['label' => 'Campus', 'column' => 2, 'options' => array_merge([['value' => '', 'label' => 'All campuses']], array_map(static fn ($campus) => ['value' => $campus['name'], 'label' => $campus['name']], $campuses))],
    ],
]) ?>
<section class="table-workspace">
    <?= view('components/table_toolbar', ['tableId' => 'id-requests-table', 'title' => 'ID requests', 'count' => count($requests), 'searchPlaceholder' => 'Search reference or student']) ?>
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