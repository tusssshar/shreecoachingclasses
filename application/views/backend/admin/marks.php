<hr>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('manage_exam_marks'); ?></div></div>
    <div class="panel-body">

        <?php echo form_open(base_url() . 'index.php?admin/marks', array('class' => 'form-inline', 'style' => 'margin-bottom:15px;')); ?>
            <input type="hidden" name="operation" value="selection">
            <select name="exam_id" class="form-control" required>
                <option value=""><?php echo get_phrase('select_exam'); ?></option>
                <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e['exam_id']; ?>" <?php if ($exam_id == $e['exam_id']) echo 'selected'; ?>><?php echo html_escape($e['name']); ?><?php echo $e['exam_date'] ? ' (' . date('d M Y', strtotime($e['exam_date'])) . ')' : ''; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="class_id" id="m_class" class="form-control" required onchange="mFilter()">
                <option value=""><?php echo get_phrase('select_class'); ?></option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?php echo $c['class_id']; ?>" <?php if ($class_id == $c['class_id']) echo 'selected'; ?>><?php echo html_escape($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="subject_id" id="m_subject" class="form-control" required>
                <option value=""><?php echo get_phrase('select_subject'); ?></option>
                <?php foreach ($subjects as $s): ?>
                    <option value="<?php echo $s['subject_id']; ?>" data-class="<?php echo $s['class_id']; ?>" <?php if ($subject_id == $s['subject_id']) echo 'selected'; ?>><?php echo html_escape($s['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-info"><?php echo get_phrase('manage_marks'); ?></button>
        <?php echo form_close(); ?>

        <?php if (!isset($students)): ?>
            <p class="text-muted"><?php echo get_phrase('select_exam_class_and_subject_to_enter_marks'); ?></p>
        <?php else: ?>
            <div class="well well-sm">
                <strong><?php echo html_escape($exam['name']); ?></strong> &middot; <?php echo html_escape($subject['name']); ?> &middot;
                <?php echo count($students); ?> <?php echo get_phrase('students'); ?>
                <?php if ($exam['results_published']): ?> &middot; <span class="label label-success"><?php echo get_phrase('results_published'); ?></span><?php endif; ?>
                <span class="pull-right"><?php echo sms_export_buttons('marks', $exam_id . '_' . $class_id . '_' . $subject_id); ?></span>
            </div>

            <?php if ($errors): ?>
                <div class="alert alert-danger"><ul style="margin:0;"><?php foreach ($errors as $er): ?><li><?php echo html_escape($er); ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <?php if (empty($students)): ?>
                <div class="alert alert-info"><?php echo get_phrase('no_active_students_in_this_class'); ?></div>
            <?php else: ?>
            <?php echo form_open(base_url() . 'index.php?admin/marks/' . $exam_id . '/' . $class_id . '/' . $subject_id); ?>
                <input type="hidden" name="operation" value="update">
                <table class="table table-bordered table-condensed">
                    <thead><tr>
                        <th style="width:70px;"><?php echo get_phrase('roll'); ?></th><th><?php echo get_phrase('student'); ?></th>
                        <th style="width:150px;"><?php echo get_phrase('marks_obtained'); ?></th><th style="width:110px;"><?php echo get_phrase('out_of'); ?></th>
                        <th><?php echo get_phrase('comment'); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($students as $s): $m = $s['mark']; ?>
                        <tr>
                            <td><?php echo html_escape($s['roll']); ?></td>
                            <td><?php echo html_escape($s['name']); ?></td>
                            <td><input type="number" step="0.5" min="0" name="mark_obtained[<?php echo $m['mark_id']; ?>]" class="form-control input-sm mark-input"
                                       value="<?php echo $m['mark_obtained'] === null ? '' : sms_num($m['mark_obtained']); ?>" placeholder="<?php echo get_phrase('absent_or_not_entered'); ?>"></td>
                            <td><input type="number" min="1" name="mark_total[<?php echo $m['mark_id']; ?>]" class="form-control input-sm mark-total" value="<?php echo (int)$m['mark_total']; ?>"></td>
                            <td><input type="text" name="comment[<?php echo $m['mark_id']; ?>]" class="form-control input-sm" value="<?php echo html_escape($m['comment']); ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_marks'); ?></button>
                <span class="text-muted" style="margin-left:8px;"><?php echo get_phrase('marks_entry_hint'); ?></span>
                <a href="<?php echo base_url(); ?>index.php?admin/tabulation_sheet/<?php echo $exam_id; ?>/<?php echo $class_id; ?>" class="btn btn-default pull-right"><?php echo get_phrase('view_tabulation_sheet'); ?> <i class="entypo-right"></i></a>
            <?php echo form_close(); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<script>
function mFilter() {
    var cls = document.getElementById('m_class').value, sel = document.getElementById('m_subject');
    for (var i = 1; i < sel.options.length; i++) {
        var show = cls !== '' && sel.options[i].getAttribute('data-class') === cls;
        sel.options[i].hidden = !show; sel.options[i].disabled = !show;
        if (!show && sel.selectedIndex === i) sel.selectedIndex = 0;
    }
}
mFilter();
// Highlight a mark above its total before saving.
document.addEventListener('input', function (e) {
    if (!e.target.classList.contains('mark-input') && !e.target.classList.contains('mark-total')) return;
    var row = e.target.closest('tr'), m = row.querySelector('.mark-input'), t = row.querySelector('.mark-total');
    m.style.borderColor = (m.value !== '' && parseFloat(m.value) > parseFloat(t.value)) ? '#d9534f' : '';
});
</script>
