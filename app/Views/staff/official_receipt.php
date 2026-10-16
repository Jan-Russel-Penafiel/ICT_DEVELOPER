<!doctype html>
<?php $receipt = $receipt ?? []; ?>
<title>Official Receipt <?= esc($receipt['or_number'] ?? '') ?></title>
<style>
    body {
        color: #0f172a;
        font: 14px system-ui;
        margin: 35px;
    }

    header {
        border-bottom: 4px solid #047857;
    }

    table {
        border-collapse: collapse;
        margin-top: 25px;
        width: 100%;
    }

    td {
        border: 1px solid #94a3b8;
        padding: 12px;
    }

    @media print {
        button {
            display: none;
        }
    }
</style>
<header>
    <h1>Official Receipt</h1>
    <p>Receipt number: <strong><?= esc($receipt['or_number']) ?></strong></p>
</header>
<table>
    <tr>
        <td>Student number</td>
        <td><?= esc($receipt['student_no']) ?></td>
    </tr>
    <tr>
        <td>Student name</td>
        <td><?= esc($receipt['last_name'] . ', ' . $receipt['first_name'] . (! empty($receipt['middle_name']) ? ' ' . $receipt['middle_name'] : '')) ?></td>
    </tr>
    <tr>
        <td>Campus</td>
        <td><?= esc($receipt['campus']) ?></td>
    </tr>
    <tr>
        <td>Payment for</td>
        <td>Lost-ID replacement</td>
    </tr>
    <tr>
        <td>Payment date</td>
        <td><?= esc($receipt['paid_at']) ?></td>
    </tr>
    <tr>
        <td>Request reference</td>
        <td><?= esc($receipt['reference_no']) ?></td>
    </tr>
</table>
<p><button type="button" onclick="window.print()">Print Official Receipt</button></p>
