<hr>
<?php if (empty($results)): ?>
    <div class="alert alert-info"><?php echo get_phrase('no_results_published_yet'); ?></div>
<?php endif; ?>
<?php foreach ($results as $res): $r = $res['row']; ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <?php echo html_escape($res['exam']['name']); ?>
            <small><?php echo $res['exam']['exam_date'] ? date('d M Y', strtotime($res['exam']['exam_date'])) : ''; ?></small>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-condensed" style="max-width:600px;">
            <thead><tr><th><?php echo get_phrase('subject'); ?></th><th class="text-center"><?php echo get_phrase('marks'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($res['subjects'] as $sub): $c = $r['cells'][$sub['subject_id']]; ?>
                <tr><td><?php echo html_escape($sub['name']); ?></td>
                    <td class="text-center"><?php echo $c ? sms_num($c['obtained']) . ' / ' . sms_num($c['total']) : '<span class="text-muted">-</span>'; ?></td></tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot><tr><th><?php echo get_phrase('total'); ?></th><th class="text-center"><?php echo sms_num($r['obtained']); ?> / <?php echo sms_num($r['total']); ?></th></tr></tfoot>
        </table>
        <p>
            <strong><?php echo get_phrase('percentage'); ?>:</strong> <?php echo number_format($r['percent'], 2); ?>% &nbsp;
            <?php if ($r['grade']): ?><strong><?php echo get_phrase('grade'); ?>:</strong> <?php echo html_escape($r['grade']); ?> &nbsp;<?php endif; ?>
            <strong><?php echo get_phrase('result'); ?>:</strong> <span class="label label-<?php echo $r['pass'] ? 'success' : 'danger'; ?>"><?php echo $r['pass'] ? get_phrase('pass') : get_phrase('fail'); ?></span> &nbsp;
            <strong><?php echo get_phrase('rank'); ?>:</strong> <?php echo $r['rank'] ?: '-'; ?>
        </p>
    </div>
</div>
<?php endforeach; ?>
<?php if ($results): ?><button type="button" class="btn btn-default" onclick="window.print()"><i class="entypo-print"></i> <?php echo get_phrase('print'); ?></button><?php endif; ?>
