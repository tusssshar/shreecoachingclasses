<?php /* Parent portal: which child is shown, with a switcher when there are several. */
if (!empty($children)): ?>
    <div class="well well-sm" style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;">
        <strong><i class="entypo-user"></i> <?php echo get_phrase('showing'); ?>:</strong>
        <?php foreach ($children as $c): $on = $child && (int)$c['student_id'] === (int)$child['student_id']; ?>
            <a href="<?php echo base_url(); ?>index.php?parents/child_select/<?php echo $c['student_id']; ?>" class="btn btn-<?php echo $on ? 'primary' : 'default'; ?> btn-sm">
                <?php echo html_escape($c['name']); ?> <small>(<?php echo html_escape($c['class_name']); ?>)</small>
            </a>
        <?php endforeach; ?>
    </div>
<?php elseif (isset($children)): ?>
    <div class="alert alert-info"><?php echo get_phrase('no_children_linked_to_your_account_contact_the_office'); ?></div>
<?php endif; ?>
