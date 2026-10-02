<?php $g = $edit ?: array('grade_id' => 0, 'name' => '', 'grade_point' => '', 'mark_from' => '', 'mark_upto' => '', 'comment' => ''); ?>
<hr>
<div class="row">
    <div class="col-md-7">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('grade_list'); ?> (<?php echo get_phrase('percentage'); ?>)</div></div>
            <div class="panel-body">
                <div class="text-right" style="margin-bottom:8px;"><?php echo sms_export_buttons('grades'); ?></div>
                <?php if (empty($grades)): ?>
                    <div class="alert alert-info">
                        <?php echo get_phrase('no_grades_configured'); ?>
                        <?php echo form_open(base_url() . 'index.php?admin/grade/load_defaults', array('style' => 'display:inline')); ?>
                            <button class="btn btn-sm btn-primary"><?php echo get_phrase('add_standard_grades'); ?> (A1 - E)</button>
                        <?php echo form_close(); ?>
                    </div>
                <?php else: ?>
                <table class="table table-bordered">
                    <thead><tr><th><?php echo get_phrase('grade'); ?></th><th><?php echo get_phrase('grade_point'); ?></th><th><?php echo get_phrase('percentage'); ?></th><th><?php echo get_phrase('comment'); ?></th><th><?php echo get_phrase('options'); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($grades as $row): ?>
                        <tr class="<?php echo $edit && $edit['grade_id'] == $row['grade_id'] ? 'info' : ''; ?>">
                            <td><strong><?php echo html_escape($row['name']); ?></strong></td>
                            <td><?php echo html_escape($row['grade_point']); ?></td>
                            <td><?php echo (int)$row['mark_from']; ?>% - <?php echo (int)$row['mark_upto']; ?>%</td>
                            <td><?php echo html_escape($row['comment']); ?></td>
                            <td style="white-space:nowrap;">
                                <a href="<?php echo base_url(); ?>index.php?admin/grade/edit/<?php echo $row['grade_id']; ?>" class="btn btn-info btn-xs"><i class="entypo-pencil"></i></a>
                                <a href="#" class="btn btn-danger btn-xs" onclick="confirm_modal('<?php echo base_url(); ?>index.php?admin/grade/delete/<?php echo $row['grade_id']; ?>'); return false;"><i class="entypo-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
                <p class="text-muted"><?php echo get_phrase('grade_hint'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="panel panel-primary">
            <div class="panel-heading"><div class="panel-title"><?php echo $edit ? get_phrase('edit_grade') : get_phrase('add_grade'); ?></div></div>
            <div class="panel-body">
                <?php echo form_open(base_url() . 'index.php?admin/grade/' . ($edit ? 'do_update/' . $g['grade_id'] : 'create'), array('class' => 'form-horizontal')); ?>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('grade_name'); ?> *</label>
                        <div class="col-sm-8"><input type="text" name="name" class="form-control" value="<?php echo html_escape($g['name']); ?>" placeholder="e.g. A1" required></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('grade_point'); ?></label>
                        <div class="col-sm-4"><input type="text" name="grade_point" class="form-control" value="<?php echo html_escape($g['grade_point']); ?>"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('percentage'); ?> *</label>
                        <div class="col-sm-3"><input type="number" name="mark_from" min="0" max="100" class="form-control" value="<?php echo html_escape($g['mark_from']); ?>" placeholder="<?php echo get_phrase('from'); ?>" required></div>
                        <div class="col-sm-1" style="padding-top:7px;">-</div>
                        <div class="col-sm-3"><input type="number" name="mark_upto" min="0" max="100" class="form-control" value="<?php echo html_escape($g['mark_upto']); ?>" placeholder="<?php echo get_phrase('to'); ?>" required></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('comment'); ?></label>
                        <div class="col-sm-8"><input type="text" name="comment" class="form-control" value="<?php echo html_escape($g['comment']); ?>"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo $edit ? get_phrase('save') : get_phrase('add_grade'); ?></button>
                            <?php if ($edit): ?><a href="<?php echo base_url(); ?>index.php?admin/grade" class="btn btn-default"><?php echo get_phrase('cancel'); ?></a><?php endif; ?>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
