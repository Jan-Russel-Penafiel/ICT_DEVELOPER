<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<header class="page-heading">
    <div>
        <h1>Institutional accounts</h1>
        <p class="muted">Import, export, and maintain section account batches.</p>
    </div>
    <button type="button" class="button" data-modal-open="import-accounts">Import section list</button>
</header>
<section class="panel filter-bar" aria-label="Institutional account filters">
    <label>Campus<select data-table-filter="account-batches-table" data-column="0"><option value="">All campuses</option><?php foreach ($batches as $batch): ?><option value="<?= esc($batch['campus'] ?? 'Unknown') ?>"><?= esc($batch['campus'] ?? 'Unknown') ?></option><?php endforeach ?></select></label>
    <label>Section<select data-table-filter="account-batches-table" data-column="2"><option value="">All sections</option><?php $allSections = []; foreach ($sectionsByCampus as $campusId => $sections) { foreach ($sections as $section) { $allSections[$section] = $section; } } ?><?php foreach (array_unique($allSections) as $section): ?><option value="<?= esc($section) ?>"><?= esc($section) ?></option><?php endforeach ?></select></label>
    <div class="filter-summary"><span class="filter-summary-label">Matching batches</span><strong data-filter-count="account-batches-table"><?= count($batches) ?> shown</strong></div>
    <div class="filter-actions"><button type="button" class="button" data-apply-filters="account-batches-table">Filter</button><button type="button" class="button secondary" data-reset-filters="account-batches-table">Reset</button></div>
</section>

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
    <header class="table-toolbar">
        <div class="table-title">
            <h2>Account batches</h2>
            <span class="table-count" data-table-count="account-batches-table"><?= count($batches) ?> shown</span>
        </div>
        <div class="table-tools"><input class="table-search" type="search" data-table-search="account-batches-table" placeholder="Search batches"></div>
    </header>
    <div class="table-wrap">
        <table id="account-batches-table" class="data-table">
            <thead><tr><th>Campus</th><th>Source file</th><th>Section</th><th>Accounts</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($batches as $batch): ?>
                    <tr>
                        <td><?= esc($batch['campus']) ?></td>
                        <td><?php if (! empty($batch['source_path'])): ?><a href="<?= site_url('staff/accounts/' . $batch['id'] . '/source') ?>"><?= esc($batch['source_file']) ?></a><?php else: ?><?= esc($batch['source_file']) ?><?php endif ?></td>
                        <td><?= esc($batch['section'] ?: '-') ?></td>
                        <td><span class="chip"><?= esc((string) $batch['account_count']) ?> accounts</span></td>
                        <td>
                            <div class="row-actions">
                                <a class="button small outline" href="<?= site_url('staff/accounts/' . $batch['id'] . '/export') ?>">Export</a>
                                <button type="button" class="button small outline" data-modal-open="batch-edit-<?= $batch['id'] ?>">Edit</button>
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
                <?php if ($batches === []): ?><tr class="empty-row"><td colspan="5">No account batches found.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
</section>

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
<?php endforeach ?>
<?= $this->endSection() ?>
