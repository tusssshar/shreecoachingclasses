<hr>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('online_exams'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($cbt_exams)): ?><p class="text-muted"><?php echo get_phrase('no_online_exams_for_your_subjects'); ?></p><?php endif; ?>
                <?php foreach ($cbt_exams as $e): ?>
                    <a href="<?php echo $portal_base; ?>results/cbt/<?php echo $e['exam_id']; ?>" style="display:block;padding:6px 0;border-bottom:1px dashed #e3e7ee;">
                        <strong><?php echo html_escape($e['title']); ?></strong><br><small class="text-muted"><?php echo html_escape($e['class_name'] . ' / ' . $e['subject_name']); ?> &middot; <?php echo (int)$e['submitted_count']; ?>/<?php echo (int)$e['assigned_count']; ?></small></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('written_exams'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($written) || empty($classes)): ?><p class="text-muted"><?php echo get_phrase('no_exams_yet'); ?></p><?php endif; ?>
                <?php foreach ($written as $w): $for = array_filter(explode(',', (string)$w['class_ids'])); ?>
                    <?php foreach ($classes as $c): if (!in_array((string)$c['class_id'], $for, true)) continue; ?>
                        <a href="<?php echo $portal_base; ?>results/written/<?php echo $w['exam_id']; ?>/<?php echo $c['class_id']; ?>" style="display:block;padding:6px 0;border-bottom:1px dashed #e3e7ee;">
                            <strong><?php echo html_escape($w['name']); ?></strong> - <?php echo html_escape($c['name']); ?></a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <?php if (isset($results)):
            usort($results, function ($a, $b) { return ($a['rank'] ?: 9999) - ($b['rank'] ?: 9999); }); ?>
            <div class="panel panel-default">
                <div class="panel-heading"><div class="panel-title"><?php echo html_escape($exam['title'] . ' - ' . $exam['class_name'] . ' / ' . $exam['subject_name']); ?></div></div>
                <div class="panel-body">
                    <?php if (!$exam['results_published']): ?><div class="alert alert-info"><?php echo get_phrase('results_not_published_to_students_yet'); ?></div><?php endif; ?>
                    <table class="table table-bordered table-condensed">
                        <thead><tr><th><?php echo get_phrase('rank'); ?></th><th><?php echo get_phrase('student'); ?></th><th><?php echo get_phrase('status'); ?></th><th><?php echo get_phrase('score'); ?></th><th>%</th><th><?php echo get_phrase('result'); ?></th></tr></thead>
                        <tbody>
                        <?php foreach ($results as $r): ?>
                            <tr><td><?php echo $r['rank'] ?: '-'; ?></td><td><?php echo html_escape($r['student_name']); ?></td><td><?php echo sms_attempt_badge($r['status']); ?></td>
                                <td><?php echo $r['percent'] !== null ? sms_num($r['score']) . ' / ' . sms_num($r['total']) : '-'; ?></td>
                                <td><?php echo $r['percent'] !== null ? number_format($r['percent'], 1) : '-'; ?></td>
                                <td><?php echo $r['pass'] === null ? '-' : ($r['pass'] ? '<span class="label label-success">' . get_phrase('pass') . '</span>' : '<span class="label label-danger">' . get_phrase('fail') . '</span>'); ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif (isset($tab)):
            $rows = $tab['rows']; usort($rows, function ($a, $b) { return ($a['rank'] ?: 9999) - ($b['rank'] ?: 9999); }); ?>
            <div class="panel panel-default">
                <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('tabulation_sheet'); ?>: <?php echo html_escape($tab['exam']['name'] . ' - ' . $class['name']); ?></div></div>
                <div class="panel-body table-responsive">
                    <table class="table table-bordered table-condensed">
                        <thead><tr><th><?php echo get_phrase('rank'); ?></th><th><?php echo get_phrase('student'); ?></th>
                            <?php foreach ($tab['subjects'] as $s): ?><th class="text-center"><?php echo html_escape($s['name']); ?></th><?php endforeach; ?>
                            <th>%</th><th><?php echo get_phrase('grade'); ?></th></tr></thead>
                        <tbody>
                        <?php foreach ($rows as $r): ?>
                            <tr><td><?php echo $r['rank'] ?: '-'; ?></td><td><?php echo html_escape($r['student']['name']); ?></td>
                                <?php foreach ($tab['subjects'] as $s): $c = $r['cells'][$s['subject_id']]; ?><td class="text-center"><?php echo $c ? sms_num($c['obtained']) . '/' . sms_num($c['total']) : '-'; ?></td><?php endforeach; ?>
                                <td><?php echo $r['percent'] !== null ? number_format($r['percent'], 1) : '-'; ?></td><td><?php echo $r['grade'] ?: '-'; ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info"><?php echo get_phrase('select_an_exam_to_see_results'); ?></div>
        <?php endif; ?>
    </div>
</div>
