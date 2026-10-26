<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$batches = $batches ?? [];
$campuses = $campuses ?? [];
$sectionsByCampus = $sectionsByCampus ?? [];
$students = $students ?? [];
$individualAccounts = $individualAccounts ?? [];
?>
<header class="page-heading">
    <div>
        <h1>Institutional accounts</h1>
        <p class="muted">Manage batch imports and individual institutional accounts.</p>
    </div>
    <div class="row-actions">
        <button type="button" class="button" data-modal-open="create-individual-account" data-account-individual-action>Create individual account</button>
        <button type="button" class="button" data-modal-open="import-accounts" data-account-batch-action>Import section list</button>
    </div>
</header>
<nav class="account-tabs" aria-label="Institutional account views">
    <a href="#batch-accounts" class="account-tab is-active" data-account-tab="batch-accounts">Batch accounts <span><?= count($batches) ?></span></a>
    <a href="#individual-accounts" class="account-tab" data-account-tab="individual-accounts">Individual accounts <span><?= count($individualAccounts) ?></span></a>
</nav>
<section id="individual-accounts" class="account-tab-panel" data-account-panel="individual-accounts" hidden>
<?php
$individualCampuses = array_values(array_unique(array_map(static fn ($account) => $account['campus'] ?? 'Unknown', $individualAccounts)));
?>
<?= view('components/filter_bar', [
    'tableId' => 'individual-accounts-table',
    'ariaLabel' => 'Individual account filters',
    'summary' => 'Matching accounts',
    'count' => count($individualAccounts),
    'filters' => [
        ['label' => 'Campus', 'column' => 1, 'options' => array_merge([['value' => '', 'label' => 'All campuses']], array_map(static fn ($value) => ['value' => $value, 'label' => $value], $individualCampuses))],
    ],
]) ?>
<section class="table-workspace">
    <?= view('components/table_toolbar', ['tableId' => 'individual-accounts-table', 'title' => 'Individual account records', 'count' => count($individualAccounts), 'searchPlaceholder' => 'Search student, email, campus...']) ?>
    <div class="table-wrap">
        <table id="individual-accounts-table" class="data-table">
            <thead><tr><th>Student</th><th>Campus</th><th>Email</th><th>Organizational unit</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($individualAccounts as $account): ?>
                    <tr>
                        <td><?= esc(trim($account['last_name'] . ', ' . $account['first_name'] . ' ' . ($account['middle_name'] ?? ''))) ?><br><span class="muted"><?= esc($account['student_no']) ?></span></td>
                        <td><?= esc($account['campus']) ?></td>
                        <td><?= esc($account['email']) ?></td>
                        <td><?= esc($account['org_unit']) ?></td>
                        <td><?= esc(format_manila_datetime($account['created_at'])) ?></td>
                        <td>
                            <div class="row-actions">
                                <button type="button" class="button small outline" data-modal-open="individual-edit-<?= $account['id'] ?>">Edit</button>
                                <?php if (staff_can('admin')): ?>
                                    <form method="post" action="<?= site_url('staff/accounts/individual/' . $account['id'] . '/delete') ?>" data-confirm="Archive this individual institutional account?">
                                        <?= csrf_field() ?>
                                        <button class="button small danger">Delete</button>
                                    </form>
                                <?php endif ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if ($individualAccounts === []): ?><tr class="empty-row"><td colspan="6">No individual accounts created.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
