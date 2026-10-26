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
<?= view('components/filter_bar', [
    'tableId' => 'repair-requests-table',
    'ariaLabel' => 'Hardware repair queue filters',
    'summary' => 'Matching repairs',
    'count' => count($requests),
    'filters' => [
        ['label' => 'Status', 'column' => 3, 'options' => array_merge([['value' => '', 'label' => 'All statuses']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], ['assessed', 'sent', 'returned', 'released', 'rejected']))],
        ['label' => 'Office', 'column' => 2, 'options' => array_merge([['value' => '', 'label' => 'All offices']], array_map(static fn ($item) => ['value' => $item['equipment'], 'label' => $item['equipment']], $equipment))],
    ],
]) ?>
<section class="table-workspace">
    <?= view('components/table_toolbar', ['tableId' => 'repair-requests-table', 'title' => 'Repair requests', 'count' => count($requests), 'searchPlaceholder' => 'Search repairs']) ?>
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