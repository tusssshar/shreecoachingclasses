<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo get_phrase('cbt_results'); ?></div>
    </div>
    <div class="panel-body">

        <form class="form-inline" style="margin-bottom:15px;" onsubmit="if(this.exam.value){location.href='<?php echo base_url(); ?>index.php?admin/exam_result_list/'+this.exam.value;} return false;">
            <label><?php echo get_phrase('exam'); ?>:</label>
            <select name="exam" class="form-control" style="min-width:380px;" onchange="this.form.onsubmit()">
                <option value=""><?php echo get_phrase('select_exam'); ?></option>
                <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e['exam_id']; ?>" <?php if (isset($exam) && $exam['exam_id'] == $e['exam_id']) echo 'selected'; ?>>
                        <?php echo html_escape($e['title'] . ' - ' . $e['class_name'] . ' / ' . $e['subject_name'] . ' (' . date('d M Y', strtotime($e['exam_date'])) . ')'); ?>
                        <?php echo $e['results_published'] ? ' [' . get_phrase('published') . ']' : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!isset($exam)): ?>
            <p class="text-muted"><?php echo get_phrase('select_an_exam_to_see_results'); ?></p>
        <?php else:
            $finished = array_filter($results, function ($r) { return $r['percent'] !== null; });
            $passed = array_filter($finished, function ($r) { return $r['pass']; });
            $avg = $finished ? array_sum(array_column($finished, 'percent')) / count($finished) : 0;
            $notified = array_filter($finished, function ($r) { return !empty($r['result_notified_at']); });
            usort($results, function ($a, $b) { return ($a['rank'] ?: 9999) - ($b['rank'] ?: 9999); }); ?>

            <div class="row">
                <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo count($finished); ?> / <?php echo count($results); ?></div><h3><?php echo get_phrase('submitted'); ?></h3></div></div>
                <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo number_format($avg, 1); ?>%</div><h3><?php echo get_phrase('class_average'); ?></h3></div></div>
                <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo count($passed); ?></div><h3><?php echo get_phrase('passed'); ?> (<?php echo (int)$exam['pass_percent']; ?>%+)</h3></div></div>
                <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo count($notified); ?></div><h3><?php echo get_phrase('result_emails_sent'); ?></h3></div></div>
            </div>

            <div style="margin-bottom:12px;">
                <span class="pull-right"><?php echo sms_export_buttons('cbt_results', $exam['exam_id']); ?></span>
                <?php if ($finished) echo sms_preview_button('cbt_result', $exam['exam_id'], 0, get_phrase('preview_result_email')); ?>
                <?php if ($exam['results_published']): ?>
                    <span class="label label-success" style="font-size:13px;"><?php echo get_phrase('results_published'); ?> <?php echo $exam['results_published_at'] ? date('d M Y, h:i A', $exam['results_published_at']) : ''; ?></span>
                    <?php if (count($notified) < count($finished)): ?>
                        <?php echo form_open(base_url() . 'index.php?admin/exam_result_list/' . $exam['exam_id'] . '/publish', array('style' => 'display:inline')); ?>
                            <button class="btn btn-primary btn-sm"><i class="entypo-paper-plane"></i> <?php echo get_phrase('email_remaining_results'); ?> (<?php echo count($finished) - count($notified); ?>)</button>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                    <?php echo form_open(base_url() . 'index.php?admin/exam_result_list/' . $exam['exam_id'] . '/unpublish', array('style' => 'display:inline')); ?>
                        <button class="btn btn-default btn-sm"><?php echo get_phrase('hide_results_from_students'); ?></button>
                    <?php echo form_close(); ?>
                <?php elseif ($finished): ?>
                    <?php echo form_open(base_url() . 'index.php?admin/exam_result_list/' . $exam['exam_id'] . '/publish', array('style' => 'display:inline', 'onsubmit' => "return confirm('" . addslashes(get_phrase('publish_results_confirm')) . "');")); ?>
                        <button class="btn btn-success"><i class="entypo-paper-plane"></i> <?php echo get_phrase('publish_results_and_email'); ?></button>
                    <?php echo form_close(); ?>
                    <span class="text-muted"><?php echo get_phrase('publish_results_hint'); ?></span>
                <?php endif; ?>
            </div>

            <table class="table table-bordered table-hover">
                <thead><tr>
                    <th class="text-center"><?php echo get_phrase('rank'); ?></th><th><?php echo get_phrase('student'); ?></th>
                    <th><?php echo get_phrase('status'); ?></th><th class="text-center"><?php echo get_phrase('score'); ?></th>
                    <th class="text-center">%</th><th><?php echo get_phrase('result'); ?></th><th><?php echo get_phrase('email'); ?></th><th></th>
                </tr></thead>
                <tbody>
                <?php foreach ($results as $r): ?>
                    <tr>
                        <td class="text-center"><?php echo $r['rank'] ?: '-'; ?></td>
                        <td><?php echo html_escape($r['student_name']); ?></td>
                        <td><?php echo sms_attempt_badge($r['status']); ?></td>
                        <td class="text-center"><?php echo $r['percent'] !== null ? sms_num($r['score']) . ' / ' . sms_num($r['total']) : '-'; ?></td>
                        <td class="text-center"><?php echo $r['percent'] !== null ? number_format($r['percent'], 1) : '-'; ?></td>
                        <td><?php if ($r['pass'] === true): ?><span class="label label-success"><?php echo get_phrase('pass'); ?></span><?php elseif ($r['pass'] === false): ?><span class="label label-danger"><?php echo get_phrase('fail'); ?></span><?php else: ?>-<?php endif; ?></td>
                        <td><?php echo !empty($r['result_notified_at']) ? '<i class="entypo-check"></i> ' . date('d M', $r['result_notified_at']) : '-'; ?></td>
                        <td><?php if ($r['percent'] !== null): ?><a href="<?php echo base_url(); ?>index.php?admin/exam_result_detail/<?php echo $exam['exam_id']; ?>/<?php echo $r['student_id']; ?>" class="btn btn-info btn-xs"><i class="entypo-eye"></i></a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
