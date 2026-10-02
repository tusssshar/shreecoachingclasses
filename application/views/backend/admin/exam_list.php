<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo get_phrase('cbt_exams'); ?></div>
    </div>
    <div class="panel-body">

        <a href="<?php echo base_url(); ?>index.php?admin/exam_add" class="btn btn-primary" style="margin-bottom:12px;">
            <i class="entypo-plus"></i> <?php echo get_phrase('add_cbt_exam'); ?>
        </a>
        <span class="pull-right"><?php echo sms_export_buttons('cbt_exams'); ?></span>
        <p class="text-muted">
            <?php echo get_phrase('cbt_workflow_hint'); ?>
        </p>

        <?php if (empty($exams)): ?>
            <div class="alert alert-info"><?php echo get_phrase('no_cbt_exams_yet'); ?></div>
        <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th><?php echo get_phrase('exam'); ?></th>
                    <th><?php echo get_phrase('class'); ?> / <?php echo get_phrase('subject'); ?></th>
                    <th><?php echo get_phrase('date'); ?> &amp; <?php echo get_phrase('time'); ?></th>
                    <th class="text-center"><?php echo get_phrase('questions'); ?></th>
                    <th class="text-center"><?php echo get_phrase('marks'); ?></th>
                    <th class="text-center"><?php echo get_phrase('submitted'); ?></th>
                    <th><?php echo get_phrase('status'); ?></th>
                    <th><?php echo get_phrase('options'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($exams as $e): list($opens, $closes) = sms_cbt_window($e); ?>
                <tr>
                    <td><strong><?php echo html_escape($e['title']); ?></strong></td>
                    <td><?php echo html_escape($e['class_name']); ?> / <?php echo html_escape($e['subject_name']); ?></td>
                    <td>
                        <?php echo $opens ? date('d M Y', $opens) : '-'; ?><br>
                        <small class="text-muted"><?php echo $opens ? date('h:i A', $opens) . ' - ' . date('h:i A', $closes) : ''; ?> (<?php echo (int)$e['duration']; ?> <?php echo get_phrase('min'); ?>)</small>
                    </td>
                    <td class="text-center"><?php echo (int)$e['question_count']; ?></td>
                    <td class="text-center"><?php echo sms_num($e['total_marks']); ?></td>
                    <td class="text-center"><?php echo (int)$e['submitted_count']; ?> / <?php echo (int)$e['assigned_count']; ?></td>
                    <td>
                        <?php echo sms_cbt_state_badge($e['state']); ?>
                        <?php if ($e['results_published']): ?><br><span class="label label-success" style="margin-top:3px;display:inline-block;"><?php echo get_phrase('results_published'); ?></span><?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="<?php echo base_url(); ?>index.php?admin/exam_view/<?php echo $e['exam_id']; ?>" class="btn btn-info btn-xs"><i class="entypo-pencil"></i> <?php echo get_phrase('edit'); ?></a>
                        <?php if ($e['status'] == 'published'): ?>
                            <a href="<?php echo base_url(); ?>index.php?admin/exam_assign/<?php echo $e['exam_id']; ?>" class="btn btn-primary btn-xs"><i class="entypo-users"></i> <?php echo get_phrase('assign'); ?></a>
                            <a href="<?php echo base_url(); ?>index.php?admin/exam_result_list/<?php echo $e['exam_id']; ?>" class="btn btn-success btn-xs"><i class="entypo-chart-bar"></i> <?php echo get_phrase('results'); ?></a>
                        <?php endif; ?>
                        <?php if ((int)$e['submitted_count'] === 0): ?>
                            <a href="#" class="btn btn-danger btn-xs"
                               onclick="confirm_modal('<?php echo base_url(); ?>index.php?admin/exam_list/delete/<?php echo $e['exam_id']; ?>'); return false;"><i class="entypo-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
