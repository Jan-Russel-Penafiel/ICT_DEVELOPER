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
<?= view('components/filter_bar', [
    'tableId' => 'replacements-table',
    'ariaLabel' => 'Replacement request filters',
    'summary' => 'Matching requests',
    'count' => count($requests),
    'filters' => [['label' => 'Status', 'column' => 3, 'options' => array_merge([['value' => '', 'label' => 'All statuses']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], ['Active', 'Completed', 'Cancelled']))]],
]) ?>
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
    <?= view('components/table_toolbar', ['tableId' => 'replacements-table', 'title' => 'Replacement requests', 'count' => count($requests), 'searchPlaceholder' => 'Search replacement requests']) ?>
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