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
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef0f4;
            margin: 0;
            padding: 30px 10px;
            color: #222;
        }
        .receipt {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d0d4dc;
            border-radius: 8px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.10);
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #1f3a68 0%, #2c5298 100%);
            color: #fff;
            padding: 22px 28px 18px;
            position: relative;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #fff !important;
            text-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }
        .receipt-header .school-meta {
            font-size: 12px;
            opacity: 0.95;
            margin-top: 4px;
            line-height: 1.5;
        }
        .receipt-header .receipt-tag {
            position: absolute;
            top: 22px;
            right: 28px;
            background: #f5b921;
            color: #1f3a68;
            font-weight: 800;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 14px;
            letter-spacing: 0.6px;
        }
        .receipt-header::after {
            content: "";
            display: block;
            height: 4px;
            background: #f5b921;
            margin: 16px -28px -18px;
        }

        .badge-paid {
            display: inline-block;
            background: #27ae60;
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1px;
        }
        .badge-due {
            display: inline-block;
            background: #c0392b;
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1px;
        }

        .body { padding: 22px 28px; }
        .meta-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }
        .meta-block {
            min-width: 220px;
            font-size: 13px;
            line-height: 1.65;
        }
        .meta-block .label {
            font-weight: 600;
            color: #1f3a68;
            display: inline-block;
            min-width: 95px;
        }
        .meta-block strong { color: #222; }

        h3.section-title {
            font-size: 14px;
            color: #1f3a68;
            border-bottom: 2px solid #f5b921;
            padding-bottom: 4px;
            margin: 22px 0 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        table.payments {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        table.payments th {
            background: #1f3a68;
            color: #fff;
            text-align: left;
            padding: 8px 10px;
            font-weight: 600;
        }
        table.payments td {
            border-bottom: 1px solid #e3e6ec;
            padding: 8px 10px;
        }
        table.payments tr:nth-child(even) td { background: #f8f9fb; }
        table.payments td.amount { text-align: right; font-weight: 600; color: #1f3a68; }

        .totals {
            margin-top: 16px;
            border-top: 2px solid #1f3a68;
            padding-top: 14px;
            display: flex;
            justify-content: flex-end;
        }
        .totals .totals-table {
            min-width: 320px;
            font-size: 14px;
        }
        .totals .totals-table .row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .totals .totals-table .row.grand {
            border-top: 1px dashed #c0c0c0;
            margin-top: 6px;
            padding-top: 8px;
            font-size: 16px;
            font-weight: 700;
            color: #1f3a68;
        }
        .totals .totals-table .row .l { color: #555; }
        .totals .totals-table .row .v { font-weight: 600; }

        .footer-block {
            margin-top: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 12px;
            color: #555;
        }
        .footer-block .signature {
            text-align: center;
            min-width: 200px;
        }
        .footer-block .signature .line {
            border-top: 1px solid #333;
            margin-top: 36px;
            padding-top: 4px;
        }
        .footer-block .thanks {
            font-style: italic;
            color: #1f3a68;
        }

        .notice {
            font-size: 11px;
            color: #777;
            margin-top: 22px;
            border-top: 1px dashed #ccc;
            padding-top: 8px;
            line-height: 1.5;
        }

        .toolbar {
            max-width: 760px;
            margin: 0 auto 14px;
            text-align: right;
        }
        .toolbar button {
            background: #1f3a68;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            margin-left: 6px;
        }
        .toolbar button.secondary { background: #555; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .receipt { box-shadow: none; border: 1px solid #1f3a68; }
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
        <span class="receipt-tag">RECEIPT</span>
        <h1><?php echo strtoupper(htmlspecialchars($school['name'])); ?></h1>
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

            <div class="meta-block" style="text-align:right;">
                <div><span class="label">Student</span> <strong><?php echo htmlspecialchars($student['name']); ?></strong></div>
                <div><span class="label">Student ID</span> <strong>STU-<?php echo str_pad($student['student_id'], 5, '0', STR_PAD_LEFT); ?></strong></div>
                <div><span class="label">Class</span> <strong><?php echo htmlspecialchars($student['standard'] ?: ($student['class_id'] ?: '-')); ?></strong></div>
                <div><span class="label">Father</span> <strong><?php echo htmlspecialchars($student['father_name'] ?: '-'); ?></strong></div>
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
