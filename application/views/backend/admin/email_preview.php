<?php $h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }; ?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $h(get_phrase('email_preview') . ($p['subject'] ? ' - ' . $p['subject'] : '')); ?></title>
<style>
    body { font-family: Arial, Helvetica, sans-serif; margin: 0; background: #f4f6f9; color: #222; }
    .bar { background: #1f3a68; color: #fff; padding: 10px 16px; font-weight: bold; }
    .info { max-width: 680px; margin: 14px auto 0; background: #fff8e1; border: 1px solid #f0d68a; padding: 10px 14px; font-size: 13px; line-height: 1.6; }
    .mail { max-width: 680px; margin: 12px auto 30px; background: #fff; padding: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.12); }
</style>
</head>
<body>
    <div class="bar"><?php echo $h(get_phrase('email_preview')); ?></div>
    <div class="info">
        <?php if (!empty($p['empty'])): ?>
            <?php echo $h(get_phrase('no_recipients_yet_nothing_will_be_sent')); ?>
        <?php else: ?>
            <strong><?php echo $h(get_phrase('subject')); ?>:</strong> <?php echo $h($p['subject']); ?><br>
            <?php if (isset($p['status'])): ?>
                <strong><?php echo $h(get_phrase('to')); ?>:</strong> <?php echo $h($p['sample']); ?> &middot;
                <?php echo $h(str_replace('_', ' ', $p['status'])); ?> &middot; <?php echo $h($p['sent_at']); ?>
            <?php else: ?>
                <strong><?php echo $h(get_phrase('preview_for')); ?>:</strong> <?php echo $h($p['sample']); ?>
                (<?php echo $h(get_phrase('each_student_gets_their_own_details')); ?>)<br>
                <strong><?php echo $h(get_phrase('recipients')); ?>:</strong>
                <?php echo count($p['recipients']); ?> <?php echo $h(get_phrase('email_addresses')); ?>,
                <?php echo (int)$p['student_count']; ?> <?php echo $h(get_phrase('students')); ?>
                <?php if ($p['recipients']): ?><br><small><?php echo $h(implode(', ', array_slice($p['recipients'], 0, 40))); ?><?php echo count($p['recipients']) > 40 ? ' ...' : ''; ?></small><?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if (empty($p['empty'])): ?>
        <div class="mail"><?php echo $p['html']; ?></div>
    <?php endif; ?>
</body>
</html>
