<hr>
<?php include __DIR__ . '/../portal/child_switcher.php'; ?>
<?php if ($child): ?>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><i class="entypo-monitor"></i> <?php echo get_phrase('online_exams'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($exams)): ?><p class="text-muted"><?php echo get_phrase('no_exams_assigned_yet'); ?></p><?php else: ?>
        <table class="table table-bordered table-condensed">
            <thead><tr><th><?php echo get_phrase('exam'); ?></th><th><?php echo get_phrase('subject'); ?></th><th><?php echo get_phrase('date'); ?> &amp; <?php echo get_phrase('time'); ?></th><th><?php echo get_phrase('duration'); ?></th><th><?php echo get_phrase('status'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($exams as $e): list($o, $c) = sms_cbt_window($e); $st = $e['attempt']['attempt_status']; ?>
                <tr><td><?php echo html_escape($e['title']); ?></td><td><?php echo html_escape($e['subject_name']); ?></td>
                    <td><?php echo date('d M Y', $o); ?> <small class="text-muted"><?php echo date('h:i A', $o); ?> - <?php echo date('h:i A', $c); ?></small></td>
                    <td><?php echo (int)$e['duration']; ?> <?php echo get_phrase('min'); ?></td>
                    <td><?php if (in_array($st, array('submitted', 'checked'), true)): ?><?php echo sms_attempt_badge('submitted'); ?>
                        <?php elseif ($st === 'in_progress'): ?><?php echo sms_attempt_badge('in_progress'); ?>
                        <?php elseif ($e['state'] === 'closed'): ?><span class="label label-danger"><?php echo get_phrase('missed'); ?></span>
                        <?php else: ?><?php echo sms_cbt_state_badge($e['state']); ?><?php endif; ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <p class="text-muted"><?php echo get_phrase('your_child_takes_the_exam_from_the_student_login'); ?></p>
    </div>
</div>
<?php endif; ?>
