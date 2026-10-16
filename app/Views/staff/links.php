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
<section class="panel filter-bar" aria-label="Registration link filters">
    <label>Campus<select data-table-filter="registration-links-table" data-column="0">
            <option value="">All campuses</option><?php foreach ($campuses as $campus): ?><option value="<?= esc($campus['id']) ?>"><?= esc($campus['name']) ?></option><?php endforeach ?>
        </select></label>
    <label>School year<select data-table-filter="registration-links-table" data-column="1">
            <option value="">All school years</option><?php $years = array_unique(array_column($links, 'school_year'));
                                                        sort($years);
                                                        foreach ($years as $year): ?><option value="<?= esc($year) ?>"><?= esc($year) ?></option><?php endforeach ?>
        </select></label>
    <div class="filter-summary"><span class="filter-summary-label">Matching links</span><strong data-filter-count="registration-links-table"><?= count($links) ?> shown</strong></div>
    <div class="filter-actions"><button type="button" class="button" data-apply-filters="registration-links-table">Filter</button><button type="button" class="button secondary" data-reset-filters="registration-links-table">Reset</button></div>
</section>
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
    <header class="table-toolbar">
        <div class="table-title">
            <h2>Registration windows</h2><span class="table-count" data-table-count="registration-links-table"><?= count($links) ?> shown</span>
        </div>
        <div class="table-tools"><input class="table-search" type="search" placeholder="Search campus or school year" data-table-search="registration-links-table"></div>
    </header>
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
                        <td><?= esc(format_manila_date($link['release_date'])) ?><span class="row-detail"><?= $link['is_active'] ? 'Active' : 'Inactive' ?></span></td>
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
<?= $this->endSection() ?>