<hr>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><i class="entypo-pencil"></i> <?php echo get_phrase('enter_marks'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($subjects)): ?>
            <div class="alert alert-info"><?php echo get_phrase('no_subjects_assigned_to_you'); ?></div>
        <?php else: ?>
        <?php echo form_open($portal_base . 'marks', array('class' => 'form-inline', 'style' => 'margin-bottom:15px;')); ?>
            <input type="hidden" name="operation" value="selection">
            <select name="exam_id" class="form-control" required>
                <option value=""><?php echo get_phrase('select_exam'); ?></option>
                <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e['exam_id']; ?>" <?php if ($exam_id == $e['exam_id']) echo 'selected'; ?>><?php echo html_escape($e['name']); ?><?php echo $e['exam_date'] ? ' (' . date('d M Y', strtotime($e['exam_date'])) . ')' : ''; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="subject_id" class="form-control" required>
                <option value=""><?php echo get_phrase('select_subject'); ?></option>
                <?php foreach ($subjects as $s): ?>
                    <option value="<?php echo $s['subject_id']; ?>" <?php if ($subject_id == $s['subject_id']) echo 'selected'; ?>><?php echo html_escape($s['class_name'] . ' - ' . $s['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-info"><?php echo get_phrase('open'); ?></button>
        <?php echo form_close(); ?>

        <?php if (isset($students)): $locked = (bool)$exam['results_published']; ?>
            <div class="well well-sm"><strong><?php echo html_escape($exam['name']); ?></strong> &middot; <?php echo html_escape($subject['class_name'] . ' - ' . $subject['name']); ?>
                <?php if ($locked): ?> &middot; <span class="label label-success"><?php echo get_phrase('results_published'); ?></span> <i class="entypo-lock"></i><?php endif; ?></div>
            <?php if ($locked): ?><div class="alert alert-info"><?php echo get_phrase('results_are_published_marks_are_locked'); ?></div><?php endif; ?>
            <?php if ($errors): ?><div class="alert alert-danger"><ul style="margin:0;"><?php foreach ($errors as $er): ?><li><?php echo html_escape($er); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <?php echo form_open($portal_base . 'marks/' . $exam_id . '/' . $subject_id); ?>
                <input type="hidden" name="operation" value="update">
                <fieldset <?php if ($locked) echo 'disabled'; ?>>
                <table class="table table-bordered table-condensed">
                    <thead><tr><th style="width:70px;"><?php echo get_phrase('roll'); ?></th><th><?php echo get_phrase('student'); ?></th>
                        <th style="width:150px;"><?php echo get_phrase('marks_obtained'); ?></th><th style="width:110px;"><?php echo get_phrase('out_of'); ?></th><th><?php echo get_phrase('comment'); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($students as $s): $m = $s['mark']; ?>
                        <tr><td><?php echo html_escape($s['roll']); ?></td><td><?php echo html_escape($s['name']); ?></td>
                            <td><input type="number" step="0.5" min="0" name="mark_obtained[<?php echo $m['mark_id']; ?>]" class="form-control input-sm" value="<?php echo $m['mark_obtained'] === null ? '' : sms_num($m['mark_obtained']); ?>" placeholder="<?php echo get_phrase('absent_or_not_entered'); ?>"></td>
                            <td><input type="number" min="1" name="mark_total[<?php echo $m['mark_id']; ?>]" class="form-control input-sm" value="<?php echo (int)$m['mark_total']; ?>"></td>
                            <td><input type="text" name="comment[<?php echo $m['mark_id']; ?>]" class="form-control input-sm" value="<?php echo html_escape($m['comment']); ?>"></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!$locked): ?><button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_marks'); ?></button>
                    <span class="text-muted" style="margin-left:8px;"><?php echo get_phrase('marks_entry_hint'); ?></span><?php endif; ?>
                </fieldset>
            <?php echo form_close(); ?>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
