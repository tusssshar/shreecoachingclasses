<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
$school_row = $this->db->get_where('settings', array('type' => 'system_name'))->row();
$school_name = $school_row ? $school_row->description : 'School';
$address_row = $this->db->get_where('settings', array('type' => 'address'))->row();
$school_addr = $address_row ? $address_row->description : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Teachers Report — <?php echo date('d M Y'); ?></title>
<style>
@page { size: A4 landscape; margin: 10mm; }
body { font-family: Arial, sans-serif; color: #222; margin: 0; padding: 14px; font-size: 12px; }
.header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 14px; }
.header h2 { margin: 0; font-size: 20px; letter-spacing: 1px; }
.header .addr { font-size: 11px; color: #555; }
.header h3 { margin: 6px 0 0; font-size: 14px; color: #1f3a68; }
table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
th, td { border: 1px solid #999; padding: 5px 7px; text-align: left; }
th { background: #eee; }
.amt { text-align: right; }
.actions { text-align: center; margin: 14px 0; }
.actions button { padding: 8px 18px; font-size: 13px; cursor: pointer; }
@media print { .actions { display: none !important; } body { padding: 0; } }
</style>
</head>
<body>

<div class="header">
    <h2><?php echo htmlspecialchars($school_name); ?></h2>
    <?php if (!empty($school_addr)): ?><div class="addr"><?php echo htmlspecialchars($school_addr); ?></div><?php endif; ?>
    <h3>TEACHERS REPORT
        <?php if (!empty($q)): ?>— search: "<?php echo htmlspecialchars($q); ?>"<?php endif; ?>
        — <?php echo date('d M Y'); ?>
    </h3>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Teacher ID</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Sex</th>
            <th>Blood</th>
            <th>Joining Date</th>
            <th class="amt">Basic</th>
            <th class="amt">Net Salary</th>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1; foreach ($rows as $r):
        $jd = $r['joining_date'] ?? '';
        $jd_display = ($jd && $jd !== '0000-00-00') ? date('d M Y', strtotime($jd)) : '-';
    ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td>TCH-<?php echo str_pad((int)$r['teacher_id'], 4, '0', STR_PAD_LEFT); ?></td>
            <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
            <td><?php echo htmlspecialchars($r['designation'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($r['email']); ?></td>
            <td><?php echo htmlspecialchars($r['phone']); ?></td>
            <td><?php echo htmlspecialchars($r['sex']); ?></td>
            <td><?php echo htmlspecialchars($r['blood_group'] ?? '-'); ?></td>
            <td><?php echo $jd_display; ?></td>
            <td class="amt"><?php echo number_format((float)($r['basic_salary'] ?? 0), 2); ?></td>
            <td class="amt"><?php echo number_format((float)($r['total_salary'] ?? 0), 2); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="actions">
    <button onclick="window.print()">Print</button>
    <button onclick="window.close()">Close</button>
</div>

<script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 250); });</script>
</body>
</html>
