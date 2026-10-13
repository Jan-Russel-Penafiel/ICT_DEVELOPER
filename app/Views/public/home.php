<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<style>
    .landing-page{width:100%;min-height:100vh;max-width:100%;margin:0;overflow-x:clip;background:#f7faf8;color:#12261c;font-family:Inter,'Segoe UI',Arial,sans-serif}
    .landing-shell{width:min(1160px,calc(100% - 40px));margin-inline:auto}

    /* HERO */
    .landing-hero{position:relative;display:flex;align-items:center;min-height:100vh;padding:clamp(120px,15vh,150px) 0 clamp(70px,10vh,110px);background:radial-gradient(1000px 520px at 88% -10%,rgba(74,222,128,.32),transparent 60%),radial-gradient(900px 560px at -10% 115%,rgba(8,123,67,.55),transparent 55%),linear-gradient(135deg,#041f14 0%,#065f36 55%,#0a8a4c 100%);color:#fff;overflow:hidden}
    .landing-hero::before{content:"";position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(ellipse at 70% 30%,black,transparent 75%)}
    .landing-hero::after{content:"";position:absolute;right:-10vw;bottom:-18vw;width:min(46vw,620px);aspect-ratio:1;border-radius:50%;background:radial-gradient(circle,rgba(74,222,128,.22),transparent 65%)}
    .landing-hero-inner{position:relative;z-index:1;width:min(1160px,calc(100% - 40px));display:grid;grid-template-columns:minmax(0,1.05fr) minmax(0,.95fr);gap:clamp(36px,5vw,72px);align-items:center;text-align:left}
    .hero-copy{display:grid;justify-items:start;max-width:620px}
    .hero-copy h1{margin:0;font-size:clamp(38px,5.4vw,66px);font-weight:800;line-height:1.08;letter-spacing:-.04em;color:#fff;text-shadow:0 4px 24px rgba(2,35,17,.45)}
    .hero-copy h1 .hl{background:none;padding:0 .05em;color:#fff}
    .hero-lede{max-width:520px;margin:22px 0 0;color:#d3efdf;font-size:clamp(15px,1.5vw,17px);line-height:1.7}
    .hero-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:32px}
    .landing-page .hero-actions .landing-primary,.landing-page .hero-actions .landing-secondary{min-height:50px;padding:14px 26px;border-radius:999px;font-size:14px;font-weight:800;transition:transform .18s ease,box-shadow .18s ease,background .18s ease}
    .landing-page .hero-actions .landing-primary{background:#fff;border:1px solid #fff;color:#065f36;box-shadow:0 14px 32px rgba(2,35,17,.4)}
    .landing-page .hero-actions .landing-primary:hover{background:#dcf7e7;transform:translateY(-2px)}
    .landing-page .hero-actions .landing-secondary{border:1px solid rgba(255,255,255,.38);background:rgba(255,255,255,.07);color:#fff;backdrop-filter:blur(6px)}
    .landing-page .hero-actions .landing-secondary:hover{background:rgba(255,255,255,.17);transform:translateY(-2px)}
    .hero-visual{position:relative;display:grid;gap:18px;place-items:center}
    .hero-visual-logo{justify-self:center;width:min(320px,80%);height:auto;transition:transform .3s ease}
    .hero-visual-logo:hover{transform:scale(1.05)}

    /* INTRO */
    .landing-section{padding:clamp(64px,8vw,104px) 0}
    .landing-intro{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(260px,.95fr);gap:clamp(36px,7vw,100px);align-items:start}
    .landing-eyebrow{display:inline-block;margin:0 0 14px;color:#0a8a4c;font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
    .landing-intro h2,.landing-steps h2,.landing-cta h2{max-width:600px;margin:0;font-size:clamp(28px,3.6vw,44px);font-weight:800;line-height:1.08;letter-spacing:-.04em;color:#0e2a1c}
    .landing-copy{color:#4f6359;font-size:16px;line-height:1.75}
    .landing-copy p:last-child{margin-bottom:0}

    /* SERVICES */
    .landing-services{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;width:min(1160px,calc(100% - 40px));margin:0 auto clamp(64px,8vw,104px)}
    .landing-service{display:flex;flex-direction:column;min-height:250px;padding:28px 24px;border:1px solid #e2ece6;border-radius:18px;background:#fff;color:inherit;text-decoration:none;box-shadow:0 8px 24px rgba(18,38,28,.05);transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
    .landing-service:hover,.landing-service:focus-visible{transform:translateY(-6px);border-color:#0a8a4c;box-shadow:0 18px 40px rgba(8,123,67,.16);outline:0}
    .landing-service-number{display:inline-grid;place-items:center;width:38px;height:38px;margin-bottom:22px;border-radius:12px;background:#e7f6ec;color:#0a8a4c;font-size:13px;font-weight:800;letter-spacing:.05em;transition:background .2s ease,color .2s ease}
    .landing-service:hover .landing-service-number{background:#0a8a4c;color:#fff}
    .landing-service h3{margin:0 0 8px;font-size:18px;font-weight:800;letter-spacing:-.02em;color:#0e2a1c}
    .landing-service p{margin:0;color:#5d7066;font-size:13.5px;line-height:1.6}
    .landing-service span:last-child{margin-top:auto;padding-top:20px;color:#0a8a4c;font-size:13px;font-weight:800}

    /* STEPS */
    .landing-steps{background:linear-gradient(180deg,#eef7f1,#e4f2e9)}
    .landing-steps-grid{display:grid;grid-template-columns:minmax(240px,.8fr) minmax(0,1.2fr);gap:clamp(32px,7vw,100px);align-items:start}
    .landing-process{display:grid;gap:14px;margin:0;padding:0;list-style:none}
    .landing-process li{display:grid;grid-template-columns:52px 1fr;gap:16px;padding:20px 22px;border:1px solid #d4e6da;border-radius:16px;background:#fff;box-shadow:0 6px 18px rgba(18,38,28,.05)}
    .landing-process strong{align-self:start;display:grid;place-items:center;width:44px;height:44px;border-radius:50%;background:#0a8a4c;color:#fff;font-size:13px;font-weight:800}
    .landing-process h3{margin:0 0 4px;font-size:16px;font-weight:800}
    .landing-process p{margin:0;color:#52675c;font-size:13.5px;line-height:1.6}

    /* CTA */
    .landing-cta{padding:clamp(64px,9vw,110px) 0;background:radial-gradient(800px 400px at 15% 0%,rgba(46,204,113,.25),transparent 60%),#0c2417;color:#fff}
    .landing-cta .landing-eyebrow{color:#6ee7a0}
    .landing-cta h2{color:#fff}
    .landing-cta p{max-width:580px;margin:16px 0 0;color:#c4dccc;font-size:16px;line-height:1.65}
    .landing-footer-note{margin:32px 0 0;color:#7fa892;font-size:12.5px}

    .landing-reveal{animation:landingRise .6s cubic-bezier(.22,.8,.35,1) both}
    .landing-reveal-delay{animation:landingRise .6s .12s cubic-bezier(.22,.8,.35,1) both}
    @keyframes landingRise{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

    @media(max-width:820px){
        .landing-intro,.landing-steps-grid{grid-template-columns:1fr}
        .landing-services{grid-template-columns:repeat(2,1fr)}
        .landing-hero{padding-top:110px}
        .landing-hero-inner{grid-template-columns:1fr}
        .hero-visual{justify-items:center}
    }
    @media(max-width:540px){
        .landing-shell,.landing-hero-inner,.landing-services{width:calc(100% - 28px)}
        .landing-services{grid-template-columns:1fr}
        .landing-service{min-height:auto}
        .hero-visual-logo{width:min(200px,60%)}
    }
    @media(prefers-reduced-motion:reduce){
        .landing-reveal,.landing-reveal-delay{animation:none}
        .landing-service,.landing-page .landing-primary,.landing-page .landing-secondary{transition:none}
    }
</style>

<div class="landing-page">
    <section class="landing-hero" aria-labelledby="landing-title">
        <div class="landing-shell landing-hero-inner">
            <div class="hero-copy">
                <h1 id="landing-title" class="landing-reveal-delay">One place for campus <span class="hl">technology services</span>.</h1>
                <p class="hero-lede landing-reveal-delay">Submit requests, register for your Campus ID, and follow every service ticket from a single official portal.</p>
            </div>
            <div class="hero-visual landing-reveal-delay">
                <img class="hero-visual-logo" src="<?= base_url('sksu.png') ?>" alt="Sultan Kudarat State University logo">
            </div>
        </div>
    </section>

    <section class="landing-section">
        <div class="landing-shell landing-intro">
            <div><p class="landing-eyebrow">The service portal</p><h2>Clear, accountable support for essential campus services.</h2></div>
            <div class="landing-copy"><p>The SKSU-IOMS Service Portal brings together common student and office ICT transactions. It is designed to make submissions straightforward, provide a clear next step, and keep request status easy to check.</p><p>Choose a service below to begin. Staff access is available separately for authorized personnel.</p></div>
        </div>
    </section>

    <section class="landing-services" aria-label="Available services">
        <a class="landing-service" href="<?= site_url('id') ?>"><span class="landing-service-number">01</span><h3>Campus ID</h3><p>Register for an available Campus ID cycle published by your Registrar.</p><span>View registration links &rarr;</span></a>
        <a class="landing-service" href="<?= site_url('id') ?>?request=repair"><span class="landing-service-number">02</span><h3>Hardware repair</h3><p>Report an issue with university equipment and receive a service reference.</p><span>Submit repair request &rarr;</span></a>
        <a class="landing-service" href="<?= site_url('id') ?>?request=reset"><span class="landing-service-number">03</span><h3>Password reset</h3><p>Request account assistance. Identity verification is completed before access is reset.</p><span>Request a reset &rarr;</span></a>
        <a class="landing-service" href="<?= site_url('id') ?>?request=track"><span class="landing-service-number">04</span><h3>Request tracking</h3><p>Check the latest status of your Campus ID, repair, or reset request.</p><span>Check status &rarr;</span></a>
    </section>

    <section class="landing-section landing-steps">
        <div class="landing-shell landing-steps-grid">
            <div><p class="landing-eyebrow">What to expect</p><h2>A simple process from submission to resolution.</h2></div>
            <ol class="landing-process">
                <li><strong>01</strong><div><h3>Choose a service</h3><p>Select the option that matches your concern and complete the required details.</p></div></li>
                <li><strong>02</strong><div><h3>Keep your reference number</h3><p>Your submission is assigned a reference number for status checks and follow-up.</p></div></li>
                <li><strong>03</strong><div><h3>Follow the update</h3><p>Use the tracking page to view progress while the responsible office processes your request.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="landing-cta">
        <div class="landing-shell">
            <p class="landing-eyebrow">Ready when you are</p>
            <h2>Start with the service you need today.</h2>
            <p>Campus ID registration links appear only while an approved registration period is active.</p>
            <p class="landing-footer-note">For official SKSU-IOMS services. Keep your request reference number for follow-up.</p>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
