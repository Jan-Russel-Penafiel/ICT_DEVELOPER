<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<?php
$ref = $ref ?? '';
$ticket = $ticket ?? null;
?>
<section class="panel public-form-page">
    <h1>Track request</h1>
    <p class="muted">Enter the reference number issued after your submission.</p>
    <form method="get" class="homepage-track inline-request-form">
        <label for="track-ref">Reference number
            <input id="track-ref" name="ref" value="<?= esc($ref) ?>" placeholder="ID-..., RPR-..., or RST-..." required>
        </label>
        <button class="button" type="submit">Check status</button>
    </form>
</section>

<?php if ($ref !== '' && ! $ticket): ?><div hidden data-swal-alert data-swal-icon="error" data-swal-title="Request not found" data-swal-text="No request matches that reference number."></div><?php endif ?>
<?php if ($ticket): ?>
    <section class="panel">
        <h2><?= esc($ticket['reference_no']) ?> <span class="chip"><?= esc($ticket['status']) ?></span></h2>
        <div class="grid">
            <p>Submitted<br><strong><?= esc(format_manila_datetime($ticket['created_at'])) ?></strong></p>
            <?php if (isset($ticket['campus'])): ?><p>Campus<br><strong><?= esc($ticket['campus']) ?></strong></p><?php endif ?>
            <?php if (! empty($ticket['released_at'])): ?><p>Released<br><strong><?= esc(format_manila_datetime($ticket['released_at'])) ?></strong></p><?php endif ?>
        </div>
    </section>
<?php endif ?>
<?= $this->endSection() ?>
