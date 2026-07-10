<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payment Receipt — <?php echo htmlspecialchars($student['name']); ?></title>
<style>
    @page { size: A4 landscape; margin: 8mm; }
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; color: #222; margin: 0; padding: 0; background: #fff; font-size: 12px; }

    .page { width: 281mm; margin: 0 auto; display: flex; gap: 6mm; align-items: stretch; }
    .receipt {
        width: 50%;
        min-height: 185mm;
        border: 1.5px solid #333;
        padding: 7mm 8mm;
        page-break-inside: avoid;
        position: relative;
    }
    .receipt + .receipt { border-left-style: dashed; }
    .receipt::before {
        content: attr(data-copy);
        position: absolute; top: 4mm; right: 8mm;
        font-size: 10px; color: #888; letter-spacing: 1px;
        text-transform: uppercase; font-weight: bold;
    }

    .header { text-align: center; border-bottom: 1.5px solid #333; padding-bottom: 5px; margin-bottom: 6px; }
    .header h2 { margin: 0 0 2px; font-size: 15px; letter-spacing: .5px; }
    .header .addr { font-size: 10px; color: #555; }
    .header h3 { margin: 5px 0 0; font-size: 12px; color: #1f3a68; }

    .meta-tbl { width: 100%; border-collapse: collapse; margin-bottom: 4px; font-size: 10.5px; }
    .meta-tbl td { padding: 2px 4px; vertical-align: top; }
    .meta-tbl .label { color: #555; width: 28%; }

    .amount-row {
        margin-top: 5px; padding: 6px 8px;
        background: #f3f7ff; border: 1px solid #b9d2ff;
        font-size: 12px; font-weight: bold;
        display: flex; justify-content: space-between;
    }
    .amount-row strong { color: #1f3a68; }

    .footer-row { margin-top: 10px; display: flex; justify-content: space-between; font-size: 10px; color: #444; }
    .sign { text-align: center; }
    .sign .line { border-top: 1px solid #999; padding-top: 3px; width: 44mm; margin: 16px auto 0; }

    .actions { position: fixed; left: 0; right: 0; bottom: 4mm; text-align: center; margin: 0; }
    .actions button { padding: 8px 18px; font-size: 13px; cursor: pointer; }
    @media print {
        .actions { display: none !important; }
        body { padding: 0; }
        .page { width: 100%; }
    }
</style>
</head>
<body>

<?php
$copies = array('Student Copy', 'Office Copy');
$ts     = (int)($payment['timestamp'] ?? time());
?>

<div class="page">

<?php foreach ($copies as $copy_label): ?>
<div class="receipt" data-copy="<?php echo $copy_label; ?>">
    <div class="header">
        <h2><?php echo htmlspecialchars($school['name']); ?></h2>
        <?php if (!empty($school['address'])): ?>
            <div class="addr"><?php echo htmlspecialchars($school['address']); ?></div>
        <?php endif; ?>
        <h3>PAYMENT RECEIPT</h3>
    </div>

    <table class="meta-tbl">
        <tr>
            <td class="label">Receipt No.</td>
            <td><strong>RCP-<?php echo str_pad((int)$payment['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
            <td class="label">Date</td>
            <td><?php echo date('d M Y', $ts); ?></td>
        </tr>
        <tr>
            <td class="label">Student Name</td>
            <td><strong><?php echo htmlspecialchars($student['name']); ?></strong></td>
            <td class="label">Student ID</td>
            <td>STU-<?php echo str_pad((int)$student['student_id'], 5, '0', STR_PAD_LEFT); ?></td>
        </tr>
        <tr>
            <td class="label">Class / Standard</td>
            <td><?php echo htmlspecialchars($student['standard'] ?? '-'); ?></td>
            <td class="label">Academic Year</td>
            <td><?php echo htmlspecialchars($student['academic_year'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="label">Payment Type</td>
            <td><?php echo htmlspecialchars($payment['payment_type'] ?? '-'); ?></td>
            <td class="label">Payment Mode</td>
            <td><?php echo htmlspecialchars($payment['method'] ?? '-'); ?></td>
        </tr>
        <?php if (!empty($payment['transaction_id'])): ?>
        <tr>
            <td class="label">Transaction Ref</td>
            <td colspan="3"><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($payment['cheque_number'])): ?>
        <tr>
            <td class="label">Cheque No.</td>
            <td><?php echo htmlspecialchars($payment['cheque_number']); ?></td>
            <td class="label">Bank / Date</td>
            <td>
                <?php echo htmlspecialchars($payment['cheque_bank'] ?? '-'); ?>
                <?php if (!empty($payment['cheque_date'])): ?> / <?php echo htmlspecialchars($payment['cheque_date']); ?><?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($payment['description'])): ?>
        <tr>
            <td class="label">Description</td>
            <td colspan="3"><?php echo htmlspecialchars($payment['description']); ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="amount-row">
        <span>Amount Received</span>
        <strong>&#8377; <?php echo number_format((float)$payment['amount'], 2); ?></strong>
    </div>

    <table class="meta-tbl" style="margin-top:8px;">
        <tr>
            <td class="label">Total Fees</td>
            <td>&#8377; <?php echo number_format($total, 2); ?></td>
            <td class="label">Paid To Date</td>
            <td>&#8377; <?php echo number_format($paid, 2); ?></td>
        </tr>
        <tr>
            <td class="label">Balance Due</td>
            <td colspan="3"><strong style="color:#c0392b;">&#8377; <?php echo number_format($due, 2); ?></strong></td>
        </tr>
    </table>

    <div class="footer-row">
        <div class="sign">
            <div class="line">Received By</div>
        </div>
        <div class="sign">
            <div class="line">Authorised Signatory</div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div class="actions">
    <button onclick="window.print()">Print Receipt</button>
    <button onclick="window.close()">Close</button>
</div>

</div>

<script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 250); });</script>

</body>
</html>
