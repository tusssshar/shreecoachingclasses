<hr>
<style>
.tt-actions { margin-bottom: 12px; }
.tt-sheet-wrap { overflow-x: auto; background: #f7f7f7; padding: 14px; border: 1px solid #ddd; }
.tt-sheet { width: 100%; min-width: 980px; border-collapse: collapse; table-layout: fixed; background: #fff; font-family: Arial, sans-serif; color: #111; }
.tt-sheet th, .tt-sheet td { border: 1px solid #333; padding: 3px 4px; text-align: center; vertical-align: middle; font-size: 11px; line-height: 1.2; font-weight: 700; overflow-wrap: anywhere; word-break: normal; }
.tt-title th { background: #d9eaf7; color: #244b68; font-size: 18px; letter-spacing: .5px; padding: 7px; }
.tt-range th { background: #a9f384; font-size: 15px; padding: 5px; }
.tt-head th { background: #ffe9a8; font-size: 12px; }
.tt-subhead th { background: #ffe9a8; font-size: 11px; }
.tt-section td { color: #8b4b1d; letter-spacing: 8px; font-size: 14px; background: #fff; padding: 3px; }
.tt-day-start td { border-top-width: 2px; }
.tt-break td { background: #2e7db6; height: 15px; padding: 0; border-color: #2e7db6; }
.tt-muted { color: #777; }
.tt-left { text-align: left !important; }
.tt-date { white-space: normal; font-size: 10px !important; line-height: 1.15 !important; padding-left: 2px !important; padding-right: 2px !important; }
.tt-filter { display: inline-flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.tt-filter input { height: 32px; }
</style>

<div class="panel panel-gradient">
    <div class="panel-heading clearfix">
        <div class="panel-title pull-left">Class Timetable</div>
        <div class="pull-right">
            <button type="button" class="btn btn-default btn-sm" onclick="openWeeklyPrintView();">
                <i class="entypo-print"></i> Print View
            </button>
        </div>
    </div>
    <div class="panel-body">
        <div class="tt-actions clearfix">
            <form class="tt-filter" onsubmit="goWeeklyTimetable(); return false;">
                <label class="control-label" for="tt_start" style="margin:0;">Week Start</label>
                <input type="date" id="tt_start" name="start" class="form-control" value="<?php echo htmlspecialchars($week_start); ?>">
                <button type="submit" class="btn btn-primary btn-sm"><i class="entypo-calendar"></i> View</button>
                <a href="<?php echo base_url(); ?>index.php?admin/section" class="btn btn-info btn-sm">
                    <i class="entypo-pencil"></i> Manage Rows
                </a>
            </form>
        </div>

        <?php if (empty($by_day)): ?>
            <div class="alert alert-info">No timetable rows yet. Add some via <strong>Manage Teachers Time Table</strong>.</div>
        <?php else: ?>
            <div class="tt-sheet-wrap">
                <table class="tt-sheet">
                    <colgroup>
                        <col style="width:48px;">
                        <col style="width:110px;">
                        <col style="width:110px;">
                        <col style="width:180px;">
                        <col style="width:190px;">
                        <col style="width:170px;">
                        <col style="width:210px;">
                    </colgroup>
                    <thead>
                        <tr class="tt-title"><th colspan="7">TIMETABLE FOR <?php echo htmlspecialchars($month_title); ?></th></tr>
                        <tr class="tt-range"><th colspan="7">Class Timetable Date <?php echo htmlspecialchars($date_range); ?></th></tr>
                        <tr class="tt-head">
                            <th rowspan="2">SR.NO.</th>
                            <th rowspan="2">DATE AND DAY</th>
                            <th rowspan="2">STD</th>
                            <th rowspan="2">TIME</th>
                            <th rowspan="2">REVISION SECTION</th>
                            <th colspan="2">LECTURE WITH SUBJECT</th>
                        </tr>
                        <tr class="tt-subhead">
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
                                $first_row = true;
                            ?>
                            <tr class="tt-day-start tt-section">
                                <td rowspan="<?php echo $rowspan; ?>"><?php echo $sr++; ?></td>
                                <td rowspan="<?php echo $rowspan; ?>" class="tt-date">
                                    <?php echo htmlspecialchars($day['meta']['label']); ?><br>
                                    (<?php echo htmlspecialchars($day['meta']['day']); ?>)
                                </td>
                                <td colspan="5">MORNING SECTION</td>
                            </tr>
                            <?php if (empty($morning)): ?>
                                <tr><td colspan="5" class="tt-muted">NO LECTURE</td></tr>
                            <?php else: foreach ($morning as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(strtoupper($item['std'])); ?></td>
                                    <td><?php echo htmlspecialchars($item['time']); ?></td>
                                    <td class="tt-left"><?php echo htmlspecialchars(strtoupper($item['revision_section'])); ?></td>
                                    <td><?php echo htmlspecialchars(strtoupper($item['teacher'])); ?></td>
                                    <td><?php echo htmlspecialchars(strtoupper($item['lecture_subject'])); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>

                            <tr class="tt-section"><td colspan="5">AFTERNOON SECTION</td></tr>
                            <?php if (empty($afternoon)): ?>
                                <tr><td colspan="5" class="tt-muted">NO LECTURE</td></tr>
                            <?php else: foreach ($afternoon as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(strtoupper($item['std'])); ?></td>
                                    <td><?php echo htmlspecialchars($item['time']); ?></td>
                                    <td class="tt-left"><?php echo htmlspecialchars(strtoupper($item['revision_section'])); ?></td>
                                    <td><?php echo htmlspecialchars(strtoupper($item['teacher'])); ?></td>
                                    <td><?php echo htmlspecialchars(strtoupper($item['lecture_subject'])); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>

                            <?php if ($day_index < $day_count): ?>
                                <tr class="tt-break"><td colspan="7"></td></tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function weeklyStartValue() {
    var el = document.getElementById('tt_start');
    return el && el.value ? el.value : '<?php echo htmlspecialchars($week_start); ?>';
}
function goWeeklyTimetable() {
    window.location.href = '<?php echo base_url(); ?>index.php?admin/weekly_timetable/' + encodeURIComponent(weeklyStartValue());
}
function openWeeklyPrintView() {
    var url = '<?php echo base_url(); ?>index.php?admin/weekly_timetable/print/' + encodeURIComponent(weeklyStartValue());
    var winRef = window.open(url, 'sms_print_view', 'width=1100,height=820,scrollbars=1,resizable=1');
    if (winRef) winRef.focus();
    else window.location.href = url;
}
</script>
