<?php $f = function ($k, $d = '') use ($form) { return html_escape(isset($form[$k]) ? $form[$k] : $d); }; ?>
<hr />
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo get_phrase('add_cbt_exam'); ?></div>
    </div>
    <div class="panel-body">
        <?php echo form_open(base_url() . 'index.php?admin/exam_add/create', array('class' => 'form-horizontal form-groups-bordered validate')); ?>

        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('exam_title'); ?> *</label>
            <div class="col-sm-6">
                <input type="text" name="title" class="form-control" value="<?php echo $f('title'); ?>" placeholder="e.g. Unit Test 1 - Algebra" required maxlength="255">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('class'); ?> *</label>
            <div class="col-sm-5">
                <select name="class_id" id="class_id" class="form-control" required onchange="filterSubjects()">
                    <option value=""><?php echo get_phrase('select_class'); ?></option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['class_id']; ?>" <?php if ($f('class_id') == $c['class_id']) echo 'selected'; ?>><?php echo html_escape($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('subject'); ?> *</label>
            <div class="col-sm-5">
                <select name="subject_id" id="subject_id" class="form-control" required>
                    <option value=""><?php echo get_phrase('select_subject'); ?></option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?php echo $s['subject_id']; ?>" data-class="<?php echo $s['class_id']; ?>" <?php if ($f('subject_id') == $s['subject_id']) echo 'selected'; ?>><?php echo html_escape($s['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('exam_date'); ?> *</label>
            <div class="col-sm-3">
                <input type="date" name="exam_date" class="form-control" value="<?php echo $f('exam_date', date('Y-m-d', strtotime('+1 day'))); ?>" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('start_time'); ?> *</label>
            <div class="col-sm-2">
                <input type="time" name="start_time" class="form-control" value="<?php echo $f('start_time', '10:00'); ?>" required>
            </div>
            <label class="col-sm-2 control-label"><?php echo get_phrase('last_entry_time'); ?></label>
            <div class="col-sm-2">
                <input type="time" name="end_time" class="form-control" value="<?php echo $f('end_time'); ?>">
            </div>
            <div class="col-sm-3"><span class="help-block" style="margin:0;"><?php echo get_phrase('exam_window_hint'); ?></span></div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('duration'); ?> *</label>
            <div class="col-sm-2">
                <div class="input-group">
                    <input type="number" name="duration" class="form-control" min="1" max="600" value="<?php echo $f('duration', '30'); ?>" required>
                    <span class="input-group-addon"><?php echo get_phrase('min'); ?></span>
                </div>
            </div>
            <label class="col-sm-2 control-label"><?php echo get_phrase('pass_percentage'); ?></label>
            <div class="col-sm-2">
                <div class="input-group">
                    <input type="number" name="pass_percent" class="form-control" min="0" max="100" value="<?php echo $f('pass_percent', '35'); ?>">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('number_of_questions'); ?> *</label>
            <div class="col-sm-2">
                <input type="number" name="question_count" class="form-control" min="1" max="200" value="<?php echo $f('question_count', '10'); ?>" required>
            </div>
            <label class="col-sm-2 control-label"><?php echo get_phrase('options_per_question'); ?></label>
            <div class="col-sm-2">
                <select name="options_per_question" class="form-control">
                    <?php foreach (array(2, 3, 4, 5) as $n): ?>
                        <option value="<?php echo $n; ?>" <?php if ($f('options_per_question', '4') == $n) echo 'selected'; ?>><?php echo $n; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('instructions'); ?></label>
            <div class="col-sm-6">
                <textarea name="instructions" class="form-control" rows="3" placeholder="e.g. Each question carries 1 mark. No negative marking."><?php echo $f('instructions'); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('session'); ?></label>
            <div class="col-sm-3">
                <input name="session" class="form-control" value="<?php echo $f('session', $session); ?>" readonly>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-blue btn-icon icon-left"><i class="entypo-right"></i> <?php echo get_phrase('continue_to_questions'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
function filterSubjects() {
    var cls = document.getElementById('class_id').value, sel = document.getElementById('subject_id');
    for (var i = 1; i < sel.options.length; i++) {
        var show = cls !== '' && sel.options[i].getAttribute('data-class') === cls;
        sel.options[i].hidden = !show; sel.options[i].disabled = !show;
        if (!show && sel.selectedIndex === i) sel.selectedIndex = 0;
    }
}
filterSubjects();
</script>
