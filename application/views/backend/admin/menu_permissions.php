<?php $roles = array('teacher' => array('entypo-user', 'teacher'), 'parent' => array('entypo-users', 'parent'), 'student' => array('entypo-graduation-cap', 'student')); ?>
<hr>
<p class="text-muted"><?php echo get_phrase('menu_permissions_hint'); ?></p>
<?php echo form_open(base_url() . 'index.php?admin/menu_permissions/save'); ?>
<div class="row">
    <?php foreach ($roles as $role => $meta): ?>
    <div class="col-md-4">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><i class="<?php echo $meta[0]; ?>"></i> <?php echo get_phrase($meta[1]); ?></div></div>
            <div class="panel-body">
                <?php foreach ($menus[$role] as $key => $m): $on = sms_menu_allowed($perms, $role, $key); ?>
                    <label style="display:flex;align-items:center;gap:8px;font-weight:600;padding:7px 4px;border-bottom:1px dashed #eceff6;<?php if ($m[4]) echo 'opacity:.6;'; ?>">
                        <input type="checkbox" name="perm[<?php echo $role; ?>][<?php echo $key; ?>]" value="1" <?php if ($on) echo 'checked'; ?> <?php if ($m[4]) echo 'disabled'; ?>>
                        <i class="<?php echo $m[1]; ?>"></i> <?php echo get_phrase($m[0]); ?>
                        <?php if ($m[4]): ?><small class="text-muted">(<?php echo get_phrase('always_on'); ?>)</small><?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_permissions'); ?></button>
<a href="#" class="btn btn-default" onclick="if(confirm('<?php echo addslashes(get_phrase('reset_to_default')); ?>?')){document.getElementById('perm_reset').submit();} return false;"><?php echo get_phrase('reset_to_default'); ?></a>
<?php echo form_close(); ?>
<?php echo form_open(base_url() . 'index.php?admin/menu_permissions/reset', array('id' => 'perm_reset')); ?><?php echo form_close(); ?>
