<!doctype html>
<?php $request = $request ?? []; ?>
<title>Repair request <?= esc($request['request_no'] ?? '') ?></title>
<style>
    body {
        font: 14px system-ui;
        margin: 35px;
        color: #0f172a
    }

    header {
        border-bottom: 4px solid #047857
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px
    }

    td {
        border: 1px solid #94a3b8;
        padding: 12px
    }

    @media print {
        button {
            display: none
        }
    }
</style>
<header>
    <h1>ICT Hardware Repair Request</h1>
    <p>Tracking number: <strong><?= esc($request['request_no']) ?></strong></p>
</header>
<table>
    <tr>
        <td>Requester</td>
        <td><?= esc($request['requester']) ?></td>
    </tr>
    <tr>
        <td>Office</td>
        <td><?= esc($request['office']) ?></td>
    </tr>
    <tr>
        <td>Equipment</td>
        <td><?= esc($request['equipment']) ?></td>
    </tr>
    <tr>
        <td>Serial no.</td>
        <td><?= esc($request['serial_no']) ?></td>
    </tr>
    <tr>
        <td>Issue</td>
        <td><?= nl2br(esc($request['issue'])) ?></td>
    </tr>
</table>
<p><button onclick="print()">Print form</button></p>