</section>
</section>
<?php foreach ($individualAccounts as $account): ?>
<dialog id="individual-edit-<?= $account['id'] ?>" class="modal">
    <header class="modal-header">
        <h2>Edit individual institutional account</h2>
        <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
    </header>
    <div class="modal-body">
        <form method="post" class="grid" action="<?= site_url('staff/accounts/individual/' . $account['id'] . '/edit') ?>">
            <?= csrf_field() ?>
            <label>Student
                <input type="text" value="<?= esc(trim($account['last_name'] . ', ' . $account['first_name'] . ' ' . ($account['middle_name'] ?? ''))) ?> (<?= esc($account['student_no']) ?>)" readonly>
            </label>
            <label>Email
                <input type="email" value="<?= esc($account['email']) ?>" readonly>
            </label>
            <label>Organizational unit
                <input name="org_unit" value="<?= esc(old('org_unit', $account['org_unit'])) ?>" maxlength="190" required>
            </label>
            <div class="modal-footer full">
                <button type="button" class="button secondary" data-modal-close>Cancel</button>
                <button class="button">Save changes</button>
            </div>
        </form>
    </div>
</dialog>
<?php endforeach ?>
<style>
    [data-account-batch-action][hidden],[data-account-individual-action][hidden]{display:none}
    .account-tabs{display:flex;gap:4px;margin:0 0 18px;border-bottom:1px solid var(--line)}
    .account-tab{padding:10px 14px;border-bottom:3px solid transparent;color:var(--muted);font-size:12px;font-weight:800;text-decoration:none}
    .account-tab:hover,.account-tab:focus-visible{color:var(--forest);outline:0}
    .account-tab.is-active{border-bottom-color:var(--forest);color:var(--forest-dark)}
    .account-tab span{display:inline-flex;min-width:20px;justify-content:center;margin-left:4px;padding:2px 6px;border-radius:999px;background:var(--mint);font-size:10px}
</style>

<dialog id="create-individual-account" class="modal" data-open-on-error>
    <header class="modal-header">
        <h2>Create individual institutional account</h2>
        <button type="button" class="modal-close" data-modal-close aria-label="Close">&times;</button>
    </header>
    <div class="modal-body">
        <form method="post" class="grid" action="<?= site_url('staff/accounts/individual') ?>">
            <?= csrf_field() ?>
            <label class="full">Student name
                <select name="student_id" required>
                    <option value="">Select a registered student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['id'] ?>" <?= old('student_id') == $student['id'] ? 'selected' : '' ?>>
                            <?= esc(trim($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? ''))) ?> (<?= esc($student['student_no']) ?>)
                        </option>
                    <?php endforeach ?>
                </select>
            </label>
            <label>Organizational unit
                <input name="org_unit" value="<?= esc(old('org_unit', '/Students')) ?>" maxlength="190" required>
            </label>
            <label>Default password
                <input type="password" name="default_password" minlength="8" maxlength="255" required autocomplete="new-password">
            </label>
            <p class="muted full">The password is placed only in the downloaded CSV and is not stored in the portal.</p>
            <div class="modal-footer full">
                <button type="button" class="button secondary" data-modal-close>Cancel</button>
                <button class="button">Create and download account CSV</button>
            </div>
        </form>
    </div>
</dialog>

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
            <p class="muted full">
                <a href="<?= site_url('staff/accounts/import-template.csv') ?>">Download the CSV template</a>
                or <a href="<?= site_url('staff/accounts/import-template') ?>">the XLSX template</a>
                &mdash; include <code>student_no</code> plus first name, last name, domain extension, default password, and organizational unit columns.
            </p>
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
<script>
(function () {
    const tabs = document.querySelectorAll('[data-account-tab]');
    const panels = document.querySelectorAll('[data-account-panel]');
    const batchActions = document.querySelectorAll('[data-account-batch-action]');
    const individualActions = document.querySelectorAll('[data-account-individual-action]');
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
        individualActions.forEach((action) => {
            action.hidden = target !== 'individual-accounts';
        });
        localStorage.setItem('ictAccountTab', target);
        if (updateHash) history.replaceState(null, '', '#' + target);
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', (event) => {
            event.preventDefault();
            activateTab(tab.dataset.accountTab);
        });
    });

    const initialTab = localStorage.getItem('ictAccountTab') ?? (location.hash === '#individual-accounts' ? 'individual-accounts' : 'batch-accounts');
    activateTab(initialTab, false);
})();
</script>
<?= $this->endSection() ?>
