<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$requests = $requests ?? [];
?>
<header class="page-heading">
    <div>
        <h1>Lost-ID replacements</h1>
        <p class="muted">Record payments and manage replacement requests.</p>
    </div><?php if (staff_can('admin', 'cashier')): ?><button type="button" class="button" data-modal-open="record-payment">Record payment</button><?php endif ?>
</header>
<section class="panel filter-bar" aria-label="Replacement request filters">
    <label>Status<select data-table-filter="replacements-table" data-column="3">
            <option value="">All statuses</option>
            <option>Active</option>
            <option>Completed</option>
            <option>Cancelled</option>
        </select></label>
    <div class="filter-summary"><span class="filter-summary-label">Matching requests</span><strong data-filter-count="replacements-table"><?= count($requests) ?> shown</strong></div>
    <div class="filter-actions"><button type="button" class="button" data-apply-filters="replacements-table">Filter</button><button type="button" class="button secondary" data-reset-filters="replacements-table">Reset</button></div>
</section>
<?php if (staff_can('admin', 'cashier')): ?><dialog id="record-payment" class="modal" data-open-on-error>
        <header class="modal-header">
            <h2>Record replacement payment</h2><button type="button" class="modal-close" data-modal-close>×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid"><?= csrf_field() ?><label class="full">Student number<input name="student_no" required></label>
                <p class="muted full">The official receipt number will be generated automatically when the payment is recorded.</p>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Record payment</button></div>
            </form>
        </div>
    </dialog><?php endif ?>
<section class="table-workspace">
    <header class="table-toolbar">
        <div class="table-title">
            <h2>Replacement requests</h2><span class="table-count" data-table-count="replacements-table"><?= count($requests) ?> shown</span>
        </div>
        <div class="table-tools"><input class="table-search" type="search" data-table-search="replacements-table" placeholder="Search replacement requests"></div>
    </header>
    <div class="table-wrap">
        <table id="replacements-table" class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Official receipt</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody><?php foreach ($requests as $item): ?><tr>
                        <td><?= esc($item['reference_no']) ?></td>
                        <td><?= esc($item['last_name'] . ', ' . $item['first_name']) ?></td>
                        <td><?= esc($item['or_number']) ?></td>
                        <td><span class="chip"><?= esc($item['status']) ?></span></td>
                        <td>
                            <div class="row-actions"><?php if ($item['or_number'] !== null && trim((string) $item['or_number']) !== ''): ?><a class="button small outline" href="<?= site_url('staff/replacements/' . $item['id'] . '/receipt') ?>" target="_blank" rel="noopener">Print Official Receipt</a><?php endif ?><?php if (staff_can('admin')): ?><form method="post" action="<?= site_url('staff/replacements/' . $item['id'] . '/delete') ?>" data-confirm="Archive this replacement request? Its audit history will be retained."><?= csrf_field() ?><button class="button small danger">Delete</button></form><?php endif ?></div>
                        </td>
                    </tr><?php endforeach ?><?php if ($requests === []): ?><tr class="empty-row">
                        <td colspan="5">No replacement requests found.</td>
                    </tr><?php endif ?></tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>