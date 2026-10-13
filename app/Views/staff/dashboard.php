<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<header class="page-heading">
    <div>
        <h1>Staff workspace</h1>
        <p class="muted">Campus activity and current operational queues.</p>
    </div>
    <form class="workspace-filters" aria-label="Campus statistics filters"><label>Campus<select data-campus-chart-filter>
                <option value="">All campuses</option><?php foreach ($campusStats as $index => $stat): ?><option value="<?= esc((string) $index) ?>"><?= esc($stat['name']) ?></option><?php endforeach ?>
            </select></label><label>Metric<select data-series-chart-filter>
                <option value="">All metrics</option>
                <option value="students">Students</option>
                <option value="requests">ID requests</option>
                <option value="accounts">Accounts</option>
            </select></label></form>
</header>
<?php
$campusMax = 1;
foreach ($campusStats as $stat) $campusMax = max($campusMax, $stat['students'], $stat['requests'], $stat['accounts']);
$barGroupWidth = 1100 / max(1, count($campusStats));
?>
<section class="panel statistics-workspace" aria-labelledby="campus-statistics-title">
    <header class="statistics-head">
        <div>
            <h2 id="campus-statistics-title">Campus statistics</h2>
            <p class="muted">Registered students, ID requests, and provisioned institutional accounts.</p>
        </div>
        <div class="chart-legend" aria-label="Chart legend"><span><i class="legend-students"></i>Students</span><span><i class="legend-requests"></i>ID requests</span><span><i class="legend-accounts"></i>Accounts</span></div>
    </header>
    <div class="campus-chart-scroll"><svg class="campus-chart" viewBox="0 0 1200 390" role="img" aria-label="Campus comparison chart">
            <line x1="64" y1="316" x2="1160" y2="316" class="chart-axis" />
            <line x1="64" y1="76" x2="64" y2="316" class="chart-axis" />
            <?php for ($line = 0; $line <= 4; $line++): $y = 316 - ($line * 60);
                $value = (int) round(($campusMax / 4) * $line); ?>
                <line x1="64" y1="<?= $y ?>" x2="1160" y2="<?= $y ?>" class="chart-gridline" />
                <text x="52" y="<?= $y + 4 ?>" text-anchor="end" class="chart-tick"><?= esc((string) $value) ?></text>
            <?php endfor ?>
            <?php foreach ($campusStats as $index => $stat):
                $start = 75 + ($index * $barGroupWidth);
                $bars = [['students', '#0a9f54'], ['requests', '#2563db'], ['accounts', '#d9469c']];
            ?>
                <g data-campus-chart-group data-campus-index="<?= esc((string) $index) ?>">
                    <?php foreach ($bars as $barIndex => [$key, $color]): $height = round(($stat[$key] / $campusMax) * 240);
                        $x = $start + ($barIndex * 19);
                        $y = 316 - $height; ?>
                        <rect data-chart-series="<?= esc($key) ?>" x="<?= esc((string) $x) ?>" y="<?= esc((string) $y) ?>" width="14" height="<?= esc((string) $height) ?>" rx="3" fill="<?= $color ?>">
                            <title><?= esc($stat['name']) ?>: <?= esc(ucfirst($key)) ?> <?= esc((string) $stat[$key]) ?></title>
                        </rect>
                    <?php endforeach ?>
                    <text x="<?= esc((string) ($start + 22)) ?>" y="342" text-anchor="end" class="chart-campus" transform="rotate(-28 <?= esc((string) ($start + 22)) ?> 342)"><?= esc($stat['name']) ?></text>
                </g>
            <?php endforeach ?>
        </svg></div>
</section>
<section class="panel queue-summary" aria-labelledby="queue-summary-title">
    <header class="statistics-head">
        <div>
            <h2 id="queue-summary-title">Queue statistics</h2>
            <p class="muted">Items that still need staff attention.</p>
        </div>
    </header>
    <div class="queue-stat-list"><a href="<?= site_url('staff/ids') ?>"><span>ID production queue</span><strong><?= esc((string) $counts['id']) ?></strong><small>Open queue</small></a><a href="<?= site_url('staff/resets') ?>"><span>Password reset tickets</span><strong><?= esc((string) $counts['reset']) ?></strong><small>Open queue</small></a><a href="<?= site_url('staff/repairs') ?>"><span>Hardware repair jobs</span><strong><?= esc((string) $counts['repair']) ?></strong><small>Open queue</small></a></div>
</section>
<?= $this->endSection() ?>