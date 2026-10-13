<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<style>
    main.wrap{width:100%;margin:0}
    body{background:#ffffff}
    .login-page{min-height:calc(100vh - 50px);min-height:calc(100dvh - 50px);display:grid;place-items:center;padding:28px 16px;background:linear-gradient(160deg,#ecfdf5 0%,#ffffff 55%,#d1fae5 100%)}
    .login-card{width:min(430px,100%);padding:28px 32px;border-radius:20px;background:#ffffff;border:1px solid #d1fae5;box-shadow:0 24px 60px rgba(6,78,59,.12)}
    .login-logo{display:grid;place-items:center;margin:0 auto 12px;width:60px;height:60px}
    .login-logo img{width:56px;height:56px;object-fit:contain}
    .login-card h1{margin:0 0 18px;text-align:center;font-size:24px;font-weight:800;letter-spacing:-.02em;color:#064e3b}
    .login-card .login-sub{margin:0 0 26px;text-align:center;color:#6b8f7d;font-size:13.5px}
    .login-card form{display:grid;gap:14px}
    .login-card label{display:grid;gap:6px;font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#065f46}
    .login-card input[type=email],.login-card input[type=password]{min-height:46px;padding:11px 14px;border:1.5px solid #d1e7da;border-radius:12px;font-size:14px;color:#12261c;background:#f9fefb;transition:border-color .18s ease,box-shadow .18s ease,background .18s ease}
    .login-card input:focus{outline:0;border-color:#10b981;background:#fff;box-shadow:0 0 0 4px rgba(16,185,129,.15)}
    .login-card .remember-me{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;text-transform:none;letter-spacing:0;color:#3f5f50}
    .login-card .remember-me input{width:16px;height:16px;accent-color:#059669}
    .login-card .button{min-height:48px;border:0;border-radius:12px;background:linear-gradient(135deg,#059669,#10b981);color:#fff;font-size:15px;font-weight:800;letter-spacing:.02em;cursor:pointer;box-shadow:0 12px 26px rgba(5,150,105,.35);transition:transform .18s ease,box-shadow .18s ease}
    .login-card .button:hover{transform:translateY(-2px);box-shadow:0 16px 32px rgba(5,150,105,.45)}
</style>
<section class="login-page">
    <div class="login-card">
        <div class="login-logo"><img src="<?= base_url('sksu.png') ?>" alt="SKSU logo"></div>
        <h1>Staff sign in</h1>
        <form action="<?= url_to('login') ?>" method="post">
            <?= csrf_field() ?>
            <label for="email">Email address
                <input id="email" type="email" name="email" value="<?= esc(old('email')) ?>" autocomplete="email" required>
            </label>
            <label for="password">Password
                <input id="password" type="password" name="password" autocomplete="current-password" required>
            </label>
            <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                <label class="remember-me"><input type="checkbox" name="remember" <?= old('remember') ? 'checked' : '' ?>> Keep me signed in on this device</label>
            <?php endif ?>
            <button class="button" type="submit">Sign in</button>
        </form>
    </div>
</section>
<?= $this->endSection() ?>
