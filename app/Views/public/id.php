<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<?php
$registrationLinks = $registrationLinks ?? [];
$campuses = $campuses ?? [];
$ref = $ref ?? '';
$ticket = $ticket ?? null;
$activeRequest = $activeRequest ?? '';
?>
<style>
    .send-request-section{max-width:820px;margin-inline:auto}.send-request-head{max-width:680px;margin:0 auto 26px;text-align:center}.send-request-eyebrow{margin:0 0 10px;color:var(--forest);font-size:11px;font-weight:850;letter-spacing:.12em;text-transform:uppercase}.send-request-head h1{margin:0;font-size:clamp(20px,2.6vw,30px);line-height:1.12;letter-spacing:-.035em;white-space:nowrap}.send-request-picker{display:grid;gap:8px;max-width:460px;margin:0 auto 24px}.send-request-picker span{color:var(--forest);font-size:11px;font-weight:850;letter-spacing:.12em;text-transform:uppercase}.send-request-picker select{min-height:50px;padding:12px 14px;border:1px solid #9fb8aa;border-radius:8px;background:#fff;font-size:15px;font-weight:600;box-shadow:0 1px 3px rgba(21,35,30,.06)}.send-request-panel{padding:clamp(18px,3vw,28px);border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 8px 24px rgba(21,35,30,.05)}.send-request-panel[hidden]{display:none}.send-request-panel>h2{margin:0;font-size:19px;letter-spacing:-.02em}.send-request-panel>.muted{margin:4px 0 0;font-size:13px}.send-request-panel .inline-request-form{margin-top:18px}.send-request-panel .registration-link-list,.send-request-panel .registration-empty{margin-top:18px}.send-request-track-form{display:flex;flex-wrap:wrap;gap:10px;align-items:end;margin-top:18px}.send-request-track-form label{flex:1 1 260px}.send-request-track-form .button{min-height:40px;min-width:130px}.send-request-track-result{margin-top:20px;padding-top:18px;border-top:1px solid #dcebe2}.send-request-track-result h3{margin:0 0 12px;font-size:16px}@media(max-width:700px){.send-request-head h1{white-space:normal}}@media(max-width:520px){.send-request-picker{max-width:none}.send-request-track-form .button{width:100%}}
</style>

<?php
$requestChoices = [
    'id' => 'Campus ID registration',
    'repair' => 'Hardware repair request',
    'reset' => 'Password reset request',
    'replacement' => 'Lost-ID replacement request',
    'track' => 'Request tracking',
];
$selectedRequest = old('form_type') ?: ($ref !== '' ? 'track' : ($activeRequest ?? ''));
if (! array_key_exists($selectedRequest, $requestChoices)) {
    $selectedRequest = '';
}
?>
<section class="send-request-section" id="submit-request" aria-labelledby="submit-request-title">
    <div class="send-request-head">
        <p class="send-request-eyebrow">Submit a request</p>
        <h1 id="submit-request-title">Choose a request and the form appears right here.</h1>
    </div>
    <div class="send-request-picker">
        <span id="request-picker-label">Request type</span>
        <select id="request-picker" aria-labelledby="request-picker-label" aria-controls="send-request-panels">
            <option value="" <?= $selectedRequest === '' ? 'selected' : '' ?>>Select a request&hellip;</option>
            <?php foreach ($requestChoices as $requestKey => $requestLabel): ?>
                <option value="<?= esc($requestKey) ?>" <?= $selectedRequest === $requestKey ? 'selected' : '' ?>><?= esc($requestLabel) ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div id="send-request-panels">
        <div class="send-request-panel" data-request-panel="id" <?= $selectedRequest === 'id' ? '' : 'hidden' ?>>
            <h2>Campus ID registration</h2>
            <p class="muted">Choose an available ID registration link for your campus.</p>
            <?php if ($registrationLinks === []): ?>
                <div class="registration-empty" role="status">
                    <h2>No registration links are open</h2>
                    <p class="muted">Your campus Registrar will publish a link here when registration is available.</p>
                </div>
            <?php else: ?>
                <div class="registration-link-list">
                    <?php foreach ($registrationLinks as $registrationLink): ?>
                        <a class="registration-link" href="<?= site_url('register/' . $registrationLink['token']) ?>">
                            <span class="registration-link-campus"><?= esc($registrationLink['campus_name']) ?></span>
                            <span class="registration-link-detail">School year <?= esc($registrationLink['school_year']) ?></span>
                            <span class="registration-link-action">Register <span aria-hidden="true">&rarr;</span></span>
                        </a>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>

        <div class="send-request-panel" data-request-panel="repair" <?= $selectedRequest === 'repair' ? '' : 'hidden' ?>>
            <h2>Hardware repair request</h2>
            <p class="muted">Provide the equipment details and ICT will issue a tracking reference.</p>
            <form method="post" action="<?= site_url('repair-request') ?>" class="grid inline-request-form">
                <?= csrf_field() ?>
                <input type="hidden" name="form_type" value="repair">
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
        </div>

        <div class="send-request-panel" data-request-panel="reset" <?= $selectedRequest === 'reset' ? '' : 'hidden' ?>>
            <h2>Password reset request</h2>
            <p class="muted">ICT verifies identity before resetting any account.</p>
            <form method="post" action="<?= site_url('reset-request') ?>" class="grid inline-request-form">
                <?= csrf_field() ?>
                <input type="hidden" name="form_type" value="reset">
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
        </div>

        <div class="send-request-panel" data-request-panel="replacement" <?= $selectedRequest === 'replacement' ? '' : 'hidden' ?>>
            <h2>Lost-ID replacement request</h2>
            <p class="muted">Submit a replacement request for your registered Campus ID. Payment details will be recorded by the authorized cashier.</p>
            <form method="post" action="<?= site_url('replacement-request') ?>" class="grid inline-request-form">
                <?= csrf_field() ?>
                <input type="hidden" name="form_type" value="replacement">
                <label>Student number
                    <input name="student_no" value="<?= esc(old('student_no')) ?>" required>
                </label>
                <label>Campus
                    <select name="campus_id" required>
                        <option value="">Select campus</option>
                        <?php foreach (($campuses ?? []) as $campus): ?>
                            <option value="<?= $campus['id'] ?>" <?= (string) old('campus_id') === (string) $campus['id'] ? 'selected' : '' ?>><?= esc($campus['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </label>
                <div class="full form-actions">
                    <button class="button" type="submit">Submit replacement request</button>
                </div>
            </form>
        </div>

        <div class="send-request-panel" data-request-panel="track" <?= $selectedRequest === 'track' ? '' : 'hidden' ?>>
            <h2>Request tracking</h2>
            <p class="muted">Enter the reference number issued after your submission.</p>
            <form method="get" action="<?= site_url('id') ?>" class="send-request-track-form">
                <input type="hidden" name="request" value="track">
                <label for="track-ref">Reference number
                    <input id="track-ref" name="ref" value="<?= esc($ref) ?>" placeholder="ID-..., IDR-..., ACC-..., RPR-..., or RST-..." required>
                </label>
                <button class="button" type="submit">Check status</button>
            </form>
            <?php if ($ref !== '' && ! $ticket): ?><div hidden data-swal-alert data-swal-icon="error" data-swal-title="Request not found" data-swal-text="No request matches that reference number."></div><?php endif ?>
            <?php if ($ticket): ?>
                <div class="send-request-track-result">
                    <h3><?= esc($ticket['reference_no']) ?> <span class="chip"><?= esc($ticket['status']) ?></span></h3>
                    <div class="grid">
                        <p>Submitted<br><strong><?= esc(format_manila_datetime($ticket['created_at'])) ?></strong></p>
                        <?php if (isset($ticket['campus'])): ?><p>Campus<br><strong><?= esc($ticket['campus']) ?></strong></p><?php endif ?>
                        <?php if (! empty($ticket['released_at'])): ?><p>Released<br><strong><?= esc(format_manila_datetime($ticket['released_at'])) ?></strong></p><?php endif ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</section>
<script>
(function () {
    const picker = document.getElementById('request-picker');
    const section = document.getElementById('submit-request');
    const panels = document.querySelectorAll('[data-request-panel]');
    if (!picker || !section || panels.length === 0) return;

    const showPanel = (key) => {
        panels.forEach((panel) => { panel.hidden = panel.dataset.requestPanel !== key; });
    };

    picker.addEventListener('change', () => {
        showPanel(picker.value);
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        const visible = document.querySelector('[data-request-panel]:not([hidden])');
        visible?.querySelector('input, select, textarea, a, button')?.focus({ preventScroll: true });
    });

    if (picker.value) {
        showPanel(picker.value);
        section.scrollIntoView({ block: 'start' });
    }
})();
</script>
<?= $this->endSection() ?>
