<hr>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><i class="entypo-users"></i> <?php echo get_phrase('my_students'); ?> (<?php echo count($students); ?>)</div></div>
    <div class="panel-body">
        <div style="margin-bottom:12px;">
            <a href="<?php echo $portal_base; ?>students" class="btn btn-<?php echo $class_id ? 'default' : 'primary'; ?> btn-sm"><?php echo get_phrase('all_classes'); ?></a>
            <?php foreach ($classes as $c): ?>
                <a href="<?php echo $portal_base; ?>students/<?php echo $c['class_id']; ?>" class="btn btn-<?php echo $class_id == $c['class_id'] ? 'primary' : 'default'; ?> btn-sm"><?php echo html_escape($c['name']); ?></a>
            <?php endforeach; ?>
        </div>
        <?php if (empty($students)): ?>
            <div class="alert alert-info"><?php echo get_phrase('no_students_found'); ?></div>
        <?php else: ?>
        <table class="table table-bordered table-condensed table-hover">
            <thead><tr><th><?php echo get_phrase('class'); ?></th><th><?php echo get_phrase('roll'); ?></th><th><?php echo get_phrase('student'); ?></th>
                <th><?php echo get_phrase('parent'); ?></th><th><?php echo get_phrase('parent_phone'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($students as $s): ?>
                <tr><td><?php echo html_escape($s['class_name']); ?></td><td><?php echo html_escape($s['roll']); ?></td>
                    <td><?php echo html_escape($s['name']); ?> <small class="text-muted">(<?php echo $s['sex'] === 'female' ? get_phrase('girl') : get_phrase('boy'); ?>)</small></td>
                    <td><?php echo html_escape($s['parent_name']); ?></td><td><?php echo html_escape($s['parent_phone']); ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
