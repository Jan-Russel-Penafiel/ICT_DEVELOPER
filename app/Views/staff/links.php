<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$links = $links ?? [];
$campuses = $campuses ?? [];
?>
<header class="page-heading">
    <div>
        <h1>ID registration links</h1>
        <p class="muted">Manage campus registration windows.</p>
    </div><button type="button" class="button" data-modal-open="create-link">Create registration link</button>
</header>
<?php $years = array_unique(array_column($links, 'school_year')); sort($years); ?>
<?= view('components/filter_bar', [
    'tableId' => 'registration-links-table',
    'ariaLabel' => 'Registration link filters',
    'summary' => 'Matching links',
    'count' => count($links),
    'filters' => [
        ['label' => 'Campus', 'column' => 0, 'options' => array_merge([['value' => '', 'label' => 'All campuses']], array_map(static fn ($campus) => ['value' => $campus['id'], 'label' => $campus['name']], $campuses))],
        ['label' => 'School year', 'column' => 1, 'options' => array_merge([['value' => '', 'label' => 'All school years']], array_map(static fn ($year) => ['value' => $year, 'label' => $year], $years))],
    ],
]) ?>
<dialog id="create-link" class="modal" data-open-on-error>
    <header class="modal-header">
        <h2>Create registration link</h2><button type="button" class="modal-close" data-modal-close aria-label="Close">×</button>
    </header>
    <div class="modal-body">
        <form method="post" class="grid"><?= csrf_field() ?><label>Campus<select name="campus_id"><?php foreach ($campuses as $campus): ?><option value="<?= $campus['id'] ?>"><?= esc($campus['name']) ?></option><?php endforeach ?></select></label><label>School year<input name="school_year" required></label><label>Opens<input type="datetime-local" name="opens_at" required></label><label>Closes<input type="datetime-local" name="closes_at" required></label><label>Release date<input type="date" name="release_date" required></label>
            <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Create</button></div>
        </form>
    </div>
</dialog>
<section class="table-workspace">
    <?= view('components/table_toolbar', ['tableId' => 'registration-links-table', 'title' => 'Registration windows', 'count' => count($links), 'searchPlaceholder' => 'Search campus or school year']) ?>
    <div class="table-wrap">
        <table id="registration-links-table" class="data-table">
            <thead>
                <tr>
                    <th>Campus</th>
                    <th>School year</th>
                    <th>Window</th>
                    <th>Release date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody><?php foreach ($links as $link): ?><tr>
                        <td><?= esc($link['campus']) ?></td>
                        <td><?= esc($link['school_year']) ?></td>
                        <td><?= esc(format_manila_datetime($link['opens_at'])) ?><span class="row-detail">Closes <?= esc(format_manila_datetime($link['closes_at'])) ?></span></td>
                        <td><?= esc(format_manila_date($link['release_date'])) ?><span class="row-detail"><?= (! empty($link['is_active']) && time() <= strtotime((string) $link['closes_at'])) ? 'Active' : 'Inactive' ?></span></td>
                        <td>
                            <div class="row-actions"><button type="button" class="button small outline" data-modal-open="link-edit-<?= $link['id'] ?>">Edit</button><a class="button small outline" href="<?= site_url('staff/links/' . $link['id'] . '/qr') ?>" target="_blank" rel="noopener">QR</a>
                                <form method="post" action="<?= site_url('staff/links/' . $link['id'] . '/delete') ?>" data-confirm="Archive this registration link? It will no longer be accessible."><?= csrf_field() ?><button class="button small danger">Delete</button></form>
                            </div>
                        </td>
                    </tr><?php endforeach ?><?php if ($links === []): ?><tr class="empty-row">
                        <td colspan="5">No registration links found.</td>
                    </tr><?php endif ?></tbody>
        </table>
    </div>
</section>
<?php foreach ($links as $link): ?><dialog id="link-edit-<?= $link['id'] ?>" class="modal">
        <header class="modal-header">
            <h2>Edit registration link</h2><button type="button" class="modal-close" data-modal-close aria-label="Close">×</button>
        </header>
        <div class="modal-body">
            <form method="post" class="grid" action="<?= site_url('staff/links/' . $link['id'] . '/edit') ?>"><?= csrf_field() ?><label>Campus<select name="campus_id"><?php foreach ($campuses as $campus): ?><option value="<?= $campus['id'] ?>" <?= (int)$link['campus_id'] === (int)$campus['id'] ? 'selected' : '' ?>><?= esc($campus['name']) ?></option><?php endforeach ?></select></label><label>School year<input name="school_year" value="<?= esc($link['school_year']) ?>" required></label><label>Opens<input type="datetime-local" name="opens_at" value="<?= esc(date('Y-m-d\TH:i', strtotime($link['opens_at']))) ?>" required></label><label>Closes<input type="datetime-local" name="closes_at" value="<?= esc(date('Y-m-d\TH:i', strtotime($link['closes_at']))) ?>" required></label><label>Release date<input type="date" name="release_date" value="<?= esc($link['release_date']) ?>" required></label><label>Availability<select name="is_active">
                        <option value="1" <?= $link['is_active'] ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ! $link['is_active'] ? 'selected' : '' ?>>Inactive</option>
                    </select></label>
                <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Save changes</button></div>
            </form>
        </div>
    </dialog><?php endforeach ?>
<script>
document.querySelectorAll('input[name="school_year"]').forEach((input) => {
    input.addEventListener('input', () => {
        const digits = (input.value || '').replace(/\\D/g, '').slice(0, 4);
        const formatted = digits.length === 4 ? digits + ' - ' + (Number(digits) + 1) : digits;
        if (input.value !== formatted) {
            input.value = formatted;
        }
    });
});
</script>
<?= $this->endSection() ?>