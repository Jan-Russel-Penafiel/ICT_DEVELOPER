<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<section class="panel public-form-page auth-form-page">
    <h1>Staff sign in</h1>
    <p class="muted">Use your authorized SKSU ICT staff account to access service queues.</p>
    <form action="<?= url_to('login') ?>" method="post" class="grid inline-request-form centered-form">
        <?= csrf_field() ?>
        <label class="full" for="email">Email address
            <input id="email" type="email" name="email" value="<?= esc(old('email')) ?>" autocomplete="email" required>
        </label>
        <label class="full" for="password">Password
            <input id="password" type="password" name="password" autocomplete="current-password" required>
        </label>
        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
            <label class="full remember-me"><input type="checkbox" name="remember" <?= old('remember') ? 'checked' : '' ?>> Keep me signed in on this device</label>
        <?php endif ?>
        <div class="full form-actions">
            <button class="button" type="submit">Sign in</button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
