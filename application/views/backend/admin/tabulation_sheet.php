<hr>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('tabulation_sheet'); ?></div></div>
    <div class="panel-body">

        <?php echo form_open(base_url() . 'index.php?admin/tabulation_sheet', array('class' => 'form-inline', 'style' => 'margin-bottom:15px;')); ?>
            <input type="hidden" name="operation" value="selection">
            <select name="exam_id" class="form-control" required>
                <option value=""><?php echo get_phrase('select_exam'); ?></option>
                <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e['exam_id']; ?>" <?php if ($exam_id == $e['exam_id']) echo 'selected'; ?>><?php echo html_escape($e['name']); ?><?php echo $e['exam_date'] ? ' (' . date('d M Y', strtotime($e['exam_date'])) . ')' : ''; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="class_id" class="form-control" required>
                <option value=""><?php echo get_phrase('select_class'); ?></option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?php echo $c['class_id']; ?>" <?php if ($class_id == $c['class_id']) echo 'selected'; ?>><?php echo html_escape($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-info"><?php echo get_phrase('view'); ?></button>
        <?php echo form_close(); ?>

        <?php if (!isset($tab)): ?>
            <p class="text-muted"><?php echo get_phrase('select_exam_and_class_to_see_the_tabulation_sheet'); ?></p>
        <?php else:
            $rows = $tab['rows'];
            usort($rows, function ($a, $b) { return ($a['rank'] ?: 9999) - ($b['rank'] ?: 9999); });
            $entered = array_filter($rows, function ($r) { return $r['percent'] !== null; }); ?>

            <?php if (!$tab['grades_configured']): ?>
                <div class="alert alert-warning"><?php echo get_phrase('no_grades_configured'); ?> <a href="<?php echo base_url(); ?>index.php?admin/grade" class="btn btn-xs btn-default"><?php echo get_phrase('set_up_grades'); ?></a></div>
            <?php endif; ?>

            <div style="margin-bottom:12px;">
                <span class="pull-right"><?php echo sms_export_buttons('tabulation', $exam_id . '_' . $class_id); ?></span>
                <strong><?php echo html_escape($tab['exam']['name']); ?></strong> &middot; <?php echo html_escape($class['name']); ?> &middot;
                <?php echo count($entered); ?> / <?php echo count($rows); ?> <?php echo get_phrase('students_with_marks'); ?>
                <?php if ($tab['exam']['results_published']): ?> &middot; <span class="label label-success"><?php echo get_phrase('results_published'); ?></span><?php endif; ?>
                <br><br>
                <?php if ($entered): ?>
                    <?php echo sms_preview_button('classic_result', $exam_id, $class_id, get_phrase('preview_result_email')); ?>
                    <?php echo form_open(base_url() . 'index.php?admin/tabulation_sheet/' . $exam_id . '/' . $class_id . '/publish', array('style' => 'display:inline', 'onsubmit' => "return confirm('" . addslashes(get_phrase('publish_results_confirm')) . "');")); ?>
                        <button class="btn btn-success btn-sm"><i class="entypo-paper-plane"></i> <?php echo $tab['exam']['results_published'] ? get_phrase('email_results_again') : get_phrase('publish_results_and_email'); ?></button>
                    <?php echo form_close(); ?>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                <thead><tr>
                    <th class="text-center"><?php echo get_phrase('rank'); ?></th><th><?php echo get_phrase('student'); ?></th>
                    <?php foreach ($tab['subjects'] as $sub): ?><th class="text-center"><?php echo html_escape($sub['name']); ?></th><?php endforeach; ?>
                    <th class="text-center"><?php echo get_phrase('total'); ?></th><th class="text-center">%</th>
                    <th class="text-center"><?php echo get_phrase('grade'); ?></th><th><?php echo get_phrase('result'); ?></th>
                </tr></thead>
                <tbody>
                <?php if (empty($rows)): ?><tr><td colspan="<?php echo 6 + count($tab['subjects']); ?>" class="text-center text-muted"><?php echo get_phrase('no_active_students_in_this_class'); ?></td></tr><?php endif; ?>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="text-center"><?php echo $r['rank'] ?: '-'; ?></td>
                        <td><?php echo html_escape($r['student']['name']); ?></td>
                        <?php foreach ($tab['subjects'] as $sub): $c = $r['cells'][$sub['subject_id']]; ?>
                            <td class="text-center"><?php echo $c ? sms_num($c['obtained']) . '<small class="text-muted">/' . sms_num($c['total']) . '</small>' : '<span class="text-muted">-</span>'; ?></td>
                        <?php endforeach; ?>
                        <td class="text-center"><?php echo $r['entered'] ? sms_num($r['obtained']) . ' / ' . sms_num($r['total']) : '-'; ?></td>
                        <td class="text-center"><?php echo $r['percent'] !== null ? number_format($r['percent'], 1) : '-'; ?></td>
                        <td class="text-center"><?php echo $r['grade'] ? html_escape($r['grade']) : '-'; ?></td>
                        <td><?php if ($r['pass'] === true): ?><span class="label label-success"><?php echo get_phrase('pass'); ?></span><?php elseif ($r['pass'] === false): ?><span class="label label-danger"><?php echo get_phrase('fail'); ?></span><?php else: ?>-<?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <p class="text-muted"><?php echo get_phrase('tabulation_hint'); ?></p>
        <?php endif; ?>
    </div>
</div>
