<hr>
<div class="row">
    <div class="col-md-12">
        <h4 style="margin-top:0;"><?php echo get_phrase('welcome'); ?>, <?php echo html_escape($student['name']); ?>
            <small><?php echo html_escape($student['class_name']); ?></small></h4>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-calendar"></i> <?php echo get_phrase('upcoming_online_exams'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($upcoming)): ?>
                    <p class="text-muted"><?php echo get_phrase('no_upcoming_exams'); ?></p>
                <?php else: ?>
                    <?php foreach ($upcoming as $e): list($o, $c) = sms_cbt_window($e); ?>
                        <div style="border-bottom:1px solid #eee;padding:8px 0;">
                            <strong><?php echo html_escape($e['title']); ?></strong> <?php echo sms_cbt_state_badge($e['state']); ?><br>
                            <span class="text-muted"><?php echo html_escape($e['subject_name']); ?> &middot; <?php echo date('d M Y, h:i A', $o); ?> - <?php echo date('h:i A', $c); ?> &middot; <?php echo (int)$e['duration']; ?> <?php echo get_phrase('min'); ?></span>
                            <?php if ($e['state'] === 'open'): ?>
                                <a href="<?php echo base_url(); ?>index.php?student/take_exam/<?php echo $e['exam_id']; ?>" class="btn btn-success btn-xs pull-right">
                                    <?php echo $e['attempt']['attempt_status'] === 'in_progress' ? get_phrase('continue') : get_phrase('start_exam'); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="<?php echo base_url(); ?>index.php?student/exams" style="display:inline-block;margin-top:8px;"><?php echo get_phrase('all_my_exams'); ?> &raquo;</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-chart-bar"></i> <?php echo get_phrase('recent_results'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($results) && empty($written)): ?>
                    <p class="text-muted"><?php echo get_phrase('no_results_published_yet'); ?></p>
                <?php endif; ?>
                <?php foreach ($results as $e): $pct = sms_percent($e['attempt']['score'], $e['attempt']['total']); ?>
                    <div style="border-bottom:1px solid #eee;padding:8px 0;">
                        <strong><?php echo html_escape($e['title']); ?></strong> <small class="text-muted">(<?php echo get_phrase('online'); ?>)</small>
                        <span class="pull-right"><?php echo sms_num($e['attempt']['score']); ?> / <?php echo sms_num($e['attempt']['total']); ?> &middot; <?php echo number_format($pct, 1); ?>%
                            <a href="<?php echo base_url(); ?>index.php?student/exam_result/<?php echo $e['exam_id']; ?>" class="btn btn-default btn-xs"><i class="entypo-eye"></i></a></span>
                    </div>
                <?php endforeach; ?>
                <?php foreach (array_slice($written, 0, 5) as $w): ?>
                    <div style="border-bottom:1px solid #eee;padding:8px 0;">
                        <strong><?php echo html_escape($w['exam']['name']); ?></strong> <small class="text-muted">(<?php echo get_phrase('written'); ?>)</small>
                        <span class="pull-right"><?php echo number_format($w['row']['percent'], 1); ?>%<?php echo $w['row']['grade'] ? ' &middot; ' . html_escape($w['row']['grade']) : ''; ?>
                            <a href="<?php echo base_url(); ?>index.php?student/marks" class="btn btn-default btn-xs"><i class="entypo-eye"></i></a></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
