<hr>
<h4 style="margin-top:0;"><?php echo get_phrase('welcome'); ?>, <?php echo html_escape($teacher['name']); ?> <small><?php echo html_escape($teacher['designation']); ?></small></h4>
<div class="row">
    <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo count($subjects); ?></div><h3><?php echo get_phrase('subjects'); ?></h3></div></div>
    <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo (int)$student_count; ?></div><h3><?php echo get_phrase('my_students'); ?></h3></div></div>
    <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo count($upcoming); ?></div><h3><?php echo get_phrase('upcoming_online_exams'); ?></h3></div></div>
    <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo (int)$pending_checks; ?></div><h3><?php echo get_phrase('papers_to_check'); ?></h3></div></div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-clock"></i> <?php echo get_phrase('todays_batches'); ?> (<?php echo date('l'); ?>)</div></div>
            <div class="panel-body">
                <?php if (empty($today_sections)): ?><p class="text-muted"><?php echo get_phrase('no_batches_today'); ?></p><?php endif; ?>
                <?php foreach ($today_sections as $s): ?>
                    <div style="border-bottom:1px dashed #e3e7ee;padding:8px 0;">
                        <strong><?php echo html_escape($s['class_name']); ?> - <?php echo html_escape($s['name']); ?></strong>
                        <span class="pull-right"><?php echo $s['start_time'] ? date('h:i A', strtotime($s['start_time'])) . ' - ' . date('h:i A', strtotime($s['end_time'])) : '-'; ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if ($class_teacher && $this->portal_model && sms_menu_allowed($this->portal_model->permissions(), 'teacher', 'attendance')): ?>
                    <div style="margin-top:10px;">
                        <?php foreach ($class_teacher as $c): ?>
                            <a href="<?php echo $portal_base; ?>attendance/<?php echo $c['class_id']; ?>/<?php echo date('Y-m-d'); ?>" class="btn btn-success btn-sm"><i class="entypo-check"></i> <?php echo get_phrase('mark_attendance'); ?>: <?php echo html_escape($c['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-monitor"></i> <?php echo get_phrase('upcoming_online_exams'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($upcoming)): ?><p class="text-muted"><?php echo get_phrase('no_upcoming_exams'); ?></p><?php endif; ?>
                <?php foreach ($upcoming as $e): list($o) = sms_cbt_window($e); ?>
                    <div style="border-bottom:1px dashed #e3e7ee;padding:8px 0;">
                        <strong><?php echo html_escape($e['title']); ?></strong> <?php echo sms_cbt_state_badge($e['state']); ?><br>
                        <small class="text-muted"><?php echo html_escape($e['class_name'] . ' / ' . $e['subject_name']); ?> &middot; <?php echo date('d M, h:i A', $o); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($notices): ?>
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-megaphone"></i> <?php echo get_phrase('latest_notices'); ?></div></div>
            <div class="panel-body">
                <?php foreach ($notices as $n): ?><p><strong><?php echo html_escape($n['notice_title']); ?></strong><br><small class="text-muted"><?php echo html_escape(mb_strimwidth($n['notice'], 0, 120, '...')); ?></small></p><?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
