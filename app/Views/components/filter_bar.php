<?php
$tableId = (string) ($tableId ?? '');
$ariaLabel = (string) ($ariaLabel ?? 'Table filters');
$filters = $filters ?? [];
$summary = (string) ($summary ?? 'Matching records');
$count = (int) ($count ?? 0);
?>
<section class="panel filter-bar" aria-label="<?= esc($ariaLabel) ?>">
    <?php foreach ($filters as $filter): ?>
        <label><?= esc($filter['label']) ?>
            <select data-table-filter="<?= esc($tableId) ?>" data-column="<?= esc((string) $filter['column']) ?>"<?= ! empty($filter['exact']) ? ' data-exact="true"' : '' ?>>
                <?php foreach ($filter['options'] as $option): ?>
                    <option value="<?= esc((string) ($option['value'] ?? '')) ?>"><?= esc((string) $option['label']) ?></option>
                <?php endforeach ?>
            </select>
        </label>
    <?php endforeach ?>
    <div class="filter-summary">
        <span class="filter-summary-label"><?= esc($summary) ?></span>
        <strong data-filter-count="<?= esc($tableId) ?>"><?= $count ?> shown</strong>
    </div>
    <div class="filter-actions">
        <button type="button" class="button" data-apply-filters="<?= esc($tableId) ?>">Filter</button>
        <button type="button" class="button secondary" data-reset-filters="<?= esc($tableId) ?>">Reset</button>
    </div>
</section>
