<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">Online Paper Checking</div>
    </div>
    <div class="panel-body">

        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
        <?php endif; ?>

        <?php if (!empty($questions) && !empty($student)): ?>
            <?php $exam_obj = $exam; ?>
            <h4>
                <?php echo htmlspecialchars($student['name']); ?>
                — <?php echo htmlspecialchars($exam_obj['class_name']); ?>
                / <?php echo htmlspecialchars($exam_obj['subject_name']); ?>
                / <?php echo htmlspecialchars($exam_obj['date']); ?>
            </h4>

            <form method="post" action="<?php echo base_url(); ?>index.php?admin/exam_paper_check/save">
                <input type="hidden" name="student_id" value="<?php echo (int)$student['student_id']; ?>">
                <input type="hidden" name="class_id"   value="<?php echo (int)$exam_obj['class_id']; ?>">
                <input type="hidden" name="subject_id" value="<?php echo (int)$exam_obj['subject_id']; ?>">
                <input type="hidden" name="date"       value="<?php echo htmlspecialchars($exam_obj['date']); ?>">
                <input type="hidden" name="duration"   value="<?php echo (int)$exam_obj['duration']; ?>">
                <input type="hidden" name="session"    value="<?php echo htmlspecialchars($exam_obj['session']); ?>">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Question</th>
                            <th>Options</th>
                            <th>Correct</th>
                            <th>Student Answer</th>
                            <th width="80">Max Marks</th>
                            <th width="110">Awarded</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; foreach ($questions as $q):
                        $existing_ans  = isset($q['existing']['answer']) ? $q['existing']['answer'] : '';
                        $existing_mark = isset($q['existing']['marks_awarded']) ? $q['existing']['marks_awarded'] : '';
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo nl2br(htmlspecialchars($q['question'])); ?></td>
                            <td>
                                <?php foreach ($q['options'] as $opt): ?>
                                    <div><strong><?php echo htmlspecialchars($opt['label']); ?>.</strong> <?php echo htmlspecialchars($opt['content']); ?></div>
                                <?php endforeach; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($q['correct_answers']); ?></strong></td>
                            <td>
                                <select name="answer[<?php echo (int)$q['question_id']; ?>]" class="form-control">
                                    <option value="">-- not attempted --</option>
                                    <?php foreach ($q['options'] as $opt): ?>
                                        <option value="<?php echo htmlspecialchars($opt['label']); ?>"
                                                <?php echo ($existing_ans === $opt['label']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($opt['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><?php echo (int)$q['marks']; ?></td>
                            <td>
                                <input type="number" step="0.5" min="0" max="<?php echo (int)$q['marks']; ?>"
                                       name="awarded[<?php echo (int)$q['question_id']; ?>]" class="form-control"
                                       value="<?php echo htmlspecialchars($existing_mark); ?>"
                                       placeholder="0 - <?php echo (int)$q['marks']; ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Save Marks</button>
                <a href="<?php echo base_url(); ?>index.php?admin/exam_paper_check" class="btn btn-default">Back</a>
            </form>
        <?php else: ?>

            <?php if (empty($assignments)): ?>
                <div class="alert alert-info">No students assigned to any exam yet. Use <a href="<?php echo base_url(); ?>index.php?admin/exam_assign">Assign Exam to Student</a> first.</div>
            <?php else: ?>
                <p>Pick a student to check their paper:</p>
                <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; foreach ($assignments as $row): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><span class="label label-<?php echo $row['status']=='completed' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                            <td>
                                <?php
                                $url = base_url() . 'index.php?admin/exam_paper_check'
                                     . '&class_id='   . (int)$row['class_id']
                                     . '&subject_id=' . (int)$row['subject_id']
                                     . '&date='       . urlencode($row['date'])
                                     . '&duration='   . (int)$row['duration']
                                     . '&session='    . urlencode($row['session'])
                                     . '&student_id=' . (int)$row['student_id'];
                                ?>
                                <a href="<?php echo $url; ?>" class="btn btn-info btn-sm">
                                    <i class="entypo-pencil"></i> Check Paper
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {
    if ($("#table_export").length) {
        $("#table_export").dataTable();
    }
});
</script>
