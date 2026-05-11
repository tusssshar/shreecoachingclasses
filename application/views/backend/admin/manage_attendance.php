<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
    .att-toolbar { background:#f5f7fa; padding:14px; border-radius:6px; border:1px solid #e3e6ec; margin-bottom:18px; }
    .att-toolbar .form-group { margin-bottom:0; }
    .att-table th, .att-table td { vertical-align: middle !important; }
    .att-radio-group label { margin-right: 14px; font-weight: 500; cursor: pointer; }
    .att-status-present { color: #27ae60; font-weight: 600; }
    .att-status-absent  { color: #c0392b; font-weight: 600; }
    .att-summary { display:inline-block; margin-left:18px; font-size:12.5px; color:#555; }
</style>

<div class="row">
    <div class="col-md-12">

        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('flash_message'); ?>
            </div>
        <?php endif; ?>

        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left">Manage Daily Attendance</h4>
            </div>
            <div class="panel-body">

                <!-- Filter bar (GET): pick date + class -->
                <div class="att-toolbar">
                    <form method="get" action="<?php echo base_url(); ?>index.php" class="form-inline">
                        <input type="hidden" name="admin/manage_attendance" value="">
                        <div class="form-group">
                            <label class="control-label"><strong>Date:</strong></label>
                            <input type="date" id="att_date_picker" class="form-control" value="<?php echo $selected_date; ?>">
                        </div>
                        <div class="form-group" style="margin-left:14px;">
                            <label class="control-label"><strong>Class:</strong></label>
                            <select id="att_class_picker" class="form-control" style="min-width:180px;">
                                <option value="">-- Select Class --</option>
                                <?php foreach ($classes as $c): ?>
                                    <option value="<?php echo $c['class_id']; ?>" <?php if ($selected_class_id == $c['class_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="loadAttendance()" style="margin-left:14px;">
                            <i class="entypo-search"></i> Load
                        </button>
                    </form>
                </div>

                <?php if ($selected_class_id === '' || $selected_class_id === null): ?>
                    <p class="text-muted">Pick a date and class above, then click <strong>Load</strong> to mark attendance.</p>
                <?php elseif (empty($students)): ?>
                    <p class="text-muted">No active students found in the selected class.</p>
                <?php else: ?>
                    <form action="<?php echo base_url(); ?>index.php?admin/manage_attendance/save" method="post">
                        <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">
                        <input type="hidden" name="class_id" value="<?php echo $selected_class_id; ?>">

                        <p>
                            <strong><?php echo count($students); ?></strong> student(s) in this class on
                            <strong><?php echo date('d M Y', strtotime($selected_date)); ?></strong>
                            <span class="att-summary">
                                Default: all <span class="att-status-present">Present</span> unless changed.
                                <a href="javascript:void(0);" onclick="markAll(1)">Mark all Present</a> |
                                <a href="javascript:void(0);" onclick="markAll(0)">Mark all Absent</a>
                            </span>
                        </p>

                        <!-- AJAX search by first name -->
                        <div class="form-inline" style="margin-bottom:10px;">
                            <div class="form-group">
                                <label for="att_search_first_name" class="control-label"><strong>Search by First Name:</strong></label>
                                <input type="text" id="att_search_first_name" class="form-control" placeholder="Type first name..." style="margin-left:8px; min-width:240px;" autocomplete="off">
                                <button type="button" class="btn btn-default" onclick="clearAttSearch()" style="margin-left:6px;">Clear</button>
                                <span id="att_search_status" class="text-muted" style="margin-left:12px; font-size:12px;"></span>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped att-table" id="att_table">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th style="width:100px;">Student ID</th>
                                    <th>Name</th>
                                    <th>Father</th>
                                    <th style="width:240px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="att_tbody">
                            <?php foreach ($students as $i => $s):
                                $sid = $s['student_id'];
                                $current = isset($existing_attendance[$sid]) ? $existing_attendance[$sid] : 1;
                            ?>
                                <tr data-student-id="<?php echo $sid; ?>">
                                    <td class="att-row-num"><?php echo $i + 1; ?></td>
                                    <td>STU-<?php echo str_pad($sid, 5, '0', STR_PAD_LEFT); ?></td>
                                    <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($s['father_name'] ?: '-'); ?></td>
                                    <td>
                                        <div class="att-radio-group">
                                            <label class="att-status-present">
                                                <input type="radio" name="status[<?php echo $sid; ?>]" value="1" <?php if ($current === 1) echo 'checked'; ?>>
                                                Present
                                            </label>
                                            <label class="att-status-absent">
                                                <input type="radio" name="status[<?php echo $sid; ?>]" value="0" <?php if ($current === 0) echo 'checked'; ?>>
                                                Absent
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div style="text-align:right;">
                            <button type="submit" class="btn btn-success">
                                <i class="entypo-check"></i> Save Attendance
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script>
    function loadAttendance() {
        var dateVal = document.getElementById('att_date_picker').value;
        var classVal = document.getElementById('att_class_picker').value;
        if (!dateVal) { alert('Please pick a date.'); return; }
        if (!classVal) { alert('Please select a class.'); return; }
        var parts = dateVal.split('-'); // YYYY-MM-DD
        var url = '<?php echo base_url(); ?>index.php?admin/manage_attendance/' +
                  parts[2] + '/' + parts[1] + '/' + parts[0] + '/' + classVal;
        window.location = url;
    }

    function markAll(val) {
        // Only mark visible rows (respecting current search filter)
        document.querySelectorAll('#att_tbody tr:not(.att-hidden) input[type="radio"][value="' + val + '"]').forEach(function (r) {
            r.checked = true;
        });
    }

    // ---- AJAX search by first name (POST) ----
    var attSearchTimer = null;

    function clearAttSearch() {
        var input = document.getElementById('att_search_first_name');
        input.value = '';
        attRunSearch();
    }

    function attRunSearch() {
        var query = document.getElementById('att_search_first_name').value.trim();
        var statusEl = document.getElementById('att_search_status');
        var classId = '<?php echo isset($selected_class_id) ? (int)$selected_class_id : 0; ?>';

        if (query === '') {
            // Show all rows
            document.querySelectorAll('#att_tbody tr').forEach(function (tr) {
                tr.classList.remove('att-hidden');
                tr.style.display = '';
            });
            statusEl.textContent = '';
            renumberVisible();
            return;
        }

        statusEl.textContent = 'Searching...';

        jQuery.ajax({
            url: '<?php echo base_url(); ?>index.php?admin/manage_attendance/search',
            method: 'POST',
            dataType: 'json',
            data: {
                class_id: classId,
                first_name: query
            },
            success: function (resp) {
                if (!resp || !resp.success) {
                    statusEl.textContent = 'Search failed';
                    return;
                }
                var matched = {};
                (resp.matched_ids || []).forEach(function (id) { matched[id] = true; });

                document.querySelectorAll('#att_tbody tr').forEach(function (tr) {
                    var sid = tr.getAttribute('data-student-id');
                    if (matched[sid]) {
                        tr.classList.remove('att-hidden');
                        tr.style.display = '';
                    } else {
                        tr.classList.add('att-hidden');
                        tr.style.display = 'none';
                    }
                });
                statusEl.textContent = resp.count + ' match(es) for "' + query + '"';
                renumberVisible();
            },
            error: function () {
                statusEl.textContent = 'Search error';
            }
        });
    }

    function renumberVisible() {
        var n = 1;
        document.querySelectorAll('#att_tbody tr').forEach(function (tr) {
            if (tr.style.display !== 'none') {
                var cell = tr.querySelector('.att-row-num');
                if (cell) cell.textContent = n++;
            }
        });
    }

    jQuery(document).ready(function () {
        var input = document.getElementById('att_search_first_name');
        if (input) {
            input.addEventListener('input', function () {
                clearTimeout(attSearchTimer);
                attSearchTimer = setTimeout(attRunSearch, 250);
            });
        }
    });
</script>
