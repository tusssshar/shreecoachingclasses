<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip — <?php echo htmlspecialchars($teacher['name']); ?> — <?php echo htmlspecialchars($month_name); ?></title>
    <style>
        body { font-family: Arial, sans-serif; color:#222; margin: 0; padding: 24px; background:#fff; }
        .slip {
            max-width: 760px; margin: 0 auto;
            border: 1px solid #333; padding: 0;
        }
        .slip-header {
            text-align: center; padding: 14px 12px; border-bottom: 2px solid #333;
            background:#f5f5f5;
        }
        .slip-header h2 { margin:0; font-size: 22px; letter-spacing: 1px; }
        .slip-header .addr { font-size: 12px; color:#555; margin-top: 4px; }
        .slip-header h3 { margin: 8px 0 0; font-size: 16px; }

        .meta-table { width:100%; border-collapse: collapse; }
        .meta-table td { padding: 6px 12px; font-size: 13px; vertical-align: top; }
        .meta-table .label { color:#666; width: 130px; }

        .pay-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .pay-table th, .pay-table td { border: 1px solid #999; padding: 8px 10px; font-size: 13px; }
        .pay-table th { background: #eee; text-align: left; }
        .pay-table .amt { text-align: right; width: 130px; }
        .pay-section-title { background: #f6f6f6; font-weight: bold; }

        .summary {
            margin-top: 14px;
            font-size: 13px;
            border: 1px solid #333; padding: 10px;
        }
        .summary table { width: 100%; }
        .summary td { padding: 4px 8px; }
        .net-row { font-size: 15px; font-weight: bold; background:#eef7ee; }

        .sign-row { margin-top: 36px; font-size: 12px; }
        .sign-row td { padding-top: 30px; border-top: 1px solid #999; text-align: center; width: 50%; }

        .actions { text-align: center; margin-top: 20px; }
        .actions button { padding: 8px 16px; font-size: 14px; cursor: pointer; }

        @media print {
            .actions { display: none !important; }
            body { padding: 0; }
            .slip { border: 1px solid #333; }
        }
    </style>
</head>
<body>

<div class="slip">
    <div class="slip-header">
        <h2><?php echo htmlspecialchars($school['name']); ?></h2>
        <?php if (!empty($school['address'])): ?>
            <div class="addr"><?php echo htmlspecialchars($school['address']); ?></div>
        <?php endif; ?>
        <h3>Salary Slip — <?php echo htmlspecialchars($month_name); ?></h3>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Employee Name</td>
            <td><strong><?php echo htmlspecialchars($teacher['name']); ?></strong></td>
            <td class="label">Employee ID</td>
            <td>TCH-<?php echo str_pad($teacher['teacher_id'], 4, '0', STR_PAD_LEFT); ?></td>
        </tr>
        <?php
            $jd = $teacher['joining_date'] ?? '';
            $jd_display = ($jd && $jd !== '0000-00-00') ? date('d M Y', strtotime($jd)) : '-';
        ?>
        <tr>
            <td class="label">Designation</td>
            <td><?php echo htmlspecialchars($teacher['designation'] ?? '-'); ?></td>
            <td class="label">Joining Date</td>
            <td><?php echo $jd_display; ?></td>
        </tr>
        <tr>
            <td class="label">PAN</td>
            <td><?php echo htmlspecialchars($teacher['pan_number'] ?? '-'); ?></td>
            <td class="label">Bank A/C</td>
            <td><?php echo htmlspecialchars($teacher['bank_account'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="label">Days in Month</td>
            <td><?php echo (int)$month_days; ?></td>
            <td class="label">Days Worked</td>
            <td><strong><?php echo $days_worked; ?></strong></td>
        </tr>
    </table>

    <table class="pay-table">
        <thead>
            <tr>
                <th>Earnings</th>
                <th class="amt">Amount (&#8377;)</th>
                <th>Deductions</th>
                <th class="amt">Amount (&#8377;)</th>
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
                <td><?php echo $ek; ?></td>
                <td class="amt"><?php echo $ek !== '' ? number_format($earnings[$ek], 2) : ''; ?></td>
                <td><?php echo $dk; ?></td>
                <td class="amt"><?php echo $dk !== '' ? number_format($deductions[$dk], 2) : ''; ?></td>
            </tr>
        <?php endfor; ?>
            <tr class="pay-section-title">
                <td>CTC (Gross Earnings)</td>
                <td class="amt"><?php echo number_format($gross, 2); ?></td>
                <td>Total Deductions</td>
                <td class="amt"><?php echo number_format($total_deduction, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>CTC (Cost to Company)</td>
                <td style="text-align:right;">&#8377; <?php echo number_format($gross, 2); ?></td>
            </tr>
            <tr class="net-row">
                <td>Net Take-Home Salary</td>
                <td style="text-align:right;">&#8377; <?php echo number_format($net, 2); ?></td>
            </tr>
            <?php if (!empty($remarks)): ?>
                <tr>
                    <td>Remarks</td>
                    <td style="text-align:right;"><?php echo htmlspecialchars($remarks); ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <table class="sign-row">
        <tr>
            <td>Employee Signature</td>
            <td>Authorised Signatory</td>
        </tr>
    </table>
</div>

<div class="actions">
    <button onclick="window.print()">Print / Save as PDF</button>
    <button onclick="window.close()">Close</button>
</div>

<?php if (!empty($pdf)): ?>
<script>
    window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 250); });
</script>
<?php endif; ?>

</body>
</html>
