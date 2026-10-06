<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<style>
    .landing-page{width:100vw;margin:0;overflow:hidden;background:#f6f8f6;color:#163428}.landing-shell{width:min(1120px,calc(100% - 40px));margin-inline:auto}.landing-hero{position:relative;min-height:100svh;display:grid;place-items:center;overflow:hidden;background:linear-gradient(118deg,#063c25 0%,#087b43 52%,#22a85c 100%);color:#fff}.landing-hero::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 78% 30%,rgba(224,255,230,.3),transparent 11%),radial-gradient(circle at 83% 70%,rgba(255,255,255,.14),transparent 23%),linear-gradient(145deg,transparent 0 58%,rgba(255,255,255,.08) 58% 58.2%,transparent 58.2%)}.landing-hero::after{content:"";position:absolute;right:-12vw;bottom:-23vw;width:min(53vw,700px);aspect-ratio:1;border:1px solid rgba(255,255,255,.32);border-radius:50%;box-shadow:0 0 0 34px rgba(255,255,255,.07),0 0 0 92px rgba(255,255,255,.05)}.landing-hero-inner{position:relative;z-index:1;display:grid;justify-items:center;width:min(760px,100%);text-align:center}.landing-kicker{display:flex;align-items:center;gap:9px;margin:0 0 16px;color:#d7f5e2;font-size:11px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}.landing-kicker::before{content:"";width:28px;height:2px;background:#a8f0bf}.landing-brand{margin:0 0 10px;font-size:clamp(28px,4.2vw,55px);font-weight:850;line-height:1;letter-spacing:-.06em}.landing-hero h1{max-width:700px;margin:0;font-size:clamp(33px,5vw,68px);font-weight:700;line-height:.99;letter-spacing:-.055em}.landing-lede{max-width:570px;margin:22px auto 0;color:#e4f7eb;font-size:clamp(15px,1.5vw,18px);line-height:1.65}.landing-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:30px}.landing-page .landing-primary,.landing-page .landing-secondary{min-height:44px;padding:12px 17px;border-radius:5px;font-size:12px;letter-spacing:.01em}.landing-page .landing-primary{background:#fff;border-color:#fff;color:#086b3d}.landing-page .landing-primary:hover{background:#e5f6ea;border-color:#e5f6ea}.landing-page .landing-secondary{border-color:rgba(255,255,255,.65);background:transparent;color:#fff}.landing-page .landing-secondary:hover{background:rgba(255,255,255,.12);border-color:#fff}
.landing-section{padding:clamp(58px,8vw,100px) 0}.landing-intro{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(250px,.9fr);gap:clamp(42px,8vw,110px);align-items:start}.landing-eyebrow{margin:0 0 13px;color:#087b43;font-size:11px;font-weight:850;letter-spacing:.12em;text-transform:uppercase}.landing-intro h2,.landing-steps h2,.landing-cta h2{max-width:590px;margin:0;font-size:clamp(28px,3.7vw,46px);line-height:1.05;letter-spacing:-.045em}.landing-copy{color:#52675c;font-size:16px;line-height:1.7}.landing-copy p:last-child{margin-bottom:0}.landing-services{display:grid;grid-template-columns:repeat(4,1fr);border-top:1px solid #cfe0d5;border-bottom:1px solid #cfe0d5}.landing-service{min-height:245px;padding:26px 23px 25px;border-right:1px solid #cfe0d5;background:#fff;color:inherit;text-decoration:none;transition:background .2s ease,color .2s ease,transform .2s ease}.landing-service:last-child{border-right:0}.landing-service:hover,.landing-service:focus-visible{background:#087b43;color:#fff;outline:0;transform:translateY(-5px)}.landing-service-number{display:block;margin-bottom:38px;color:#54a675;font-size:11px;font-weight:850;letter-spacing:.1em}.landing-service:hover .landing-service-number{color:#baf0cc}.landing-service h3{margin:0 0 10px;font-size:19px;letter-spacing:-.025em}.landing-service p{margin:0;color:#64766d;font-size:13px;line-height:1.6}.landing-service:hover p{color:#e1f7e9}.landing-service span:last-child{display:block;margin-top:22px;font-size:12px;font-weight:850}.landing-steps{background:#eaf5ef}.landing-steps-grid{display:grid;grid-template-columns:minmax(230px,.75fr) minmax(0,1.25fr);gap:clamp(36px,8vw,110px);align-items:start}.landing-process{display:grid;gap:0;margin:4px 0 0;padding:0;list-style:none;border-top:1px solid #bcd4c4}.landing-process li{display:grid;grid-template-columns:45px 1fr;gap:13px;padding:19px 0;border-bottom:1px solid #bcd4c4}.landing-process strong{color:#087b43;font-size:12px;letter-spacing:.07em}.landing-process h3{margin:0 0 3px;font-size:15px}.landing-process p{margin:0;color:#52675c;font-size:13px;line-height:1.55}.landing-cta{padding:clamp(60px,10vw,116px) 0;background:#112d20;color:#fff}.landing-cta-inner{display:flex;justify-content:space-between;align-items:end;gap:35px}.landing-cta h2{max-width:630px}.landing-cta p{max-width:570px;margin:17px 0 0;color:#cfe5d6;font-size:16px;line-height:1.65}.landing-footer-note{margin-top:35px;color:#8fb8a0;font-size:12px}.landing-reveal{animation:landingRise .65s both}.landing-reveal-delay{animation:landingRise .65s .13s both}@keyframes landingRise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}@media(max-width:820px){.landing-hero{min-height:600px;padding:105px 0 55px}.landing-intro,.landing-steps-grid{grid-template-columns:1fr}.landing-services{grid-template-columns:repeat(2,1fr)}.landing-service:nth-child(2){border-right:0}.landing-service:nth-child(-n+2){border-bottom:1px solid #cfe0d5}.landing-cta-inner{display:block}.landing-cta .landing-actions{margin-top:27px}}@media(max-width:520px){.landing-shell{width:min(100% - 28px,1120px)}.landing-hero{min-height:100svh;padding:105px 0 48px}.landing-hero::after{right:-120px;bottom:-90px;width:360px}.landing-brand{font-size:28px}.landing-hero h1{font-size:39px}.landing-services{grid-template-columns:1fr}.landing-service,.landing-service:nth-child(2){min-height:auto;border-right:0;border-bottom:1px solid #cfe0d5}.landing-service:last-child{border-bottom:0}.landing-section{padding-block:58px}.landing-cta{padding-block:65px}}@media(prefers-reduced-motion:reduce){.landing-reveal{animation:none}.landing-service{transition:none}}
</style>
<style>.landing-hero{display:flex;align-items:end;justify-content:center;padding:clamp(105px,15vh,138px) 0 clamp(52px,9vh,86px)}.landing-hero-inner{justify-items:start;width:min(1120px,calc(100% - 40px));text-align:left}.landing-lede{margin:22px 0 0}@media(max-width:820px){.landing-hero{padding:105px 0 55px}}@media(max-width:520px){.landing-shell,.landing-hero-inner{width:calc(100% - 28px)}.landing-hero{padding:105px 0 48px}}.landing-page{width:100%;max-width:100%;margin:0;overflow-x:clip}</style>

<div class="landing-page">
    <section class="landing-hero" aria-labelledby="landing-title">
        <div class="landing-shell landing-hero-inner">
            <div>
                <p class="landing-kicker landing-reveal">Sultan Kudarat State University</p>
                <p class="landing-brand landing-reveal">SKSU ICT</p>
                <h1 id="landing-title" class="landing-reveal-delay">One place for campus technology services.</h1>
                <p class="landing-lede landing-reveal-delay">Submit requests, register for your Campus ID, and follow every service ticket from a single official portal.</p>
                <div class="landing-actions landing-reveal-delay"><a class="button landing-primary" href="<?= site_url('id') ?>">Start a request</a><a class="button landing-secondary" href="<?= site_url('id') ?>?request=track">Track a request</a></div>
            </div>
        </div>
    </section>

    <section class="landing-section">
        <div class="landing-shell landing-intro">
            <div><p class="landing-eyebrow">The service portal</p><h2>Clear, accountable support for essential campus services.</h2></div>
            <div class="landing-copy"><p>The SKSU ICT Service Portal brings together common student and office ICT transactions. It is designed to make submissions straightforward, provide a clear next step, and keep request status easy to check.</p><p>Choose a service below to begin. Staff access is available separately for authorized personnel.</p></div>
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
        <div class="landing-shell landing-cta-inner">
            <div><p class="landing-eyebrow" style="color:#a8e9bd">Ready when you are</p><h2>Start with the service you need today.</h2><p>Campus ID registration links appear only while an approved registration period is active.</p></div>
        </div>
        <div class="landing-shell"><p class="landing-footer-note">For official SKSU ICT services. Keep your request reference number for follow-up.</p></div>
    </section>
</div>
<?= $this->endSection() ?>
