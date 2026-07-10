<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$jd = $teacher['joining_date'] ?? '';
$jd_display = ($jd && $jd !== '0000-00-00') ? date('D, d M Y', strtotime($jd)) : '-';
$slip_no = 'SAL-' . str_pad($teacher['teacher_id'], 6, '0', STR_PAD_LEFT) . '-' . preg_replace('/[^0-9]/', '', $month_name);
$today = date('d M Y');
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salary Slip <?php echo htmlspecialchars($slip_no); ?> &mdash; <?php echo htmlspecialchars($teacher['name']); ?></title>
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
    <button onclick="window.print()">Print Salary Slip</button>
    <button class="secondary" onclick="window.close()">Close</button>
</div>

<div class="receipt">
    <div class="receipt-header">
        <h1><?php echo strtoupper(htmlspecialchars($school['name'])); ?></h1>
        <div class="receipt-subtag">SALARY SLIP</div>
        <div class="school-meta">
            <?php
                $sm_bits = array();
                if (!empty($school['phone']))   { $sm_bits[] = 'Phone: ' . htmlspecialchars($school['phone']); }
                if (!empty($school['email']))   { $sm_bits[] = 'Email: ' . htmlspecialchars($school['email']); }
                if (!empty($school['website'])) { $sm_bits[] = htmlspecialchars($school['website']); }
            ?>
            <?php if (!empty($school['address'])): ?>
                <?php echo htmlspecialchars($school['address']); ?><?php if (!empty($sm_bits)) echo '<br>'; ?>
            <?php endif; ?>
            <?php echo implode(' &nbsp;|&nbsp; ', $sm_bits); ?>
        </div>
    </div>

    <div class="body">

        <div class="meta-grid">
            <div class="meta-block">
                <div><span class="label">Name</span> <strong><?php echo htmlspecialchars($teacher['name']); ?></strong></div>
                <div>
                    <span class="label">Employee ID</span> <strong>TCH-<?php echo str_pad($teacher['teacher_id'], 4, '0', STR_PAD_LEFT); ?></strong>
                    <span class="sep">|</span>
                    <span class="label" style="min-width:80px;">Designation</span> <strong><?php echo htmlspecialchars($teacher['designation'] ?? '-'); ?></strong>
                </div>
                <div>
                    <span class="label">Joining</span> <strong><?php echo $jd_display; ?></strong>
                </div>
                <div>
                    <span class="label">PAN</span> <strong><?php echo htmlspecialchars($teacher['pan_number'] ?? '-'); ?></strong>
                    <span class="sep">|</span>
                    <span class="label" style="min-width:65px;">Bank A/C</span> <strong><?php echo htmlspecialchars($teacher['bank_account'] ?? '-'); ?></strong>
                </div>
            </div>

            <div class="meta-block">
                <div><span class="label">Slip No.</span> <strong><?php echo htmlspecialchars($slip_no); ?></strong></div>
                <div><span class="label">Pay Period</span> <strong><?php echo htmlspecialchars($month_name); ?></strong></div>
                <div><span class="label">Issued On</span> <strong><?php echo $today; ?></strong></div>
                <div>
                    <span class="label">Days in Month</span> <strong><?php echo (int)$month_days; ?></strong>
                    <span class="sep">|</span>
                    <span class="label" style="min-width:85px;">Days Worked</span> <strong><?php echo $days_worked; ?></strong>
                </div>
            </div>
        </div>

        <h3 class="section-title">Earnings &amp; Deductions</h3>
        <table class="payments">
            <thead>
                <tr>
                    <th>Earnings</th>
                    <th style="width:120px; text-align:right;">Amount (&#8377;)</th>
                    <th>Deductions</th>
                    <th style="width:120px; text-align:right;">Amount (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $e_keys = array_keys($earnings);
                $d_keys = array_keys($deductions);
                $rows = max(count($e_keys), count($d_keys));
                for ($i = 0; $i < $rows; $i++):
                    $ek = $e_keys[$i] ?? '';
                    $dk = $d_keys[$i] ?? '';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ek); ?></td>
                        <td class="amount"><?php echo $ek !== '' ? number_format($earnings[$ek], 2) : ''; ?></td>
                        <td><?php echo htmlspecialchars($dk); ?></td>
                        <td class="amount"><?php echo $dk !== '' ? number_format($deductions[$dk], 2) : ''; ?></td>
                    </tr>
                <?php endfor; ?>
                <tr style="background:#1f3a68; color:#fff; font-weight:700;">
                    <td style="padding:6px;">CTC (Gross Earnings)</td>
                    <td class="amount" style="color:#fff; padding:6px;"><?php echo number_format($gross, 2); ?></td>
                    <td style="padding:6px;">Total Deductions</td>
                    <td class="amount" style="color:#fff; padding:6px;"><?php echo number_format($total_deduction, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-table">
                <div class="row"><span class="l">CTC (Cost to Company)</span><span class="v">&#8377; <?php echo number_format($gross, 2); ?></span></div>
                <div class="row"><span class="l">Total Deductions</span><span class="v">&#8377; <?php echo number_format($total_deduction, 2); ?></span></div>
                <div class="row grand">
                    <span class="l">Net Take-Home Salary</span>
                    <span class="v">&#8377; <?php echo number_format($net, 2); ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($remarks)): ?>
            <h3 class="section-title">Remarks</h3>
            <p style="font-size:11.5px; color:#444; margin:0;"><?php echo htmlspecialchars($remarks); ?></p>
        <?php endif; ?>

        <div class="footer-block">
            <div class="thanks">
                Earnings are pro-rated by days worked.
            </div>
            <div class="signature">
                <div class="line">Authorised Signatory</div>
            </div>
        </div>

        <div class="notice">
            * This is a system-generated salary slip. For any queries, contact HR / Accounts.<br>
            * PF and Tax shown are fixed monthly deductions; other allowances are calculated as a percentage of Basic.
        </div>

    </div>
</div>

<?php if (!empty($pdf)): ?>
<script>
    window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 250); });
</script>
<?php endif; ?>

</body>
</html>
