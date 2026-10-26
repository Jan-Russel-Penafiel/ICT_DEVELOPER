<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$batches = $batches ?? [];
$campuses = $campuses ?? [];
$sectionsByCampus = $sectionsByCampus ?? [];
?>
<header class="page-heading">
    <div>
        <h1>Institutional accounts</h1>
        <p class="muted">Manage batch accounts and individual account requests.</p>
    </div>
    <button type="button" class="button" data-modal-open="import-accounts" data-account-batch-action>Import section list</button>
</header>
<?php $individualRequests = $individualRequests ?? []; ?>
<nav class="account-tabs" aria-label="Institutional account views">
    <a href="#batch-accounts" class="account-tab is-active" data-account-tab="batch-accounts">Batch accounts <span><?= count($batches) ?></span></a>
    <a href="#individual-accounts" class="account-tab" data-account-tab="individual-accounts">Individual accounts <span><?= count($individualRequests) ?></span></a>
</nav>
    <section id="individual-accounts" class="account-tab-panel" data-account-panel="individual-accounts" hidden>
        <?php
        $individualCampuses = array_values(array_unique(array_column($individualRequests, 'campus')));
        $individualStatuses = array_values(array_unique(array_column($individualRequests, 'status')));
        ?>
        <?= view('components/filter_bar', [
            'tableId' => 'individual-account-requests-table',
            'ariaLabel' => 'Individual account request filters',
            'summary' => 'Matching requests',
            'count' => count($individualRequests),
            'filters' => [
                ['label' => 'Campus', 'column' => 2, 'options' => array_merge([['value' => '', 'label' => 'All campuses']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], $individualCampuses))],
                ['label' => 'Status', 'column' => 5, 'options' => array_merge([['value' => '', 'label' => 'All statuses']], array_map(static fn ($value) => ['value' => $value, 'label' => ucfirst($value)], $individualStatuses))],
            ],
        ]) ?>
        <section class="table-workspace">
        <?= view('components/table_toolbar', ['tableId' => 'individual-account-requests-table', 'title' => 'Individual account requests', 'count' => count($individualRequests), 'searchPlaceholder' => 'Search student, program/section, reference...']) ?>
    <div class="table-wrap">
        <table id="individual-account-requests-table" class="data-table">
            <thead><tr><th>Reference</th><th>Student</th><th>Campus</th><th>Program / section</th><th>Contact</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($individualRequests as $request): ?>
                    <tr>
                        <td><strong><?= esc($request['ref_no']) ?></strong></td>
                        <td><?= esc(trim($request['first_name'] . ' ' . ($request['middle_name'] ?? '') . ' ' . $request['last_name'])) ?><br><span class="muted"><?= esc($request['student_no']) ?></span></td>
                        <td><?= esc($request['campus']) ?></td>
                        <td><?= esc($request['program_strand']) ?><br><span class="muted"><?= esc($request['year_level']) ?> / <?= esc($request['section']) ?></span></td>
                        <td><?= esc($request['contact_no']) ?><?php if (! empty($request['email'])): ?><br><span class="muted"><?= esc($request['email']) ?></span><?php endif ?></td>
                        <td><span class="chip"><?= esc($request['status']) ?></span></td>
                        <td><?= esc(format_manila_datetime($request['created_at'])) ?></td>
                        <td><button type="button" class="button small outline" data-modal-open="individual-status-<?= $request['id'] ?>">Status</button></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($individualRequests === []): ?><tr class="empty-row"><td colspan="8">No individual account requests found.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
