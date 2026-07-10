<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Class Timetable - <?php echo htmlspecialchars($date_range); ?></title>
<style>
@page { size: A4 portrait; margin: 6mm; }
* { box-sizing: border-box; }
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    color: #111;
    background:
        linear-gradient(#e8e8e8 1px, transparent 1px) 0 0 / 100% 16px,
        linear-gradient(90deg, #e8e8e8 1px, transparent 1px) 0 0 / 72px 100%;
}
.sheet-area { width: 100%; padding: 8mm 5mm; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; background: #fff; }
th, td { border: 1px solid #222; padding: 2px 3px; text-align: center; vertical-align: middle; font-size: 9.8px; line-height: 1.18; font-weight: 700; overflow-wrap: anywhere; word-break: normal; }
.title th { background: #d9eaf7; color: #244b68; font-size: 16px; letter-spacing: .5px; padding: 6px; }
.range th { background: #a9f384; font-size: 13px; padding: 4px; }
.head th, .subhead th { background: #ffe9a8; }
.section td { color: #8b4b1d; letter-spacing: 7px; font-size: 12px; padding: 2px; }
.day-start td { border-top-width: 2px; }
.break td { background: #2e7db6; height: 12px; padding: 0; border-color: #2e7db6; }
.muted { color: #777; }
.left { text-align: left; }
.date { font-size: 8.8px !important; line-height: 1.1 !important; padding-left: 1px !important; padding-right: 1px !important; white-space: normal; }
.actions { text-align: center; margin: 10px 0; }
.actions button { padding: 6px 14px; font-size: 12px; cursor: pointer; }
@media print {
    .actions { display: none !important; }
    .sheet-area { padding: 0; }
}
</style>
</head>
<body>
<div class="sheet-area">
    <?php if (empty($by_day)): ?>
        <p>No timetable rows.</p>
    <?php else: ?>
        <table>
            <colgroup>
                <col style="width:48px;">
                <col style="width:110px;">
                <col style="width:110px;">
                <col style="width:175px;">
                <col style="width:180px;">
                <col style="width:155px;">
                <col style="width:190px;">
            </colgroup>
            <thead>
                <tr class="title"><th colspan="7">TIMETABLE FOR <?php echo htmlspecialchars($month_title); ?></th></tr>
                <tr class="range"><th colspan="7">Class Timetable Date <?php echo htmlspecialchars($date_range); ?></th></tr>
                <tr class="head">
                    <th rowspan="2">SR.NO.</th>
                    <th rowspan="2">DATE AND DAY</th>
                    <th rowspan="2">STD</th>
                    <th rowspan="2">TIME</th>
                    <th rowspan="2">REVISION SECTION</th>
                    <th colspan="2">LECTURE WITH SUBJECT</th>
                </tr>
                <tr class="subhead">
                    <th>TEACHER</th>
                    <th>SUBJECT</th>
                </tr>
            </thead>
            <tbody>
                <?php $sr = 1; $day_count = count($by_day); $day_index = 0; ?>
                <?php foreach ($by_day as $day_name => $day): $day_index++; ?>
                    <?php
                        $morning = $day['morning'];
                        $afternoon = $day['afternoon'];
                        $rowspan = max(1, count($morning)) + max(1, count($afternoon)) + 2;
                    ?>
                    <tr class="day-start section">
                        <td rowspan="<?php echo $rowspan; ?>"><?php echo $sr++; ?></td>
                        <td rowspan="<?php echo $rowspan; ?>" class="date">
                            <?php echo htmlspecialchars($day['meta']['label']); ?><br>
                            (<?php echo htmlspecialchars($day['meta']['day']); ?>)
                        </td>
                        <td colspan="5">MORNING SECTION</td>
                    </tr>
                    <?php if (empty($morning)): ?>
                        <tr><td colspan="5" class="muted">NO LECTURE</td></tr>
                    <?php else: foreach ($morning as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(strtoupper($item['std'])); ?></td>
                            <td><?php echo htmlspecialchars($item['time']); ?></td>
                            <td class="left"><?php echo htmlspecialchars(strtoupper($item['revision_section'])); ?></td>
                            <td><?php echo htmlspecialchars(strtoupper($item['teacher'])); ?></td>
                            <td><?php echo htmlspecialchars(strtoupper($item['lecture_subject'])); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>

                    <tr class="section"><td colspan="5">AFTERNOON SECTION</td></tr>
                    <?php if (empty($afternoon)): ?>
                        <tr><td colspan="5" class="muted">NO LECTURE</td></tr>
                    <?php else: foreach ($afternoon as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(strtoupper($item['std'])); ?></td>
                            <td><?php echo htmlspecialchars($item['time']); ?></td>
                            <td class="left"><?php echo htmlspecialchars(strtoupper($item['revision_section'])); ?></td>
                            <td><?php echo htmlspecialchars(strtoupper($item['teacher'])); ?></td>
                            <td><?php echo htmlspecialchars(strtoupper($item['lecture_subject'])); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>

                    <?php if ($day_index < $day_count): ?>
                        <tr class="break"><td colspan="7"></td></tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="actions">
    <button type="button" onclick="window.print()">Print</button>
    <button type="button" onclick="window.close()">Close</button>
</div>

<script>
window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 250); });
</script>
</body>
</html>
