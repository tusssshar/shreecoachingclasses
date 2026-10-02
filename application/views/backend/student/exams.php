<hr>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('my_online_exams'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($exams)): ?>
            <div class="alert alert-info"><?php echo get_phrase('no_exams_assigned_to_you_yet'); ?></div>
        <?php else: ?>
        <table class="table table-bordered">
            <thead><tr>
                <th><?php echo get_phrase('exam'); ?></th><th><?php echo get_phrase('subject'); ?></th>
                <th><?php echo get_phrase('date'); ?> &amp; <?php echo get_phrase('time'); ?></th>
                <th class="text-center"><?php echo get_phrase('duration'); ?></th><th class="text-center"><?php echo get_phrase('marks'); ?></th>
                <th><?php echo get_phrase('status'); ?></th><th></th>
            </tr></thead>
            <tbody>
            <?php foreach ($exams as $e):
                list($o, $c) = sms_cbt_window($e);
                $st = $e['attempt']['attempt_status'];
                $done = in_array($st, array('submitted', 'checked'), true); ?>
                <tr>
                    <td><strong><?php echo html_escape($e['title']); ?></strong>
                        <?php if ($e['instructions']): ?><br><small class="text-muted"><?php echo nl2br(html_escape($e['instructions'])); ?></small><?php endif; ?></td>
                    <td><?php echo html_escape($e['subject_name']); ?></td>
                    <td><?php echo date('d M Y', $o); ?><br><small class="text-muted"><?php echo date('h:i A', $o); ?> - <?php echo date('h:i A', $c); ?></small></td>
                    <td class="text-center"><?php echo (int)$e['duration']; ?> <?php echo get_phrase('min'); ?></td>
                    <td class="text-center"><?php echo sms_num($e['total_marks']); ?></td>
                    <td>
                        <?php if ($done): ?><?php echo sms_attempt_badge('submitted'); ?>
                        <?php elseif ($st === 'in_progress'): ?><?php echo sms_attempt_badge('in_progress'); ?>
                        <?php else: ?><?php echo sms_cbt_state_badge($e['state']); ?><?php endif; ?>
                    </td>
                    <td style="white-space:nowrap;">
                        <?php if ($done && $e['results_published']): ?>
                            <a href="<?php echo base_url(); ?>index.php?student/exam_result/<?php echo $e['exam_id']; ?>" class="btn btn-success btn-sm"><i class="entypo-eye"></i> <?php echo get_phrase('view_result'); ?></a>
                        <?php elseif ($done): ?>
                            <span class="text-muted"><?php echo get_phrase('result_awaited'); ?></span>
                        <?php elseif ($e['state'] === 'open' || $st === 'in_progress'): ?>
                            <a href="<?php echo base_url(); ?>index.php?student/take_exam/<?php echo $e['exam_id']; ?>" class="btn btn-primary btn-sm"
                               onclick="return <?php echo $st === 'in_progress' ? 'true' : "confirm('" . addslashes(get_phrase('start_exam_confirm')) . "')"; ?>;">
                                <i class="entypo-play"></i> <?php echo $st === 'in_progress' ? get_phrase('continue') : get_phrase('start_exam'); ?></a>
                        <?php elseif ($e['state'] === 'upcoming'): ?>
                            <span class="text-muted"><?php echo get_phrase('opens_at'); ?> <?php echo date('d M, h:i A', $o); ?></span>
                        <?php else: ?>
                            <span class="text-danger"><?php echo get_phrase('missed'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
