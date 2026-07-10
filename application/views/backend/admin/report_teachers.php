<hr>
<style>
.teacher-report-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
.teacher-select-cell { width: 34px; text-align: center; }
.teacher-report-table th, .teacher-report-table td { vertical-align: middle !important; }
.teacher-selected-note { margin-left: 10px; font-size: 12px; }
</style>
<div class="panel panel-gradient">
    <div class="panel-heading clearfix">
        <div class="panel-title pull-left">Teachers Report</div>
        <div class="pull-right teacher-report-actions">
            <button type="button" class="btn btn-success btn-sm"
                    onclick="exportTeachers('excel');">
                <i class="entypo-download"></i> Excel (CSV)
            </button>
            <button type="button" class="btn btn-default btn-sm"
                    onclick="exportTeachers('print');">
                <i class="entypo-print"></i> Print View
            </button>
        </div>
    </div>
    <div class="panel-body">

        <div class="form-inline" style="margin-bottom:14px;">
            <div class="form-group">
                <input type="text" id="teacher_report_q" class="form-control" style="min-width:320px;"
                       value="<?php echo htmlspecialchars($q); ?>"
                       placeholder="Search by name, email, phone, designation..."
                       onkeydown="if(event.key==='Enter'){event.preventDefault();SMS.go('admin/report_teachers',{q:this.value});}">
            </div>
            <button type="button" class="btn btn-primary"
                    onclick="SMS.go('admin/report_teachers', { q: document.getElementById('teacher_report_q').value });">
                <i class="entypo-search"></i> Search
            </button>
            <?php if ($q !== ''): ?>
                <button type="button" class="btn btn-default"
                        onclick="SMS.go('admin/report_teachers', {});">Clear</button>
            <?php endif; ?>
            <span class="text-muted" style="margin-left:14px;">Showing <strong><?php echo count($rows); ?></strong> teacher(s).</span>
            <span id="teacher_selected_note" class="text-info teacher-selected-note">0 selected</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped teacher-report-table">
                <thead>
                    <tr>
                        <th class="teacher-select-cell">
                            <input type="checkbox" id="teacher_select_all" onclick="toggleAllTeachers(this)">
                        </th>
                        <th>#</th>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Sex</th>
                        <th>Blood</th>
                        <th>Joining Date</th>
                        <th class="text-right">Basic</th>
                        <th class="text-right">Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 1; foreach ($rows as $r):
                    $jd = $r['joining_date'] ?? '';
                    $jd_display = ($jd && $jd !== '0000-00-00') ? date('d M Y', strtotime($jd)) : '-';
                ?>
                    <tr>
                        <td class="teacher-select-cell">
                            <input type="checkbox" class="teacher-select" value="<?php echo (int)$r['teacher_id']; ?>" onchange="updateTeacherSelection()">
                        </td>
                        <td><?php echo $i++; ?></td>
                        <td>TCH-<?php echo str_pad((int)$r['teacher_id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($r['designation'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($r['email']); ?></td>
                        <td><?php echo htmlspecialchars($r['phone']); ?></td>
                        <td><?php echo htmlspecialchars($r['sex']); ?></td>
                        <td><?php echo htmlspecialchars($r['blood_group'] ?? '-'); ?></td>
                        <td><?php echo $jd_display; ?></td>
                        <td class="text-right">&#8377; <?php echo number_format((float)($r['basic_salary'] ?? 0), 2); ?></td>
                        <td class="text-right">&#8377; <?php echo number_format((float)($r['total_salary'] ?? 0), 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function selectedTeacherIds() {
    var ids = [];
    jQuery('.teacher-select:checked').each(function () { ids.push(this.value); });
    return ids;
}

function updateTeacherSelection() {
    var total = jQuery('.teacher-select').length;
    var ids = selectedTeacherIds();
    jQuery('#teacher_selected_note').text(ids.length + ' selected');
    jQuery('#teacher_select_all').prop('checked', total > 0 && ids.length === total);
}

function toggleAllTeachers(source) {
    jQuery('.teacher-select').prop('checked', !!source.checked);
    updateTeacherSelection();
}

function teacherExportParams() {
    var params = { q: document.getElementById('teacher_report_q').value };
    var ids = selectedTeacherIds();
    if (ids.length) params.ids = ids.join(',');
    return params;
}

function exportTeachers(mode) {
    var params = teacherExportParams();
    if (mode === 'excel') {
        SMS.go('admin/report_teachers/excel', params);
    } else {
        SMS.openPrintView('admin/report_teachers/print', params);
    }
}

jQuery(document).ready(updateTeacherSelection);
</script>
