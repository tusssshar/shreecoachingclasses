<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">CBT Exam List</div>
    </div>
    <div class="panel-body">

        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
        <?php endif; ?>

        <a href="<?php echo base_url(); ?>index.php?admin/exam_add" class="btn btn-primary" style="margin-bottom:12px;">
            <i class="entypo-plus-circled"></i> Add New CBT Exam
        </a>

        <div class="table-responsive">
            <table class="table table-bordered datatable" id="table_export">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Duration (min)</th>
                        <th>Session</th>
                        <th>Questions</th>
                        <th>Total Marks</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                if (!empty($exam_groups)):
                    foreach ($exam_groups as $row):
                        $session_url = $row['session'] === '' ? '%null' : $row['session'];
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo (int)$row['duration']; ?></td>
                        <td><?php echo htmlspecialchars($row['session']); ?></td>
                        <td><?php echo (int)$row['actual_questions']; ?> / <?php echo (int)$row['question_count']; ?></td>
                        <td><?php echo (int)$row['total_marks']; ?></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-default pull-right" role="menu">
                                    <li>
                                        <a href="<?php echo base_url(); ?>index.php?admin/exam_view/<?php echo $row['class_id'].'/'.$row['subject_id'].'/'.$row['duration'].'/'.$row['date'].'/'.$session_url; ?>">
                                            <i class="entypo-pencil"></i> Edit Questions
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url(); ?>index.php?admin/exam_assign">
                                            <i class="entypo-users"></i> Assign to Students
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#" onclick="confirm_modal('<?php echo base_url(); ?>index.php?admin/exam_list/delete/<?php echo $row['class_id'].'/'.$row['subject_id'].'/'.$row['duration'].'/'.$row['date'].'/'.$session_url; ?>');">
                                            <i class="entypo-trash"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {
    $("#table_export").dataTable();
});
</script>
