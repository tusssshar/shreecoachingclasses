<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$sr = $this->db->get_where('settings', array('type' => 'system_name'))->row();
$school_name = $sr ? $sr->description : 'School';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<title>Students Report — <?php echo date('d M Y'); ?></title>
<style>
@page { size: A4 landscape; margin: 10mm; }
body { font-family: Arial, sans-serif; color: #222; margin: 0; padding: 12px; font-size: 11.5px; }
.header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 10px; }
.header h2 { margin: 0; font-size: 18px; }
.header h3 { margin: 4px 0 0; font-size: 13px; color: #1f3a68; }
table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
th, td { border: 1px solid #999; padding: 4px 6px; }
th { background: #eee; text-align: left; }
.amt { text-align: right; }
.actions { text-align: center; margin: 12px 0; }
@media print { .actions { display: none !important; } body { padding: 0; } }
</style></head><body>
<div class="header">
    <h2><?php echo htmlspecialchars($school_name); ?></h2>
    <h3>STUDENTS REPORT
        <?php if (!empty($q)) echo '— search: "' . htmlspecialchars($q) . '"'; ?>
        — <?php echo date('d M Y'); ?>
    </h3>
</div>
<table>
<thead><tr>
<th>#</th><th>Student ID</th><th>Name</th><th>Standard</th><th>Academic Year</th>
<th>Sex</th><th>Father Mobile</th><th>Email</th>
<th class="amt">Total Fees</th><th class="amt">Paid</th><th class="amt">Balance</th>
</tr></thead><tbody>
<?php $i=1; foreach ($rows as $r): $total=(float)$r['total_fees']; $paid=(float)$r['payment_done']; ?>
<tr>
    <td><?php echo $i++; ?></td>
    <td>STU-<?php echo str_pad((int)$r['student_id'],5,'0',STR_PAD_LEFT); ?></td>
    <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
    <td><?php echo htmlspecialchars($r['standard']); ?></td>
    <td><?php echo htmlspecialchars($r['academic_year']??'-'); ?></td>
    <td><?php echo htmlspecialchars($r['sex']); ?></td>
    <td><?php echo htmlspecialchars($r['fmobile']); ?></td>
    <td><?php echo htmlspecialchars($r['email']); ?></td>
    <td class="amt"><?php echo number_format($total,2); ?></td>
    <td class="amt"><?php echo number_format($paid,2); ?></td>
    <td class="amt"><?php echo number_format($total-$paid,2); ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<div class="actions">
    <button type="button" onclick="window.print()">Print</button>
    <button type="button" onclick="window.close()">Close</button>
</div>
<script>window.addEventListener('load',function(){setTimeout(function(){window.print();},250);});</script>
</body></html>
