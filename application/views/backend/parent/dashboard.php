<hr>
<h4 style="margin-top:0;"><?php echo get_phrase('welcome'); ?>, <?php echo html_escape($parent['name']); ?></h4>
<?php include __DIR__ . '/../portal/child_switcher.php'; ?>
<?php if ($child): ?>
<div class="row">
    <?php if ($attendance !== null): ?>
    <div class="col-sm-4"><div class="tile-stats tile-white-gray"><div class="num"><?php echo $attendance['percent'] === null ? '-' : $attendance['percent'] . '%'; ?></div><h3><?php echo get_phrase('attendance_this_month'); ?></h3></div></div>
    <?php endif; ?>
    <?php if ($fees !== null): ?>
    <div class="col-sm-4"><div class="tile-stats tile-<?php echo $fees['balance'] > 0 ? 'red' : 'green'; ?>"><div class="num">&#8377; <?php echo number_format($fees['balance']); ?></div><h3><?php echo get_phrase('fee_balance_due'); ?></h3></div></div>
    <?php endif; ?>
    <div class="col-sm-4"><div class="tile-stats tile-white-gray"><div class="num"><?php echo count($upcoming); ?></div><h3><?php echo get_phrase('upcoming_online_exams'); ?></h3></div></div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-calendar"></i> <?php echo get_phrase('upcoming_online_exams'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($upcoming)): ?><p class="text-muted"><?php echo get_phrase('no_upcoming_exams'); ?></p><?php endif; ?>
                <?php foreach ($upcoming as $e): list($o, $c) = sms_cbt_window($e); ?>
                    <div style="border-bottom:1px dashed #e3e7ee;padding:8px 0;"><strong><?php echo html_escape($e['title']); ?></strong> <?php echo sms_cbt_state_badge($e['state']); ?><br>
                        <small class="text-muted"><?php echo html_escape($e['subject_name']); ?> &middot; <?php echo date('d M Y, h:i A', $o); ?> - <?php echo date('h:i A', $c); ?></small></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($notices): ?>
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-megaphone"></i> <?php echo get_phrase('latest_notices'); ?></div></div>
            <div class="panel-body"><?php foreach ($notices as $n): ?><p><strong><?php echo html_escape($n['notice_title']); ?></strong><br><small class="text-muted"><?php echo html_escape(mb_strimwidth($n['notice'], 0, 120, '...')); ?></small></p><?php endforeach; ?></div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-chart-bar"></i> <?php echo get_phrase('recent_results'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($cbt_results) && empty($written)): ?><p class="text-muted"><?php echo get_phrase('no_results_published_yet'); ?></p><?php endif; ?>
                <?php foreach ($cbt_results as $e): ?>
                    <div style="border-bottom:1px dashed #e3e7ee;padding:8px 0;"><strong><?php echo html_escape($e['title']); ?></strong> <small class="text-muted">(<?php echo get_phrase('online'); ?>)</small>
                        <span class="pull-right"><?php echo number_format(sms_percent($e['attempt']['score'], $e['attempt']['total']), 1); ?>%</span></div>
                <?php endforeach; ?>
                <?php foreach ($written as $w): ?>
                    <div style="border-bottom:1px dashed #e3e7ee;padding:8px 0;"><strong><?php echo html_escape($w['exam']['name']); ?></strong> <small class="text-muted">(<?php echo get_phrase('written'); ?>)</small>
                        <span class="pull-right"><?php echo number_format($w['row']['percent'], 1); ?>%<?php echo $w['row']['grade'] ? ' &middot; ' . html_escape($w['row']['grade']) : ''; ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
