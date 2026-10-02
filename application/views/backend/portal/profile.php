<hr>
<?php include __DIR__ . '/child_switcher.php'; ?>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('my_profile'); ?></div></div>
            <div class="panel-body">
                <table class="table table-condensed">
                    <?php foreach ($profile as $label => $value): ?>
                        <tr><th style="width:40%;"><?php echo html_escape($label); ?></th><td><?php echo html_escape($value); ?></td></tr>
                    <?php endforeach; ?>
                </table>
                <p class="text-muted"><?php echo get_phrase('contact_the_office_to_change_your_details'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('change_password'); ?></div></div>
            <div class="panel-body">
                <?php echo form_open($portal_base . 'manage_profile/change_password', array('class' => 'form-horizontal')); ?>
                    <div class="form-group"><label class="col-sm-5 control-label"><?php echo get_phrase('current_password'); ?></label>
                        <div class="col-sm-7"><input type="password" name="password" class="form-control" required></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label"><?php echo get_phrase('new_password'); ?></label>
                        <div class="col-sm-7"><input type="password" name="new_password" class="form-control" required></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label"><?php echo get_phrase('confirm_new_password'); ?></label>
                        <div class="col-sm-7"><input type="password" name="confirm_new_password" class="form-control" required></div></div>
                    <div class="form-group"><div class="col-sm-offset-5 col-sm-7">
                        <button type="submit" class="btn btn-success"><i class="fa fa-lock"></i> <?php echo get_phrase('change_password'); ?></button></div></div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
