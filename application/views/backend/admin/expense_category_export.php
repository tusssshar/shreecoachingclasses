<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$report_no = 'EXP-' . $year;
$today = date('d M Y');
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Expense Report <?php echo htmlspecialchars($year); ?> &mdash; <?php echo htmlspecialchars($school['name']); ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eef0f4;
            margin: 0;
            padding: 30px 10px;
            color: #222;
        }
        .report {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d0d4dc;
            border-radius: 8px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.10);
            overflow: hidden;
        }
        .report-header {
            background: linear-gradient(135deg, #1f3a68 0%, #2c5298 100%);
            color: #fff;
            padding: 22px 28px 18px;
            position: relative;
        }
        .report-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #fff !important;
            text-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }
        .report-header .school-meta {
            font-size: 12px;
            opacity: 0.95;
            margin-top: 4px;
            line-height: 1.5;
            color: #fff;
        }
        .report-header .report-tag {
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
        .report-header::after {
            content: "";
            display: block;
            height: 4px;
            background: #f5b921;
            margin: 16px -28px -18px;
        }

        .body { padding: 22px 28px; }
        .meta-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 13px;
            line-height: 1.65;
        }
        .meta-grid .label {
            font-weight: 600;
            color: #1f3a68;
            display: inline-block;
            min-width: 90px;
        }

        h3.section-title {
            font-size: 14px;
            color: #1f3a68;
            border-bottom: 2px solid #f5b921;
            padding-bottom: 4px;
            margin: 22px 0 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        table.items th {
            background: #1f3a68;
            color: #fff;
            text-align: left;
            padding: 10px;
            font-weight: 600;
        }
        table.items td {
            border-bottom: 1px solid #e3e6ec;
            padding: 9px 10px;
        }
        table.items tr:nth-child(even) td { background: #f8f9fb; }
        table.items td.amount { text-align: right; font-weight: 600; color: #1f3a68; }
        table.items tfoot td {
            background: #1f3a68;
            color: #fff;
            font-weight: 700;
            padding: 11px 10px;
        }
        table.items tfoot td.amount { color: #fff; font-size: 15px; }

        .empty-notice {
            font-size: 13px;
            color: #777;
            background: #f8f9fb;
            border: 1px dashed #c0c0c0;
            padding: 16px;
            text-align: center;
            border-radius: 4px;
        }

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
        .footer-block .meta {
            font-style: italic;
            color: #1f3a68;
        }

        .toolbar {
            max-width: 800px;
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
            .report { box-shadow: none; border: 1px solid #1f3a68; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">Print Report</button>
    <button class="secondary" onclick="window.close()">Close</button>
</div>

<div class="report">
    <div class="report-header">
        <span class="report-tag">EXPENSE REPORT</span>
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
            <div>
                <div><span class="label">Report No.</span> <strong><?php echo htmlspecialchars($report_no); ?></strong></div>
                <div><span class="label">Generated</span> <strong><?php echo $today; ?></strong></div>
            </div>
            <div style="text-align:right;">
                <div><span class="label">Financial Year</span> <strong style="font-size:18px; color:#1f3a68;"><?php echo htmlspecialchars($year); ?></strong></div>
                <div><span class="label">Categories</span> <strong><?php echo count($rows); ?></strong></div>
            </div>
        </div>

        <h3 class="section-title">Expense Categories &mdash; <?php echo htmlspecialchars($year); ?></h3>

        <?php if (empty($rows)): ?>
            <div class="empty-notice">
                No expense category records found for year <strong><?php echo htmlspecialchars($year); ?></strong>.
            </div>
        <?php else: ?>
            <table class="items">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Category Name</th>
                        <th style="width:90px;">Year</th>
                        <th style="width:160px; text-align:right;">Amount (&#8377;)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $r): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['year'] ?? '-'); ?></td>
                            <td class="amount"><?php echo number_format(floatval($r['amount']), 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;">Total Expenses</td>
                        <td class="amount">&#8377; <?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>

        <div class="footer-block">
            <div class="meta">
                System-generated report &middot; verified by school administration.
            </div>
            <div class="signature">
                <div class="line">Authorized Signature</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
