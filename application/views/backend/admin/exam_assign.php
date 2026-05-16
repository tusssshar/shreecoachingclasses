<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">Assign Exam to Student</div>
    </div>
    <div class="panel-body">

        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
        <?php endif; ?>

        <?php if (empty($exam_groups)): ?>
            <div class="alert alert-info">No CBT exams found. Create one via <a href="<?php echo base_url(); ?>index.php?admin/exam_add">Add Exam</a>.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Session</th>
                            <th>Questions</th>
                            <th>Assigned</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; foreach ($exam_groups as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo (int)$row['duration']; ?> min</td>
                            <td><?php echo htmlspecialchars($row['session']); ?></td>
                            <td><?php echo (int)$row['actual_questions']; ?></td>
                            <td><?php echo (int)$row['assigned_count']; ?></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm"
                                    onclick="openAssignForm(<?php echo (int)$row['class_id']; ?>, <?php echo (int)$row['subject_id']; ?>, '<?php echo htmlspecialchars($row['date']); ?>', <?php echo (int)$row['duration']; ?>, '<?php echo htmlspecialchars(addslashes($row['session'])); ?>', '<?php echo htmlspecialchars($row['class_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['subject_name'], ENT_QUOTES); ?>')">
                                    <i class="entypo-users"></i> Assign Students
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- ASSIGN MODAL -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo base_url(); ?>index.php?admin/exam_assign/save">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Assign Students <small id="assignSubtitle"></small></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="class_id" id="assign_class_id">
                    <input type="hidden" name="subject_id" id="assign_subject_id">
                    <input type="hidden" name="date" id="assign_date">
                    <input type="hidden" name="duration" id="assign_duration">
                    <input type="hidden" name="session" id="assign_session">

                    <p>Select students of this class to assign the exam to:</p>
                    <div id="studentListContainer">Loading students&hellip;</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Assignments</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {
    $("#table_export").dataTable();
});

function openAssignForm(class_id, subject_id, date, duration, session, class_name, subject_name) {
    $('#assign_class_id').val(class_id);
    $('#assign_subject_id').val(subject_id);
    $('#assign_date').val(date);
    $('#assign_duration').val(duration);
    $('#assign_session').val(session);
    $('#assignSubtitle').text('— ' + class_name + ' / ' + subject_name + ' / ' + date);
    $('#studentListContainer').html('Loading students…');
    $('#assignModal').modal('show');

    $.getJSON('<?php echo base_url(); ?>index.php?admin/exam_assign_students/' + class_id, function (students) {
        if (!students || students.length === 0) {
            $('#studentListContainer').html('<em>No active students in this class.</em>');
            return;
        }
        var html = '<table class="table table-striped table-condensed"><thead><tr>'
                 + '<th width="40"><input type="checkbox" id="checkAll"></th>'
                 + '<th>Name</th><th>Roll</th><th>Email</th></tr></thead><tbody>';
        students.forEach(function (s) {
            html += '<tr><td><input type="checkbox" name="student_ids[]" value="' + s.student_id + '" class="stuChk"></td>'
                  + '<td>' + (s.name || '') + '</td>'
                  + '<td>' + (s.roll || '') + '</td>'
                  + '<td>' + (s.email || '') + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#studentListContainer').html(html);

        $('#checkAll').on('change', function () {
            $('.stuChk').prop('checked', this.checked);
        });
    });
}
</script>
