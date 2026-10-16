<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<?php
$link = $link ?? [];
$open = $open ?? false;
?>
<section class="panel">
    <h1><?= esc($link['campus_name']) ?> ID registration</h1>
    <p class="muted">School year <?= esc($link['school_year']) ?> &middot; ID release begins <?= esc(format_manila_date($link['release_date'])) ?></p>
    <?php if (! $open): ?>
        <div hidden data-swal-alert data-swal-icon="error" data-swal-title="Registration unavailable" data-swal-text="<?= esc('This link accepts submissions from ' . format_manila_datetime($link['opens_at']) . ' until ' . format_manila_datetime($link['closes_at']) . ', Manila time.', 'attr') ?>"></div>
    <?php else: ?>
        <button type="button" class="button" data-modal-open="registration-form">Start registration</button>
        <dialog id="registration-form" class="modal registration-modal" data-open-on-error>
        <header class="modal-header"><div><h2>Register for an ID</h2><p class="muted"><?= esc($link['campus_name']) ?></p></div><button type="button" class="modal-close" data-modal-close aria-label="Close">×</button></header>
        <div class="modal-body"><form method="post" enctype="multipart/form-data" class="grid registration-form-grid">
            <?= csrf_field() ?>
            <label>Student number<input name="student_no" value="<?= esc(old('student_no')) ?>" required></label>
            <label>Contact number<input name="contact_no" value="<?= esc(old('contact_no')) ?>" required></label>
            <label>First name<input name="first_name" value="<?= esc(old('first_name')) ?>" required></label>
            <label>Last name<input name="last_name" value="<?= esc(old('last_name')) ?>" required></label>
            <label>Middle name<input name="middle_name" value="<?= esc(old('middle_name')) ?>"></label>
            <label>Email<input type="email" name="email" value="<?= esc(old('email')) ?>"></label>
            <label class="registration-field-wide">Program / strand<input name="program_strand" value="<?= esc(old('program_strand')) ?>" required></label>
            <label class="registration-field-narrow">Year level<input name="year_level" value="<?= esc(old('year_level')) ?>" required></label>
            <label class="registration-field-narrow">Section<input name="section" value="<?= esc(old('section')) ?>" required></label>
            <label>Photo (JPG or PNG, 5 MB max)<input type="file" name="photo" accept="image/jpeg,image/png"></label>
            <label class="full" style="display:flex;align-items:flex-start;gap:8px;font-weight:600"><input style="width:auto;min-height:auto;margin-top:4px" type="checkbox" name="consent" value="1" required> I consent to the processing of this information for school ID issuance.</label>
            <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Submit registration</button></div>
        </form></div></dialog>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
