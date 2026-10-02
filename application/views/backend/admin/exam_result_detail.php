<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo html_escape($student['name']); ?> &middot; <?php echo html_escape($exam['title']); ?></div>
    </div>
    <div class="panel-body">
        <div class="well well-sm">
            <?php echo html_escape($exam['class_name']); ?> / <?php echo html_escape($exam['subject_name']); ?> &middot;
            <?php echo sms_attempt_badge($attempt['status']); ?> &middot;
            <?php echo get_phrase('score'); ?>: <strong><?php echo sms_num($attempt['score']); ?> / <?php echo sms_num($attempt['total']); ?></strong>
            (<?php echo number_format(sms_percent($attempt['score'], $attempt['total']), 1); ?>%)
            <a href="<?php echo base_url(); ?>index.php?admin/exam_result_list/<?php echo $exam['exam_id']; ?>" class="btn btn-default btn-xs pull-right"><i class="entypo-left"></i> <?php echo get_phrase('back'); ?></a>
            <a href="<?php echo base_url(); ?>index.php?admin/exam_paper_check/<?php echo $exam['exam_id']; ?>/<?php echo $student['student_id']; ?>" class="btn btn-info btn-xs pull-right" style="margin-right:6px;"><i class="entypo-pencil"></i> <?php echo get_phrase('adjust_marks'); ?></a>
        </div>
        <table class="table table-bordered">
            <thead><tr><th>#</th><th><?php echo get_phrase('question'); ?></th><th><?php echo get_phrase('student_answer'); ?></th><th><?php echo get_phrase('correct_answer'); ?></th><th class="text-center"><?php echo get_phrase('marks'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($questions as $i => $q):
                $ans = $answers[$q['question_id']] ?? null; $given = $ans ? strtoupper(trim($ans['answer'])) : '';
                $ok = sms_cbt_is_correct($given, $q['correct_answers']); ?>
                <tr class="<?php echo $given === '' ? '' : ($ok ? 'success' : 'danger'); ?>">
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo nl2br(html_escape($q['question'])); ?></td>
                    <td><?php echo $given === '' ? '<em class="text-muted">' . get_phrase('not_answered') . '</em>' : '<strong>' . $given . '</strong>. ' . html_escape($q['options'][$given] ?? ''); ?></td>
                    <td><strong><?php echo html_escape($q['correct_answers']); ?></strong>. <?php echo html_escape($q['options'][strtoupper($q['correct_answers'])] ?? ''); ?></td>
                    <td class="text-center"><?php echo sms_num($ans['marks_awarded'] ?? 0); ?> / <?php echo (int)$q['marks']; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
