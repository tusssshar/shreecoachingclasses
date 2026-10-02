<?php
$class_names = array_column($classes, 'name', 'class_id');
$e = $edit ?: array('exam_id' => 0, 'name' => '', 'exam_date' => '', 'total_marks' => 100, 'pass_percent' => 35, 'class_ids' => '', 'comment' => '');
$sel = array_filter(explode(',', (string)$e['class_ids']));
?>
<hr>
<div class="row">
    <div class="col-md-7">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('written_exams'); ?></div></div>
            <div class="panel-body">
                <div class="text-right" style="margin-bottom:8px;"><?php echo sms_export_buttons('written_exams'); ?></div>
                <table class="table table-bordered">
                    <thead><tr>
                        <th><?php echo get_phrase('exam'); ?></th><th><?php echo get_phrase('date'); ?></th>
                        <th><?php echo get_phrase('classes'); ?></th><th class="text-center"><?php echo get_phrase('total_marks'); ?></th>
                        <th><?php echo get_phrase('status'); ?></th><th><?php echo get_phrase('options'); ?></th>
                    </tr></thead>
                    <tbody>
                    <?php if (empty($exams)): ?><tr><td colspan="6" class="text-center text-muted"><?php echo get_phrase('no_exams_yet'); ?></td></tr><?php endif; ?>
                    <?php foreach ($exams as $row):
                        $names = array();
                        foreach (array_filter(explode(',', (string)$row['class_ids'])) as $cid) if (isset($class_names[$cid])) $names[] = $class_names[$cid];
                        $past = $row['exam_date'] && $row['exam_date'] < date('Y-m-d'); ?>
                        <tr>
                            <td><strong><?php echo html_escape($row['name']); ?></strong><?php if ($row['comment']): ?><br><small class="text-muted"><?php echo html_escape($row['comment']); ?></small><?php endif; ?></td>
                            <td style="white-space:nowrap;"><?php echo $row['exam_date'] ? date('d M Y', strtotime($row['exam_date'])) : html_escape($row['date']); ?></td>
                            <td><?php echo $names ? html_escape(implode(', ', $names)) : '<span class="text-danger">' . get_phrase('no_class_selected') . '</span>'; ?></td>
                            <td class="text-center"><?php echo (int)$row['total_marks']; ?></td>
                            <td>
                                <?php if ($row['results_published']): ?><span class="label label-success"><?php echo get_phrase('results_published'); ?></span>
                                <?php elseif ($past): ?><span class="label label-warning"><?php echo get_phrase('marks_pending'); ?></span>
                                <?php else: ?><span class="label label-info"><?php echo get_phrase('upcoming'); ?></span><?php endif; ?>
                                <?php if ($row['notified_at']): ?><br><small class="text-muted"><i class="entypo-mail"></i> <?php echo date('d M', $row['notified_at']); ?></small><?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="<?php echo base_url(); ?>index.php?admin/exam/edit/<?php echo $row['exam_id']; ?>" class="btn btn-info btn-xs"><i class="entypo-pencil"></i></a>
                                <?php echo sms_preview_button('classic_scheduled', $row['exam_id'], 0, get_phrase('preview'), 'xs'); ?>
                                <?php if (!$past && $names): ?>
                                    <?php echo form_open(base_url() . 'index.php?admin/exam/notify/' . $row['exam_id'], array('style' => 'display:inline', 'onsubmit' => "return confirm('" . addslashes(get_phrase('email_exam_schedule_to_students')) . "?');")); ?>
                                        <button class="btn btn-primary btn-xs" title="<?php echo get_phrase('email_exam_schedule_to_students'); ?>"><i class="entypo-paper-plane"></i></button>
                                    <?php echo form_close(); ?>
                                <?php endif; ?>
                                <a href="<?php echo base_url(); ?>index.php?admin/marks" class="btn btn-default btn-xs" title="<?php echo get_phrase('enter_marks'); ?>"><i class="entypo-doc-text"></i></a>
                                <a href="#" class="btn btn-danger btn-xs" onclick="confirm_modal('<?php echo base_url(); ?>index.php?admin/exam/delete/<?php echo $row['exam_id']; ?>'); return false;"><i class="entypo-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="text-muted"><?php echo get_phrase('written_exam_workflow_hint'); ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="panel panel-primary">
            <div class="panel-heading"><div class="panel-title"><?php echo $edit ? get_phrase('edit_exam') : get_phrase('add_exam'); ?></div></div>
            <div class="panel-body">
                <?php echo form_open(base_url() . 'index.php?admin/exam/' . ($edit ? 'edit/do_update/' . $e['exam_id'] : 'create'), array('class' => 'form-horizontal')); ?>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('exam_name'); ?> *</label>
                        <div class="col-sm-8"><input type="text" name="name" class="form-control" value="<?php echo html_escape($e['name']); ?>" placeholder="e.g. First Term Exam" required></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('date'); ?> *</label>
                        <div class="col-sm-8"><input type="date" name="exam_date" class="form-control" value="<?php echo html_escape($e['exam_date']); ?>" required></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('classes'); ?> *</label>
                        <div class="col-sm-8" style="max-height:160px;overflow:auto;">
                            <?php foreach ($classes as $c): ?>
                                <label style="display:block;font-weight:normal;"><input type="checkbox" name="class_ids[]" value="<?php echo $c['class_id']; ?>" <?php if (in_array($c['class_id'], $sel)) echo 'checked'; ?>> <?php echo html_escape($c['name']); ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('marks_per_subject'); ?></label>
                        <div class="col-sm-4"><input type="number" name="total_marks" min="1" class="form-control" value="<?php echo (int)$e['total_marks']; ?>"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('pass_percentage'); ?></label>
                        <div class="col-sm-4"><div class="input-group"><input type="number" name="pass_percent" min="0" max="100" class="form-control" value="<?php echo (int)$e['pass_percent']; ?>"><span class="input-group-addon">%</span></div></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('instructions'); ?></label>
                        <div class="col-sm-8"><textarea name="comment" class="form-control" rows="2"><?php echo html_escape($e['comment']); ?></textarea></div>
                    </div>
                    <?php if (!$edit): ?>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <label style="font-weight:normal;"><input type="checkbox" name="notify" value="1"> <?php echo get_phrase('email_exam_schedule_to_students'); ?></label>
                            <span class="help-block"><?php echo get_phrase('or_preview_first_then_send_from_the_list'); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo $edit ? get_phrase('save') : get_phrase('add_exam'); ?></button>
                            <?php if ($edit): ?><a href="<?php echo base_url(); ?>index.php?admin/exam" class="btn btn-default"><?php echo get_phrase('cancel'); ?></a><?php endif; ?>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
