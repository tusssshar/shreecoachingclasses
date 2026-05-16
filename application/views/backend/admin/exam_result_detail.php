<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">
            <?php echo htmlspecialchars($class_name); ?>
            | <?php echo htmlspecialchars($student['name'] ?? ''); ?>
            | <?php echo htmlspecialchars($subject_name); ?>
            | <?php echo htmlspecialchars($exam['date']); ?>
            — Exam Result
        </div>
    </div>
    <div class="panel-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Question</th>
                    <th>Correct</th>
                    <th>Student Answer</th>
                    <th>Marks Awarded / Max</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $total_awarded = 0;
            $total_possible = 0;
            $i = 1;
            foreach ($questions as $row):
                $correct = '';
                $student_text = '';
                foreach ($row['options'] as $opt) {
                    if ($opt['label'] === $row['correct_answers']) $correct = $opt['content'];
                    if (!empty($row['student_answer']) && $opt['label'] === $row['student_answer']) $student_text = $opt['content'];
                }
                $awarded = $row['marks_awarded'] !== null ? (float)$row['marks_awarded'] : 0;
                $total_awarded  += $awarded;
                $total_possible += (int)$row['marks'];
            ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['question'])); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['correct_answers']); ?></strong>
                        <?php if ($correct !== '') echo ' — ' . htmlspecialchars($correct); ?>
                    </td>
                    <td>
                        <?php if (!empty($row['student_answer'])): ?>
                            <strong><?php echo htmlspecialchars($row['student_answer']); ?></strong>
                            <?php if ($student_text !== '') echo ' — ' . htmlspecialchars($student_text); ?>
                        <?php else: ?>
                            <em>not attempted</em>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $awarded; ?> / <?php echo (int)$row['marks']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total</th>
                    <th><?php echo $total_awarded; ?> / <?php echo $total_possible; ?>
                        <?php if ($total_possible > 0): ?>
                            (<?php echo round(($total_awarded / $total_possible) * 100, 2); ?>%)
                        <?php endif; ?>
                    </th>
                </tr>
            </tfoot>
        </table>

        <a href="<?php echo base_url(); ?>index.php?admin/exam_result_list" class="btn btn-default">Back to Results</a>
    </div>
</div>
