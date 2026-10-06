<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<section class="panel public-form-page">
    <h1>Password reset request</h1>
    <p class="muted">ICT verifies identity before resetting any account.</p>
    <form method="post" class="grid inline-request-form">
        <?= csrf_field() ?>
        <label>Student number
            <input name="student_no" value="<?= esc(old('student_no')) ?>" autocomplete="username" required>
        </label>
        <label>Name
            <input name="requester_name" value="<?= esc(old('requester_name')) ?>" autocomplete="name" required>
        </label>
        <label class="full">Email or mobile number
            <input name="contact" value="<?= esc(old('contact')) ?>" autocomplete="email" required>
        </label>
        <div class="full form-actions">
            <button class="button" type="submit">Request reset</button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
