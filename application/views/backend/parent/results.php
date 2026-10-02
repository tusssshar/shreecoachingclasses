<hr>
<?php include __DIR__ . '/../portal/child_switcher.php'; ?>
<?php if ($child): ?>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><i class="entypo-monitor"></i> <?php echo get_phrase('online_exam_results'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($cbt)): ?><p class="text-muted"><?php echo get_phrase('no_results_published_yet'); ?></p><?php else: ?>
        <table class="table table-bordered table-condensed">
            <thead><tr><th><?php echo get_phrase('exam'); ?></th><th><?php echo get_phrase('subject'); ?></th><th><?php echo get_phrase('date'); ?></th><th><?php echo get_phrase('score'); ?></th><th>%</th><th><?php echo get_phrase('result'); ?></th><th></th></tr></thead>
            <tbody>
            <?php foreach ($cbt as $e): $pct = sms_percent($e['attempt']['score'], $e['attempt']['total']); ?>
                <tr><td><?php echo html_escape($e['title']); ?></td><td><?php echo html_escape($e['subject_name']); ?></td><td><?php echo date('d M Y', strtotime($e['exam_date'])); ?></td>
                    <td><?php echo sms_num($e['attempt']['score']); ?> / <?php echo sms_num($e['attempt']['total']); ?></td><td><?php echo number_format($pct, 1); ?></td>
                    <td><?php echo sms_is_pass($pct, $e['pass_percent']) ? '<span class="label label-success">' . get_phrase('pass') . '</span>' : '<span class="label label-danger">' . get_phrase('fail') . '</span>'; ?></td>
                    <td><a href="<?php echo $portal_base; ?>exam_result/<?php echo $e['exam_id']; ?>" class="btn btn-default btn-xs"><i class="entypo-eye"></i></a></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php foreach ($written as $res): $r = $res['row']; ?>
<div class="panel panel-default">
    <div class="panel-heading"><div class="panel-title"><?php echo html_escape($res['exam']['name']); ?> <small><?php echo $res['exam']['exam_date'] ? date('d M Y', strtotime($res['exam']['exam_date'])) : ''; ?></small></div></div>
    <div class="panel-body">
        <table class="table table-bordered table-condensed" style="max-width:560px;">
            <thead><tr><th><?php echo get_phrase('subject'); ?></th><th class="text-center"><?php echo get_phrase('marks'); ?></th></tr></thead>
            <tbody><?php foreach ($res['subjects'] as $sub): $c = $r['cells'][$sub['subject_id']]; ?>
                <tr><td><?php echo html_escape($sub['name']); ?></td><td class="text-center"><?php echo $c ? sms_num($c['obtained']) . ' / ' . sms_num($c['total']) : '-'; ?></td></tr><?php endforeach; ?></tbody>
        </table>
        <p><strong><?php echo get_phrase('total'); ?>:</strong> <?php echo sms_num($r['obtained']); ?> / <?php echo sms_num($r['total']); ?> &nbsp;
           <strong><?php echo get_phrase('percentage'); ?>:</strong> <?php echo number_format($r['percent'], 2); ?>% &nbsp;
           <?php if ($r['grade']): ?><strong><?php echo get_phrase('grade'); ?>:</strong> <?php echo html_escape($r['grade']); ?> &nbsp;<?php endif; ?>
           <strong><?php echo get_phrase('rank'); ?>:</strong> <?php echo $r['rank'] ?: '-'; ?></p>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
