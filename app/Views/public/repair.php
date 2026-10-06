<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<section class="panel public-form-page repair-form-page">
    <h1>Hardware repair request</h1>
    <p class="muted">Provide the equipment details and ICT will issue a tracking reference.</p>
    <form method="post" class="grid inline-request-form">
        <?= csrf_field() ?>
        <label>Requester
            <input name="requester" value="<?= esc(old('requester')) ?>" autocomplete="name" required>
        </label>
        <label>Contact
            <input name="contact" value="<?= esc(old('contact')) ?>" autocomplete="tel" required>
        </label>
        <label>Office / unit
            <input name="office" value="<?= esc(old('office')) ?>" autocomplete="organization" required>
        </label>
        <label>Equipment
            <input name="equipment" value="<?= esc(old('equipment')) ?>" required>
        </label>
        <label class="full">Serial number <span class="field-optional">Optional</span>
            <input name="serial_no" value="<?= esc(old('serial_no')) ?>">
        </label>
        <label class="full">Issue
            <textarea name="issue" rows="5" required><?= esc(old('issue')) ?></textarea>
        </label>
        <div class="full form-actions">
            <button class="button" type="submit">Submit request</button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
