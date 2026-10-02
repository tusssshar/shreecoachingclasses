<?php
$base = base_url() . 'index.php?admin/exam_view/' . $exam['exam_id'];
list($opens, $closes) = sms_cbt_window($exam);
$t = function ($v) { return $v ? substr($v, 0, 5) : ''; };
?>
<hr>
<div class="row">
    <div class="col-md-12">

        <!-- STATUS BAR -->
        <div class="panel panel-default">
            <div class="panel-body" style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;">
                <div style="flex:1;min-width:260px;">
                    <strong style="font-size:16px;"><?php echo html_escape($exam['title']); ?></strong>
                    <?php echo sms_cbt_state_badge($state); ?>
                    <div class="text-muted">
                        <?php echo html_escape($exam['class_name']); ?> &middot; <?php echo html_escape($exam['subject_name']); ?> &middot;
                        <?php echo $opens ? date('d M Y, h:i A', $opens) . ' - ' . date('h:i A', $closes) : '-'; ?> &middot;
                        <?php echo (int)$exam['duration']; ?> <?php echo get_phrase('min'); ?> &middot;
                        <?php echo count($questions); ?> <?php echo get_phrase('questions'); ?> &middot;
                        <?php echo sms_num($exam['total_marks']); ?> <?php echo get_phrase('marks'); ?>
                    </div>
                </div>
                <a href="<?php echo base_url(); ?>index.php?admin/exam_list" class="btn btn-default"><i class="entypo-left"></i> <?php echo get_phrase('back_to_list'); ?></a>
                <?php echo sms_preview_button('cbt_scheduled', $exam['exam_id'], 0, get_phrase('preview_exam_email'), 'md'); ?>
                <?php if ($exam['status'] == 'draft'): ?>
                    <?php echo form_open($base . '/publish', array('style' => 'display:inline')); ?>
                        <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('publish_exam'); ?></button>
                    <?php echo form_close(); ?>
                <?php else: ?>
                    <a href="<?php echo base_url(); ?>index.php?admin/exam_assign/<?php echo $exam['exam_id']; ?>" class="btn btn-primary"><i class="entypo-users"></i> <?php echo get_phrase('assign_students'); ?></a>
                    <?php if (!$locked): ?>
                        <?php echo form_open($base . '/unpublish', array('style' => 'display:inline')); ?>
                            <button type="submit" class="btn btn-warning"><?php echo get_phrase('move_to_draft'); ?></button>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($problems): ?>
            <div class="alert alert-danger">
                <strong><?php echo get_phrase('fix_these_before_publishing'); ?>:</strong>
                <ul style="margin:6px 0 0;"><?php foreach ($problems as $p): ?><li><?php echo html_escape($p); ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <?php if ($locked): ?>
            <div class="alert alert-warning"><i class="entypo-lock"></i> <?php echo get_phrase('questions_are_locked_because_students_have_started'); ?></div>
        <?php elseif ($exam['status'] == 'published'): ?>
            <div class="alert alert-info"><?php echo get_phrase('published_exam_edit_hint'); ?></div>
        <?php endif; ?>

        <ul class="nav nav-tabs bordered">
            <li class="active"><a href="#questions" data-toggle="tab"><i class="entypo-list"></i> <?php echo get_phrase('questions'); ?> (<?php echo count($questions); ?>)</a></li>
            <li><a href="#settings" data-toggle="tab"><i class="entypo-cog"></i> <?php echo get_phrase('exam_settings'); ?></a></li>
        </ul>

        <div class="tab-content" style="padding-top:12px;">
            <!-- QUESTIONS -->
            <div class="tab-pane active" id="questions">
                <?php foreach ($questions as $i => $q):
                    $blank = trim($q['question']) === '' || preg_match('/^Question \d+$/', trim($q['question'])); ?>
                <div class="panel panel-<?php echo $blank ? 'warning' : 'default'; ?>" id="q<?php echo $q['question_id']; ?>">
                    <div class="panel-heading">
                        <div class="panel-title"><?php echo get_phrase('question'); ?> <?php echo $i + 1; ?>
                            <?php if ($blank): ?><span class="label label-warning"><?php echo get_phrase('not_entered'); ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open($base . '/save_question/' . $q['question_id'], array('class' => 'form-horizontal')); ?>
                        <fieldset <?php if ($locked) echo 'disabled'; ?>>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo get_phrase('question'); ?></label>
                                <div class="col-sm-10">
                                    <textarea name="question" class="form-control" rows="2" required><?php echo html_escape($blank ? '' : $q['question']); ?></textarea>
                                </div>
                            </div>
                            <?php foreach ($q['options'] as $label => $content): ?>
                            <div class="form-group" style="margin-bottom:6px;">
                                <label class="col-sm-2 control-label">
                                    <input type="radio" name="correct_answers" value="<?php echo $label; ?>" <?php if (strtoupper($q['correct_answers']) === $label) echo 'checked'; ?> title="<?php echo get_phrase('correct_answer'); ?>">
                                    <?php echo get_phrase('option'); ?> <?php echo $label; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="options[<?php echo $label; ?>]" class="form-control" value="<?php echo html_escape($content); ?>">
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo get_phrase('marks'); ?></label>
                                <div class="col-sm-2"><input type="number" name="marks" min="1" max="100" class="form-control" value="<?php echo (int)$q['marks']; ?>"></div>
                                <div class="col-sm-8 text-right">
                                    <span class="text-muted" style="margin-right:10px;"><?php echo get_phrase('select_the_radio_of_the_correct_option'); ?></span>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="entypo-check"></i> <?php echo get_phrase('save'); ?></button>
                                    <?php if (!$locked && count($questions) > 1): ?>
                                        <a href="#" class="btn btn-danger btn-sm" onclick="confirm_modal('<?php echo $base; ?>/delete_question/<?php echo $q['question_id']; ?>'); return false;"><i class="entypo-trash"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </fieldset>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (!$locked): ?>
                    <?php echo form_open($base . '/add_question'); ?>
                        <button type="submit" class="btn btn-default"><i class="entypo-plus"></i> <?php echo get_phrase('add_question'); ?></button>
                    <?php echo form_close(); ?>
                <?php endif; ?>
            </div>

            <!-- SETTINGS -->
            <div class="tab-pane" id="settings">
                <?php echo form_open($base . '/save_settings', array('class' => 'form-horizontal form-groups-bordered')); ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo get_phrase('exam_title'); ?></label>
                    <div class="col-sm-6"><input type="text" name="title" class="form-control" value="<?php echo html_escape($exam['title']); ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo get_phrase('class'); ?></label>
                    <div class="col-sm-4">
                        <select name="class_id" id="class_id" class="form-control" onchange="filterSubjects()" <?php if ($locked) echo 'disabled'; ?>>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?php echo $c['class_id']; ?>" <?php if ($c['class_id'] == $exam['class_id']) echo 'selected'; ?>><?php echo html_escape($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo get_phrase('subject'); ?></label>
                    <div class="col-sm-4">
                        <select name="subject_id" id="subject_id" class="form-control" <?php if ($locked) echo 'disabled'; ?>>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?php echo $s['subject_id']; ?>" data-class="<?php echo $s['class_id']; ?>" <?php if ($s['subject_id'] == $exam['subject_id']) echo 'selected'; ?>><?php echo html_escape($s['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo get_phrase('exam_date'); ?></label>
                    <div class="col-sm-3"><input type="date" name="exam_date" class="form-control" value="<?php echo html_escape($exam['exam_date']); ?>" required></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo get_phrase('start_time'); ?></label>
                    <div class="col-sm-2"><input type="time" name="start_time" class="form-control" value="<?php echo $t($exam['start_time']); ?>" required></div>
                    <label class="col-sm-2 control-label"><?php echo get_phrase('last_entry_time'); ?></label>
                    <div class="col-sm-2"><input type="time" name="end_time" class="form-control" value="<?php echo $t($exam['end_time']); ?>"></div>
                    <div class="col-sm-3"><span class="help-block" style="margin:0;"><?php echo get_phrase('exam_window_hint'); ?></span></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo get_phrase('duration'); ?></label>
                    <div class="col-sm-2"><div class="input-group"><input type="number" name="duration" min="1" max="600" class="form-control" value="<?php echo (int)$exam['duration']; ?>" required><span class="input-group-addon"><?php echo get_phrase('min'); ?></span></div></div>
                    <label class="col-sm-2 control-label"><?php echo get_phrase('pass_percentage'); ?></label>
                    <div class="col-sm-2"><div class="input-group"><input type="number" name="pass_percent" min="0" max="100" class="form-control" value="<?php echo (int)$exam['pass_percent']; ?>"><span class="input-group-addon">%</span></div></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo get_phrase('instructions'); ?></label>
                    <div class="col-sm-6"><textarea name="instructions" class="form-control" rows="3"><?php echo html_escape($exam['instructions']); ?></textarea></div>
                </div>
                <input type="hidden" name="session" value="<?php echo html_escape($exam['session']); ?>">
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_settings'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
function filterSubjects() {
    var cls = document.getElementById('class_id').value, sel = document.getElementById('subject_id'), firstOk = -1;
    for (var i = 0; i < sel.options.length; i++) {
        var show = sel.options[i].getAttribute('data-class') === cls;
        sel.options[i].hidden = !show; sel.options[i].disabled = !show;
        if (show && firstOk < 0) firstOk = i;
    }
    if (sel.options[sel.selectedIndex] && sel.options[sel.selectedIndex].disabled && firstOk >= 0) sel.selectedIndex = firstOk;
}
filterSubjects();
</script>
