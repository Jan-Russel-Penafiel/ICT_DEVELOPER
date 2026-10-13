<?= $this->extend('layouts/base') ?><?= $this->section('content') ?><header class="page-heading">
    <div>
        <h1>Settings</h1>
        <p class="muted">Manage the operational defaults used by ICT service workflows.</p>
    </div><button type="button" class="button" data-modal-open="settings-modal">Edit settings</button>
</header>
<section class="table-workspace">
    <header class="table-toolbar">
        <div class="table-title">
            <h2>Current configuration</h2>
        </div>
    </header>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Setting</th>
                    <th>Current value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Replacement fee</td>
                    <td>₱<?= esc($settings['replacement_fee'] ?? '100') ?></td>
                </tr>
                <tr>
                    <td>Release offset</td>
                    <td><?= esc($settings['release_offset_months'] ?? '1') ?> month(s)</td>
                </tr>
                <tr>
                    <td>Email domain</td>
                    <td><?= esc($settings['email_domain'] ?? '') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
<dialog id="settings-modal" class="modal" data-open-on-error>
    <header class="modal-header">
        <h2>Edit settings</h2><button type="button" class="modal-close" data-modal-close aria-label="Close">×</button>
    </header>
    <div class="modal-body">
        <form method="post" class="grid"><?= csrf_field() ?><label>Replacement fee<input type="number" step=".01" name="replacement_fee" value="<?= esc($settings['replacement_fee'] ?? '100') ?>" required></label><label>Release offset (months)<input type="number" name="release_offset_months" value="<?= esc($settings['release_offset_months'] ?? '1') ?>" required></label><label class="full">Email domain<input name="email_domain" value="<?= esc($settings['email_domain'] ?? '') ?>" placeholder="school.edu.ph" required></label>
            <div class="modal-footer full"><button type="button" class="button secondary" data-modal-close>Cancel</button><button class="button">Save settings</button></div>
        </form>
    </div>
</dialog><?= $this->endSection() ?>