</section>
</section>
<div id="batch-accounts" class="account-tab-panel" data-account-panel="batch-accounts">
<?php
$batchCampuses = array_values(array_unique(array_map(static fn ($batch) => $batch['campus'] ?? 'Unknown', $batches)));
$allSections = [];
foreach ($sectionsByCampus as $sections) {
    foreach ($sections as $section) {
        $allSections[$section] = $section;
    }
}
?>
<?= view('components/filter_bar', [
    'tableId' => 'account-batches-table',
    'ariaLabel' => 'Institutional account filters',
    'summary' => 'Matching batches',
    'count' => count($batches),
    'filters' => [
        ['label' => 'Campus', 'column' => 0, 'options' => array_merge([['value' => '', 'label' => 'All campuses']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], $batchCampuses))],
        ['label' => 'Section', 'column' => 2, 'options' => array_merge([['value' => '', 'label' => 'All sections']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], array_values($allSections)))],
    ],
]) ?>
<style>
    [data-account-batch-action][hidden]{display:none}
    .account-tabs{display:flex;gap:4px;margin:0 0 18px;border-bottom:1px solid var(--line)}
    .account-tab{padding:10px 14px;border-bottom:3px solid transparent;color:var(--muted);font-size:12px;font-weight:800;text-decoration:none}
    .account-tab:hover,.account-tab:focus-visible{color:var(--forest);outline:0}
    .account-tab.is-active{border-bottom-color:var(--forest);color:var(--forest-dark)}
    .account-tab span{display:inline-flex;min-width:20px;justify-content:center;margin-left:4px;padding:2px 6px;border-radius:999px;background:var(--mint);font-size:10px}
</style>

<dialog id="import-accounts" class="modal" data-open-on-error>
    <header class="modal-header">
        <h2>Student list per section</h2>
        <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
    </header>
    <div class="modal-body">
        <form method="post" enctype="multipart/form-data" class="grid" action="<?= site_url('staff/accounts/import') ?>">
            <?= csrf_field() ?>
            <label>Campus
                <select name="campus_id" required data-account-campus>
                    <?php foreach ($campuses as $campus): ?>
                        <option value="<?= $campus['id'] ?>" <?= old('campus_id') == $campus['id'] ? 'selected' : '' ?>><?= esc($campus['name']) ?></option>
                    <?php endforeach ?>
                </select>
            </label>
            <label>Section
                <select name="section" required data-account-section>
                    <?php foreach ($sectionsByCampus as $campusId => $sections): ?>
                        <?php foreach ($sections as $section): ?>
                            <option value="<?= esc($section) ?>" data-campus-id="<?= $campusId ?>" <?= old('section') === $section ? 'selected' : '' ?>><?= esc($section) ?></option>
                        <?php endforeach ?>
                    <?php endforeach ?>
                </select>
            </label>
            <label class="full">Student list (CSV or XLSX)
                <input type="file" name="file" accept=".csv,.xlsx" required>
            </label>
            <p class="muted full"><a href="<?= site_url('staff/accounts/import-template') ?>">Download the XLSX template</a> &mdash; include one <code>student_no</code> column for the selected section.</p>
            <div class="modal-footer full">
                <button type="button" class="button secondary" data-modal-close>Cancel</button>
                <button class="button">Import section list</button>
            </div>
        </form>
    </div>
</dialog>

<script>
const accountCampus = document.querySelector('[data-account-campus]');
const accountSection = document.querySelector('[data-account-section]');
function filterAccountSections() {
    const campusId = accountCampus?.value ?? '';
    const options = [...(accountSection?.options ?? [])];
    let firstVisible = null;
    options.forEach((option) => {
        const visible = option.dataset.campusId === campusId;
        option.hidden = !visible;
        if (visible && firstVisible === null) firstVisible = option;
    });
    if (accountSection && accountSection.selectedOptions[0]?.hidden) {
        accountSection.value = firstVisible?.value ?? '';
    }
    if (accountSection) accountSection.disabled = firstVisible === null;
}
accountCampus?.addEventListener('change', filterAccountSections);
filterAccountSections();
</script>

