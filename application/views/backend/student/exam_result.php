<?php
$pct = sms_percent($attempt['score'], $attempt['total']);
$pass = sms_is_pass($pct, $exam['pass_percent']);
$correct = 0; $wrong = 0; $skipped = 0;
foreach ($questions as $q) {
    $g = strtoupper(trim($answers[$q['question_id']]['answer'] ?? ''));
    if ($g === '') $skipped++; elseif (sms_cbt_is_correct($g, $q['correct_answers'])) $correct++; else $wrong++;
}
?>
<hr>
<div class="row">
    <div class="col-sm-3"><div class="tile-stats tile-<?php echo $pass ? 'green' : 'red'; ?>"><div class="num"><?php echo sms_num($attempt['score']); ?> / <?php echo sms_num($attempt['total']); ?></div><h3><?php echo get_phrase('score'); ?></h3></div></div>
    <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo number_format($pct, 1); ?>%</div><h3><?php echo $pass ? get_phrase('pass') : get_phrase('fail'); ?></h3></div></div>
    <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo $rank ?: '-'; ?></div><h3><?php echo get_phrase('rank'); ?></h3></div></div>
    <div class="col-sm-3"><div class="tile-stats tile-white-gray"><div class="num"><?php echo $correct; ?> / <?php echo $wrong; ?> / <?php echo $skipped; ?></div><h3><?php echo get_phrase('correct'); ?> / <?php echo get_phrase('wrong'); ?> / <?php echo get_phrase('skipped'); ?></h3></div></div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('answers'); ?></div></div>
    <div class="panel-body">
        <?php foreach ($questions as $i => $q):
            $a = $answers[$q['question_id']] ?? null; $g = $a ? strtoupper(trim($a['answer'])) : '';
            $ok = sms_cbt_is_correct($g, $q['correct_answers']); $right = strtoupper($q['correct_answers']); ?>
            <div style="border:1px solid <?php echo $g === '' ? '#e3e7ee' : ($ok ? '#b9dfc1' : '#efc0c0'); ?>;border-radius:4px;padding:10px 12px;margin-bottom:10px;">
                <strong><?php echo $i + 1; ?>.</strong> <?php echo nl2br(html_escape($q['question'])); ?>
                <span class="pull-right"><?php echo sms_num($a['marks_awarded'] ?? 0); ?> / <?php echo (int)$q['marks']; ?></span>
                <div style="margin-top:6px;">
                    <?php foreach ($q['options'] as $label => $content): if (trim($content) === '') continue; ?>
                        <div style="padding:3px 6px;<?php echo $label === $right ? 'background:#e8f4ea;' : ($label === $g ? 'background:#fbeaea;' : ''); ?>">
                            <strong><?php echo $label; ?>.</strong> <?php echo html_escape($content); ?>
                            <?php if ($label === $right): ?> <i class="entypo-check" style="color:#3c9a4f;"></i><?php endif; ?>
                            <?php if ($label === $g && !$ok): ?> <small class="text-danger">(<?php echo get_phrase('your_answer'); ?>)</small><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($g === ''): ?><small class="text-muted"><?php echo get_phrase('not_answered'); ?></small><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <a href="<?php echo isset($back_url) ? $back_url : base_url() . 'index.php?student/exams'; ?>" class="btn btn-default"><i class="entypo-left"></i> <?php echo get_phrase('back'); ?></a>
        <button type="button" class="btn btn-default" onclick="window.print()"><i class="entypo-print"></i> <?php echo get_phrase('print'); ?></button>
    </div>
</div>
