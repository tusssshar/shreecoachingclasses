<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo get_phrase('paper_checking'); ?></div>
    </div>
    <div class="panel-body">

        <form class="form-inline" style="margin-bottom:15px;" onsubmit="if(this.exam.value){location.href='<?php echo base_url(); ?>index.php?admin/exam_paper_check/'+this.exam.value;} return false;">
            <label><?php echo get_phrase('exam'); ?>:</label>
            <select name="exam" class="form-control" style="min-width:380px;" onchange="this.form.onsubmit()">
                <option value=""><?php echo get_phrase('select_exam'); ?></option>
                <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e['exam_id']; ?>" <?php if (isset($exam) && $exam['exam_id'] == $e['exam_id']) echo 'selected'; ?>>
                        <?php echo html_escape($e['title'] . ' - ' . $e['class_name'] . ' / ' . $e['subject_name'] . ' (' . date('d M Y', strtotime($e['exam_date'])) . ') - ' . $e['submitted_count'] . '/' . $e['assigned_count'] . ' submitted'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!isset($exam)): ?>
            <p class="text-muted"><?php echo get_phrase('paper_check_hint'); ?></p>

        <?php elseif (!isset($student)): ?>
            <table class="table table-bordered">
                <thead><tr>
                    <th><?php echo get_phrase('student'); ?></th><th><?php echo get_phrase('status'); ?></th>
                    <th><?php echo get_phrase('started'); ?></th><th><?php echo get_phrase('submitted'); ?></th>
                    <th class="text-center"><?php echo get_phrase('score'); ?></th><th></th>
                </tr></thead>
                <tbody>
                <?php if (empty($assignments)): ?>
                    <tr><td colspan="6" class="text-center text-muted"><?php echo get_phrase('no_students_assigned'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($assignments as $a): $done = in_array($a['status'], array('submitted', 'checked')); ?>
                    <tr>
                        <td><?php echo html_escape($a['student_name']); ?></td>
                        <td><?php echo sms_attempt_badge($a['status']); ?></td>
                        <td><?php echo $a['started_at'] ? date('d M, h:i A', $a['started_at']) : '-'; ?></td>
                        <td><?php echo $a['submitted_at'] ? date('d M, h:i A', $a['submitted_at']) : '-'; ?></td>
                        <td class="text-center"><?php echo $done ? sms_num($a['score']) . ' / ' . sms_num($a['total']) : '-'; ?></td>
                        <td>
                            <?php if ($done): ?>
                                <a href="<?php echo base_url(); ?>index.php?admin/exam_paper_check/<?php echo $exam['exam_id']; ?>/<?php echo $a['student_id']; ?>" class="btn btn-info btn-xs"><i class="entypo-eye"></i> <?php echo get_phrase('review'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <div class="well well-sm">
                <strong><?php echo html_escape($student['name']); ?></strong> &middot; <?php echo html_escape($exam['title']); ?> &middot;
                <?php echo sms_attempt_badge($attempt['status']); ?> &middot;
                <?php echo get_phrase('score'); ?>: <strong><?php echo sms_num($attempt['score']); ?> / <?php echo sms_num($attempt['total']); ?></strong>
                <a href="<?php echo base_url(); ?>index.php?admin/exam_paper_check/<?php echo $exam['exam_id']; ?>" class="btn btn-default btn-xs pull-right"><i class="entypo-left"></i> <?php echo get_phrase('back'); ?></a>
            </div>
            <p class="text-muted"><?php echo get_phrase('auto_marked_hint'); ?></p>

            <?php echo form_open(base_url() . 'index.php?admin/exam_paper_check/' . $exam['exam_id'] . '/' . $student['student_id'] . '/save'); ?>
            <table class="table table-bordered">
                <thead><tr>
                    <th>#</th><th><?php echo get_phrase('question'); ?></th><th><?php echo get_phrase('student_answer'); ?></th>
                    <th><?php echo get_phrase('correct_answer'); ?></th><th style="width:130px;"><?php echo get_phrase('marks_awarded'); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($questions as $i => $q):
                    $ans = isset($answers[$q['question_id']]) ? $answers[$q['question_id']] : null;
                    $given = $ans ? strtoupper(trim($ans['answer'])) : '';
                    $ok = sms_cbt_is_correct($given, $q['correct_answers']);
                    $awarded = $ans && $ans['marks_awarded'] !== null ? $ans['marks_awarded'] : 0; ?>
                    <tr class="<?php echo $given === '' ? '' : ($ok ? 'success' : 'danger'); ?>">
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo nl2br(html_escape($q['question'])); ?></td>
                        <td><?php echo $given === '' ? '<em class="text-muted">' . get_phrase('not_answered') . '</em>' : '<strong>' . $given . '</strong>. ' . html_escape($q['options'][$given] ?? ''); ?></td>
                        <td><strong><?php echo html_escape($q['correct_answers']); ?></strong>. <?php echo html_escape($q['options'][strtoupper($q['correct_answers'])] ?? ''); ?></td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.5" min="0" max="<?php echo (int)$q['marks']; ?>" name="awarded[<?php echo $q['question_id']; ?>]" class="form-control" value="<?php echo sms_num($awarded); ?>">
                                <span class="input-group-addon">/ <?php echo (int)$q['marks']; ?></span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_marks'); ?></button>
            <?php echo form_close(); ?>
        <?php endif; ?>
    </div>
</div>