<section class="table-workspace">
    <?= view('components/table_toolbar', ['tableId' => 'account-batches-table', 'title' => 'Account batches', 'count' => count($batches), 'searchPlaceholder' => 'Search batches']) ?>
    <div class="table-wrap">
        <table id="account-batches-table" class="data-table">
            <thead><tr><th>Campus</th><th>Source file</th><th>Section</th><th>Accounts</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($batches as $batch): ?>
                    <tr>
                        <td><?= esc($batch['campus']) ?></td>
                        <td><?php if (! empty($batch['source_path'])): ?><a href="<?= site_url('staff/accounts/' . $batch['id'] . '/source') ?>"><?= esc($batch['source_file']) ?></a><?php else: ?><?= esc($batch['source_file']) ?><?php endif ?></td>
                        <td><?= esc($batch['section'] ?: '-') ?></td>
                        <td><span class="chip"><?= esc((string) $batch['account_count']) ?> accounts</span></td>
                        <td><span class="chip"><?= esc($batch['status'] ?? 'active') ?></span></td>
                        <td>
                            <div class="row-actions">
                                <a class="button small outline" href="<?= site_url('staff/accounts/' . $batch['id'] . '/export') ?>">Export</a>
                                <button type="button" class="button small outline" data-modal-open="batch-edit-<?= $batch['id'] ?>">Edit</button>
                                <button type="button" class="button small outline" data-modal-open="batch-status-<?= $batch['id'] ?>">Status</button>
                                <?php if (staff_can('admin')): ?>
                                    <form method="post" action="<?= site_url('staff/accounts/' . $batch['id'] . '/delete') ?>" data-confirm="Archive this account batch and every account it contains?">
                                        <?= csrf_field() ?>
                                        <button class="button small danger">Delete</button>
                                    </form>
                                <?php endif ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($batches === []): ?><tr class="empty-row"><td colspan="6">No account batches found.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
</section>
</div>
<?php foreach ($batches as $batch): ?>
    <dialog id="batch-edit-<?= $batch['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Edit account batch</h2>
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/accounts/' . $batch['id'] . '/edit') ?>">
                <?= csrf_field() ?>
                <label>Campus
                    <select name="campus_id">
                        <?php foreach ($campuses as $campus): ?>
                            <option value="<?= $campus['id'] ?>" <?= (int) $batch['campus_id'] === (int) $campus['id'] ? 'selected' : '' ?>><?= esc($campus['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
                <label>Section
                    <input name="section" value="<?= esc($batch['section'] ?? '') ?>" maxlength="50">
                </label>
                <div class="modal-footer full">
                    <button type="button" class="button secondary" data-modal-close>Cancel</button>
                    <button class="button">Save changes</button>
                </div>
            </form>
        </div>
    </dialog>
    <dialog id="batch-status-<?= $batch['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Update batch status</h2>
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/accounts/' . $batch['id'] . '/status') ?>">
                <?= csrf_field() ?>
                <label class="full">Status
                    <select name="status" required>
                        <?php foreach (['active', 'inactive'] as $status): ?>
                            <option value="<?= $status ?>" <?= ($batch['status'] ?? 'active') === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
                <div class="modal-footer full">
                    <button type="button" class="button secondary" data-modal-close>Cancel</button>
                    <button class="button">Update status</button>
                </div>
            </form>
        </div>
    </dialog>
<?php endforeach ?>
<?php foreach ($individualRequests as $request): ?>
    <dialog id="individual-status-<?= $request['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Update individual account status</h2>
            <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/accounts/individual/' . $request['id'] . '/status') ?>">
                <?= csrf_field() ?>
                <label class="full">Status
                    <select name="status" required>
                        <?php foreach (['processing', 'completed', 'rejected'] as $status): ?>
                            <option value="<?= $status ?>"><?= esc(ucfirst($status)) ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
                <div class="modal-footer full">
                    <button type="button" class="button secondary" data-modal-close>Cancel</button>
                    <button class="button">Update status</button>
                </div>
            </form>
        </div>
    </dialog>
<?php endforeach ?>
<script>
(function () {
    const tabs = document.querySelectorAll('[data-account-tab]');
    const panels = document.querySelectorAll('[data-account-panel]');
    const batchActions = document.querySelectorAll('[data-account-batch-action]');
    if (tabs.length === 0 || panels.length === 0) return;

    const activateTab = (target, updateHash = true) => {
        tabs.forEach((tab) => {
            tab.classList.toggle('is-active', tab.dataset.accountTab === target);
        });
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.accountPanel !== target;
        });
        batchActions.forEach((action) => {
            action.hidden = target !== 'batch-accounts';
        });
        if (updateHash) history.replaceState(null, '', '#' + target);
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', (event) => {
            event.preventDefault();
            activateTab(tab.dataset.accountTab);
        });
    });

    activateTab(location.hash === '#individual-accounts' ? 'individual-accounts' : 'batch-accounts', false);
})();
</script>
<?= $this->endSection() ?>
