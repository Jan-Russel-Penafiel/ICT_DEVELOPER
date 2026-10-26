<?php
$tableId = (string) ($tableId ?? '');
$title = (string) ($title ?? 'Records');
$count = (int) ($count ?? 0);
$searchPlaceholder = (string) ($searchPlaceholder ?? 'Search records');
?>
<header class="table-toolbar">
    <div class="table-title">
        <h2><?= esc($title) ?></h2>
        <span class="table-count" data-table-count="<?= esc($tableId) ?>"><?= $count ?> shown</span>
    </div>
    <div class="table-tools">
        <input class="table-search" type="search" data-table-search="<?= esc($tableId) ?>" placeholder="<?= esc($searchPlaceholder) ?>" aria-label="<?= esc($searchPlaceholder) ?>">
    </div>
</header>
