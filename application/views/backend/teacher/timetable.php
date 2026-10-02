<?php $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'); ?>
<hr>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><i class="entypo-clock"></i> <?php echo get_phrase('my_weekly_timetable'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($sections)): ?>
            <div class="alert alert-info"><?php echo get_phrase('no_batches_assigned_to_you'); ?></div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th><?php echo get_phrase('day'); ?></th><th><?php echo get_phrase('batches'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($days as $d):
                $list = array_filter($sections, function ($s) use ($d) { return $s['days'] && stripos($s['days'], $d) !== false; }); ?>
                <tr class="<?php echo $d === date('l') ? 'info' : ''; ?>">
                    <td style="width:140px;"><strong><?php echo get_phrase(strtolower($d)); ?></strong></td>
                    <td>
                        <?php if (!$list): ?><span class="text-muted">-</span><?php endif; ?>
                        <?php foreach ($list as $s): ?>
                            <span class="label label-primary" style="display:inline-block;margin:2px 4px 2px 0;font-size:12px;">
                                <?php echo html_escape($s['class_name'] . ' ' . $s['name']); ?> &middot;
                                <?php echo $s['start_time'] ? date('h:i A', strtotime($s['start_time'])) . ' - ' . date('h:i A', strtotime($s['end_time'])) : ''; ?>
                            </span>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('subjects_i_teach'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($subjects)): ?><p class="text-muted"><?php echo get_phrase('no_subjects_assigned_to_you'); ?></p><?php endif; ?>
        <?php foreach ($subjects as $s): ?>
            <span class="label label-info" style="display:inline-block;margin:3px;font-size:12px;"><?php echo html_escape($s['class_name'] . ' - ' . $s['name']); ?></span>
        <?php endforeach; ?>
    </div>
</div>
