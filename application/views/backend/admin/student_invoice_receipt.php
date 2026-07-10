<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function rec_method_label($code)
{
    $m = (string) $code;
    if ($m === '1' || strtolower($m) === 'cash')   return 'Cash';
    if ($m === '2' || strtolower($m) === 'check' || strtolower($m) === 'cheque') return 'Cheque';
    if ($m === '3' || strtolower($m) === 'card')   return 'Card';
    if (strtolower($m) === 'online') return 'Online';
    return ucfirst($m);
}

function rec_format_ts($ts)
{
    if (!$ts) return '';
    if (!ctype_digit((string)$ts)) {
        $tsx = strtotime($ts);
        return $tsx ? date('D, d M Y', $tsx) : '';
    }
    return date('D, d M Y', (int)$ts);
}

$is_fully_paid = ($due <= 0.001);
$receipt_no = 'RCPT-' . str_pad($invoice['invoice_id'], 6, '0', STR_PAD_LEFT);
$today = date('d M Y');
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt <?php echo htmlspecialchars($receipt_no); ?> &mdash; <?php echo htmlspecialchars($student['name']); ?></title>
    <style>
        @page { size: A4 portrait; margin: 8mm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef0f4;
            margin: 0;
            padding: 6px 8px;
            color: #222;
        }
        .receipt {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d0d4dc;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #1f3a68 0%, #2c5298 100%);
            color: #fff;
            padding: 10px 14px 8px;
            position: relative;
            text-align: center;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #fff !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.35);
        }
        .receipt-header .receipt-subtag {
            display: inline-block;
            margin-top: 3px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2.5px;
            color: #f5b921;
            text-transform: uppercase;
        }
        .receipt-header .school-meta {
            font-size: 12px;
            font-weight: 600;
            opacity: 0.98;
            margin-top: 3px;
            line-height: 1.45;
            color: #fff;
        }
        .receipt-header::after {
            content: "";
            display: block;
            height: 3px;
            background: #f5b921;
            margin: 8px -14px -8px;
        }

        .badge-paid, .badge-due {
            display: inline-block;
            color: #fff;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.8px;
        }
        .badge-paid { background: #27ae60; }
        .badge-due  { background: #c0392b; }

        .body { padding: 8px 14px; }
        .meta-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 18px;
            margin: 8px 0 6px;
            padding: 8px 10px;
            background: #f5f7fb;
            border: 1px solid #d6dde9;
            border-left: 4px solid #f5b921;
            border-radius: 4px;
        }
        .meta-block {
            flex: 1 1 0;
            min-width: 240px;
            font-size: 12px;
            line-height: 1.55;
            text-align: left;
        }
        .meta-block > div { margin: 0; }
        .meta-block .label {
            font-weight: 700;
            color: #1f3a68;
            display: inline-block;
            min-width: 95px;
        }
        .meta-block strong { color: #111; font-weight: 700; }
        .meta-block .sep { color: #9ca3af; margin: 0 6px; font-weight: 400; }

        h3.section-title {
            font-size: 13px;
            color: #1f3a68;
            border-bottom: 2px solid #f5b921;
            padding-bottom: 2px;
            margin: 6px 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.payments {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        table.payments th {
            background: #1f3a68;
            color: #fff;
            text-align: left;
            padding: 3px 6px;
            font-weight: 600;
        }
        table.payments td {
            border-bottom: 1px solid #e3e6ec;
            padding: 3px 6px;
        }
        table.payments tr:nth-child(even) td { background: #f8f9fb; }
        table.payments td.amount { text-align: right; font-weight: 600; color: #1f3a68; }

        .totals {
            margin-top: 4px;
            border-top: 2px solid #1f3a68;
            padding-top: 4px;
            display: flex;
            justify-content: flex-end;
        }
        .totals .totals-table {
            min-width: 300px;
            font-size: 11.5px;
        }
        .totals .totals-table .row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .totals .totals-table .row.grand {
            border-top: 1px dashed #c0c0c0;
            margin-top: 4px;
            padding-top: 5px;
            font-size: 13px;
            font-weight: 700;
            color: #1f3a68;
        }
        .totals .totals-table .row .l { color: #555; }
        .totals .totals-table .row .v { font-weight: 600; }

        .footer-block {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11.5px;
            color: #555;
        }
        .footer-block .signature {
            text-align: center;
            min-width: 200px;
        }
        .footer-block .signature .line {
            border-top: 1px solid #333;
            margin-top: 14px;
            padding-top: 2px;
        }
        .footer-block .thanks {
            font-style: italic;
            color: #1f3a68;
        }

        .notice {
            font-size: 10.5px;
            color: #777;
            margin-top: 5px;
            border-top: 1px dashed #ccc;
            padding-top: 4px;
            line-height: 1.4;
        }

        .toolbar {
            max-width: 760px;
            margin: 0 auto 8px;
            text-align: right;
        }
        .toolbar button {
            background: #1f3a68;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 12.5px;
            cursor: pointer;
            margin-left: 6px;
        }
        .toolbar button.secondary { background: #555; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .receipt { box-shadow: none; border: 1px solid #1f3a68; max-width: none; border-radius: 0; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">Print Receipt</button>
    <button class="secondary" onclick="window.close()">Close</button>
</div>

<div class="receipt">
    <div class="receipt-header">
        <h1><?php echo strtoupper(htmlspecialchars($school['name'])); ?></h1>
        <div class="receipt-subtag">RECEIPT</div>
        <div class="school-meta">
            <?php echo htmlspecialchars($school['address']); ?><br>
            Phone: <?php echo htmlspecialchars($school['phone']); ?>
            &nbsp;|&nbsp; Email: <?php echo htmlspecialchars($school['email']); ?>
            &nbsp;|&nbsp; <?php echo htmlspecialchars($school['website']); ?>
        </div>
    </div>

    <div class="body">

        <div class="meta-grid">
            <div class="meta-block">
                <div><span class="label">Name</span> <strong><?php echo htmlspecialchars($student['name']); ?></strong></div>
                <div>
                    <span class="label">Student ID</span> <strong>STU-<?php echo str_pad($student['student_id'], 5, '0', STR_PAD_LEFT); ?></strong>
                    <span class="sep">|</span>
                    <span class="label" style="min-width:50px;">Class</span> <strong><?php echo htmlspecialchars($student['standard'] ?: ($student['class_id'] ?: '-')); ?></strong>
                </div>
                <div><span class="label">Father</span> <strong><?php echo htmlspecialchars($student['father_name'] ?: '-'); ?></strong></div>
            </div>

            <div class="meta-block">
                <div><span class="label">Receipt No.</span> <strong><?php echo htmlspecialchars($receipt_no); ?></strong></div>
                <div><span class="label">Issued On</span> <strong><?php echo $today; ?></strong></div>
                <div><span class="label">Status</span>
                    <?php if ($is_fully_paid): ?>
                        <span class="badge-paid">FULLY PAID</span>
                    <?php else: ?>
                        <span class="badge-due">PENDING &nbsp;&#8377;<?php echo number_format($due, 2); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h3 class="section-title">Charge</h3>
        <table class="payments">
            <thead>
                <tr>
                    <th>Item / Title</th>
                    <th>Description</th>
                    <th style="width:140px; text-align:right;">Amount (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php echo htmlspecialchars($invoice['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($invoice['description'] ?: '-'); ?></td>
                    <td class="amount"><?php echo number_format(floatval($invoice['amount']), 2); ?></td>
                </tr>
            </tbody>
        </table>

        <h3 class="section-title">Payment History</h3>
        <?php if (empty($payments)): ?>
            <p style="font-size:13px; color:#777;">No payment records on file for this student.</p>
        <?php else: ?>
            <table class="payments">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th style="width:170px;">Date</th>
                        <th style="width:90px;">Method</th>
                        <th style="width:110px;">Type</th>
                        <th>Note</th>
                        <th style="width:120px; text-align:right;">Amount (&#8377;)</th>
                        <th style="width:130px; text-align:right;">Balance (&#8377;)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $running_paid = 0;
                        $total_amount_for_balance = floatval($invoice['amount']);
                    ?>
                    <?php foreach ($payments as $i => $p):
                        $running_paid += floatval($p['amount']);
                        $running_balance = max($total_amount_for_balance - $running_paid, 0);
                    ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo rec_format_ts($p['timestamp']); ?></td>
                            <td><?php echo htmlspecialchars(rec_method_label($p['method'])); ?></td>
                            <td><?php echo htmlspecialchars($p['payment_type'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($p['description'] ?? $p['title'] ?? ''); ?></td>
                            <td class="amount"><?php echo number_format(floatval($p['amount']), 2); ?></td>
                            <td class="amount" style="color:<?php echo $running_balance <= 0 ? '#27ae60' : '#c0392b'; ?>;">
                                <?php echo number_format($running_balance, 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background:#1f3a68; color:#fff; font-weight:700;">
                        <td colspan="5" style="text-align:right; padding:10px;">Total Paid</td>
                        <td class="amount" style="color:#fff; padding:10px;"><?php echo number_format($running_paid, 2); ?></td>
                        <td class="amount" style="color:#fff; padding:10px;"><?php echo number_format(max($total_amount_for_balance - $running_paid, 0), 2); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="totals">
            <div class="totals-table">
                <div class="row"><span class="l">Total Fees</span><span class="v">&#8377; <?php echo number_format(floatval($invoice['amount']), 2); ?></span></div>
                <div class="row"><span class="l">Total Paid</span><span class="v">&#8377; <?php echo number_format($paid, 2); ?></span></div>
                <div class="row grand">
                    <span class="l"><?php echo $is_fully_paid ? 'Balance' : 'Balance Due'; ?></span>
                    <span class="v">&#8377; <?php echo number_format(max($due, 0), 2); ?></span>
                </div>
            </div>
        </div>

        <div class="footer-block">
            <div class="thanks">
                <?php echo $is_fully_paid ? 'Thank you for your payment.' : 'Please clear the outstanding balance at your earliest convenience.'; ?>
            </div>
            <div class="signature">
                <div class="line">Authorized Signature</div>
            </div>
        </div>

        <div class="notice">
            * This is a system-generated receipt. Fees once paid are non-refundable and non-transferable as per institute policy.<br>
            * For any queries regarding this receipt, contact the institute office.
        </div>

    </div>
</div>

</body>
</html>
