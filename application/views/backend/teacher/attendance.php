<hr>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><i class="entypo-check"></i> <?php echo get_phrase('mark_attendance'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($classes)): ?>
            <div class="alert alert-info"><?php echo get_phrase('only_class_teachers_can_mark_attendance'); ?></div>
        <?php else: ?>
        <?php echo form_open($portal_base . 'attendance', array('class' => 'form-inline', 'style' => 'margin-bottom:14px;')); ?>
            <select name="class_id" class="form-control" required>
                <?php foreach ($classes as $c): ?><option value="<?php echo $c['class_id']; ?>" <?php if ($class_id == $c['class_id']) echo 'selected'; ?>><?php echo html_escape($c['name']); ?></option><?php endforeach; ?>
            </select>
            <input type="date" name="date" class="form-control" value="<?php echo html_escape($date); ?>" max="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d', strtotime('-7 days')); ?>" required>
            <button type="submit" class="btn btn-info"><?php echo get_phrase('open'); ?></button>
        <?php echo form_close(); ?>

        <?php if (isset($students)): ?>
            <?php if ($date_error): ?><div class="alert alert-warning"><?php echo get_phrase($date_error); ?></div><?php endif; ?>
            <?php if (empty($students)): ?>
                <div class="alert alert-info"><?php echo get_phrase('no_active_students_in_this_class'); ?></div>
            <?php else: ?>
            <?php echo form_open($portal_base . 'attendance/' . $class_id . '/' . $date); ?>
                <input type="hidden" name="save" value="1">
                <p>
                    <strong><?php echo date('l, d M Y', strtotime($date)); ?></strong> &middot;
                    <?php echo count($marked) ? get_phrase('already_marked_you_can_correct_it') : get_phrase('not_marked_yet_all_present_by_default'); ?>
                    <button type="button" class="btn btn-default btn-xs" onclick="document.querySelectorAll('.att-p').forEach(function(r){r.checked=true;})"><?php echo get_phrase('all_present'); ?></button>
                </p>
                <table class="table table-bordered table-condensed" style="max-width:640px;">
                    <thead><tr><th><?php echo get_phrase('roll'); ?></th><th><?php echo get_phrase('student'); ?></th><th class="text-center"><?php echo get_phrase('present'); ?></th><th class="text-center"><?php echo get_phrase('absent'); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($students as $s): $st = $marked[(int)$s['student_id']] ?? 1; ?>
                        <tr>
                            <td><?php echo html_escape($s['roll']); ?></td><td><?php echo html_escape($s['name']); ?></td>
                            <td class="text-center"><input type="radio" class="att-p" name="status[<?php echo $s['student_id']; ?>]" value="1" <?php if ($st !== 2) echo 'checked'; ?> <?php if ($date_error) echo 'disabled'; ?>></td>
                            <td class="text-center"><input type="radio" name="status[<?php echo $s['student_id']; ?>]" value="2" <?php if ($st === 2) echo 'checked'; ?> <?php if ($date_error) echo 'disabled'; ?>></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!$date_error): ?><button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_attendance'); ?></button><?php endif; ?>
            <?php echo form_close(); ?>
            <?php endif; ?>
        <?php endif; ?>
        <p class="text-muted" style="margin-top:10px;"><?php echo get_phrase('attendance_edit_window_hint'); ?></p>
        <?php endif; ?>
    </div>
</div>
