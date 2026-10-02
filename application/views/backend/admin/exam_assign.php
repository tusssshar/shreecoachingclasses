<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo get_phrase('assign_exam_to_students'); ?></div>
    </div>
    <div class="panel-body">

        <form class="form-inline" style="margin-bottom:15px;" onsubmit="if(this.exam.value){location.href='<?php echo base_url(); ?>index.php?admin/exam_assign/'+this.exam.value;} return false;">
            <label><?php echo get_phrase('exam'); ?>:</label>
            <select name="exam" class="form-control" style="min-width:380px;" onchange="this.form.onsubmit()">
                <option value=""><?php echo get_phrase('select_exam'); ?></option>
                <?php foreach ($exams as $e): list($o) = sms_cbt_window($e); ?>
                    <option value="<?php echo $e['exam_id']; ?>" <?php if (isset($exam) && $exam['exam_id'] == $e['exam_id']) echo 'selected'; ?>>
                        <?php echo html_escape($e['title'] . ' - ' . $e['class_name'] . ' / ' . $e['subject_name'] . ' (' . ($o ? date('d M Y', $o) : '-') . ')'); ?>
                        <?php echo $e['status'] == 'draft' ? ' [' . get_phrase('draft') . ']' : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!isset($exam)): ?>
            <p class="text-muted"><?php echo get_phrase('select_an_exam_to_assign_students'); ?></p>
        <?php else: list($opens, $closes) = sms_cbt_window($exam); ?>

            <div class="well well-sm">
                <strong><?php echo html_escape($exam['title']); ?></strong> <?php echo sms_cbt_state_badge($state); ?> &middot;
                <?php echo html_escape($exam['class_name']); ?> / <?php echo html_escape($exam['subject_name']); ?> &middot;
                <?php echo $opens ? date('d M Y, h:i A', $opens) . ' - ' . date('h:i A', $closes) : '-'; ?> &middot;
                <?php echo (int)$exam['duration']; ?> <?php echo get_phrase('min'); ?>
            </div>

            <?php if ($exam['status'] != 'published'): ?>
                <div class="alert alert-warning">
                    <?php echo get_phrase('publish_the_exam_before_assigning_it'); ?>
                    <a href="<?php echo base_url(); ?>index.php?admin/exam_view/<?php echo $exam['exam_id']; ?>" class="btn btn-xs btn-default"><?php echo get_phrase('open_exam'); ?></a>
                </div>
            <?php elseif ($state == 'closed'): ?>
                <div class="alert alert-warning"><?php echo get_phrase('this_exam_has_already_closed'); ?></div>
            <?php endif; ?>

            <?php if (empty($students)): ?>
                <div class="alert alert-info"><?php echo get_phrase('no_active_students_in_this_class'); ?></div>
            <?php else: ?>
            <?php echo form_open(base_url() . 'index.php?admin/exam_assign/' . $exam['exam_id'] . '/save'); ?>
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="check_all" title="<?php echo get_phrase('select_all'); ?>"></th>
                            <th><?php echo get_phrase('roll'); ?></th>
                            <th><?php echo get_phrase('student'); ?></th>
                            <th><?php echo get_phrase('email'); ?></th>
                            <th><?php echo get_phrase('status'); ?></th>
                            <th><?php echo get_phrase('email_sent'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $s): $a = isset($assigned[$s['student_id']]) ? $assigned[$s['student_id']] : null; ?>
                        <tr class="<?php echo $a ? 'success' : ''; ?>">
                            <td><?php if (!$a): ?><input type="checkbox" class="student_cb" name="student_ids[]" value="<?php echo $s['student_id']; ?>"><?php else: ?><i class="entypo-check"></i><?php endif; ?></td>
                            <td><?php echo html_escape($s['roll']); ?></td>
                            <td><?php echo html_escape($s['name']); ?></td>
                            <td>
                                <?php echo $s['email'] ? html_escape($s['email']) : '<span class="text-danger">' . get_phrase('no_email') . '</span>'; ?>
                                <?php if (!empty($s['parent_email'])): ?><br><small class="text-muted"><?php echo get_phrase('parent'); ?>: <?php echo html_escape($s['parent_email']); ?></small><?php endif; ?>
                            </td>
                            <td><?php echo $a ? sms_attempt_badge($a['status']) : '<span class="text-muted">' . get_phrase('not_assigned') . '</span>'; ?></td>
                            <td><?php echo ($a && $a['notified_at']) ? date('d M, h:i A', $a['notified_at']) : '-'; ?></td>
                            <td>
                                <?php if ($a && $a['status'] == 'assigned'): ?>
                                    <a href="#" class="btn btn-xs btn-default" onclick="confirm_modal('<?php echo base_url(); ?>index.php?admin/exam_assign/<?php echo $exam['exam_id']; ?>/remove/<?php echo $s['student_id']; ?>'); return false;"><?php echo get_phrase('remove'); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($exam['status'] == 'published' && $state != 'closed'): ?>
                    <button type="submit" class="btn btn-primary"><i class="entypo-paper-plane"></i> <?php echo get_phrase('assign_selected_and_email'); ?></button>
                    <?php echo sms_preview_button('cbt_scheduled', $exam['exam_id'], 0, get_phrase('preview_exam_email'), 'md'); ?>
                    <?php echo sms_preview_button('cbt_reminder', $exam['exam_id'], 0, get_phrase('preview_reminder'), 'md'); ?>
                    <span class="text-muted" style="margin-left:8px;"><?php echo get_phrase('assign_email_hint'); ?></span>
                <?php endif; ?>
            <?php echo form_close(); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<script>
(function () {
    var all = document.getElementById('check_all');
    if (!all) return;
    all.addEventListener('change', function () {
        var cbs = document.querySelectorAll('.student_cb');
        for (var i = 0; i < cbs.length; i++) cbs[i].checked = all.checked;
    });
})();
</script>
