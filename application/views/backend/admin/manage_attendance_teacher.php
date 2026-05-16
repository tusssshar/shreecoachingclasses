<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="att-toolbar">
    <form method="get" action="<?php echo base_url(); ?>index.php" class="form-inline">
        <input type="hidden" name="admin/manage_attendance" value="">
        <div class="form-group">
            <label class="control-label"><strong>Date:</strong></label>
            <input type="date" id="att_t_date_picker" class="form-control" value="<?php echo $selected_date; ?>">
        </div>
        <button type="button" class="btn btn-primary" onclick="loadTeacherAttendance()" style="margin-left:14px;">
            <i class="entypo-search"></i> Load
        </button>
    </form>
</div>

<?php if (empty($teachers)): ?>
    <p class="text-muted">No teachers found. Add teachers first from the Teachers page.</p>
<?php else: ?>
    <form action="<?php echo base_url(); ?>index.php?admin/manage_attendance/save_teacher" method="post">
        <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">

        <p>
            <strong><?php echo count($teachers); ?></strong> teacher(s) on
            <strong><?php echo date('d M Y', strtotime($selected_date)); ?></strong>
            <span class="att-summary">
                Default: all <span class="att-status-present">Present</span> unless changed.
                <a href="javascript:void(0);" onclick="markAllTeachers(1)">Mark all Present</a> |
                <a href="javascript:void(0);" onclick="markAllTeachers(0)">Mark all Absent</a>
            </span>
        </p>

        <div class="form-inline" style="margin-bottom:10px;">
            <div class="form-group">
                <label for="att_t_search_name" class="control-label"><strong>Search by Name:</strong></label>
                <input type="text" id="att_t_search_name" class="form-control" placeholder="Type teacher name..." style="margin-left:8px; min-width:240px;" autocomplete="off">
                <button type="button" class="btn btn-default" onclick="clearTeacherSearch()" style="margin-left:6px;">Clear</button>
                <span id="att_t_search_status" class="text-muted" style="margin-left:12px; font-size:12px;"></span>
            </div>
        </div>

        <table class="table table-bordered table-striped att-table" id="att_t_table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th style="width:120px;">Teacher ID</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th style="width:240px;">Status</th>
                </tr>
            </thead>
            <tbody id="att_t_tbody">
            <?php foreach ($teachers as $i => $t):
                $tid = $t['teacher_id'];
                $current = isset($existing_teacher_attendance[$tid]) ? $existing_teacher_attendance[$tid] : 1;
            ?>
                <tr data-teacher-id="<?php echo $tid; ?>">
                    <td class="att-t-row-num"><?php echo $i + 1; ?></td>
                    <td>TCH-<?php echo str_pad($tid, 4, '0', STR_PAD_LEFT); ?></td>
                    <td><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars(($t['designation'] ?? '') ?: '-'); ?></td>
                    <td>
                        <div class="att-radio-group">
                            <label class="att-status-present">
                                <input type="radio" name="teacher_status[<?php echo $tid; ?>]" value="1" <?php if ($current === 1) echo 'checked'; ?>>
                                Present
                            </label>
                            <label class="att-status-absent">
                                <input type="radio" name="teacher_status[<?php echo $tid; ?>]" value="0" <?php if ($current === 0) echo 'checked'; ?>>
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
                <i class="entypo-check"></i> Save Teacher Attendance
            </button>
        </div>
    </form>
<?php endif; ?>

<script>
    function loadTeacherAttendance() {
        var dateVal = document.getElementById('att_t_date_picker').value;
        if (!dateVal) { alert('Please pick a date.'); return; }
        var parts = dateVal.split('-'); // YYYY-MM-DD
        window.location = '<?php echo base_url(); ?>index.php?admin/manage_attendance/teacher/' +
                          parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function markAllTeachers(val) {
        document.querySelectorAll('#att_t_tbody tr:not(.att-hidden) input[type="radio"][value="' + val + '"]').forEach(function (r) {
            r.checked = true;
        });
    }

    var attTeacherSearchTimer = null;

    function clearTeacherSearch() {
        document.getElementById('att_t_search_name').value = '';
        attTeacherRunSearch();
    }

    function attTeacherRunSearch() {
        var query = document.getElementById('att_t_search_name').value.trim();
        var statusEl = document.getElementById('att_t_search_status');

        if (query === '') {
            document.querySelectorAll('#att_t_tbody tr').forEach(function (tr) {
                tr.classList.remove('att-hidden');
                tr.style.display = '';
            });
            statusEl.textContent = '';
            renumberTeacherVisible();
            return;
        }

        statusEl.textContent = 'Searching...';

        jQuery.ajax({
            url: '<?php echo base_url(); ?>index.php?admin/manage_attendance/search_teacher',
            method: 'POST',
            dataType: 'json',
            data: { first_name: query },
            success: function (resp) {
                if (!resp || !resp.success) {
                    statusEl.textContent = 'Search failed';
                    return;
                }
                var matched = {};
                (resp.matched_ids || []).forEach(function (id) { matched[id] = true; });

                document.querySelectorAll('#att_t_tbody tr').forEach(function (tr) {
                    var tid = tr.getAttribute('data-teacher-id');
                    if (matched[tid]) {
                        tr.classList.remove('att-hidden');
                        tr.style.display = '';
                    } else {
                        tr.classList.add('att-hidden');
                        tr.style.display = 'none';
                    }
                });
                statusEl.textContent = resp.count + ' match(es) for "' + query + '"';
                renumberTeacherVisible();
            },
            error: function () {
                statusEl.textContent = 'Search error';
            }
        });
    }

    function renumberTeacherVisible() {
        var n = 1;
        document.querySelectorAll('#att_t_tbody tr').forEach(function (tr) {
            if (tr.style.display !== 'none') {
                var cell = tr.querySelector('.att-t-row-num');
                if (cell) cell.textContent = n++;
            }
        });
    }

    jQuery(document).ready(function () {
        var input = document.getElementById('att_t_search_name');
        if (input) {
            input.addEventListener('input', function () {
                clearTimeout(attTeacherSearchTimer);
                attTeacherSearchTimer = setTimeout(attTeacherRunSearch, 250);
            });
        }
    });
</script>
