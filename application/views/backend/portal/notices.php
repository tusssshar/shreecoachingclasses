<hr>
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-megaphone"></i> <?php echo get_phrase('noticeboard'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($notices)): ?><p class="text-muted"><?php echo get_phrase('no_notices'); ?></p><?php endif; ?>
                <?php foreach ($notices as $n): ?>
                    <div style="border-bottom:1px dashed #e3e7ee;padding:10px 0;">
                        <strong><?php echo html_escape($n['notice_title']); ?></strong>
                        <small class="text-muted pull-right"><?php echo $n['create_timestamp'] ? date('d M Y', $n['create_timestamp']) : ''; ?></small>
                        <div style="margin-top:4px;"><?php echo nl2br(html_escape($n['notice'])); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading"><div class="panel-title"><i class="entypo-calendar"></i> <?php echo get_phrase('holidays'); ?></div></div>
            <div class="panel-body">
                <?php if (empty($holidays)): ?><p class="text-muted"><?php echo get_phrase('no_holidays'); ?></p><?php endif; ?>
                <?php foreach ($holidays as $h): ?>
                    <div style="padding:6px 0;"><strong><?php echo html_escape($h['title']); ?></strong> <small class="text-muted"><?php echo html_escape($h['date']); ?></small><br><?php echo html_escape($h['holiday']); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
