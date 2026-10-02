<hr>
<div class="row">
    <div class="col-md-7">
        <div class="panel panel-gradient">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('email_settings'); ?> (SMTP)</div></div>
            <div class="panel-body">
                <?php if ($configured): ?>
                    <div class="alert alert-success"><i class="entypo-check"></i> <?php echo get_phrase('email_is_configured'); ?></div>
                <?php else: ?>
                    <div class="alert alert-warning"><i class="entypo-attention"></i> <?php echo get_phrase('email_not_configured_hint'); ?></div>
                <?php endif; ?>

                <?php echo form_open(base_url() . 'index.php?admin/email_settings/save', array('class' => 'form-horizontal form-groups-bordered', 'autocomplete' => 'off')); ?>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('gmail_address'); ?></label>
                        <div class="col-sm-8"><input type="email" name="smtp_user" class="form-control" value="<?php echo html_escape($settings['smtp_user']); ?>" placeholder="yourname@gmail.com"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('app_password'); ?></label>
                        <div class="col-sm-8">
                            <input type="password" name="smtp_pass" class="form-control" value="" autocomplete="new-password"
                                   placeholder="<?php echo $settings['smtp_pass'] !== '' ? get_phrase('saved_leave_blank_to_keep') : 'xxxx xxxx xxxx xxxx'; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo get_phrase('smtp_server'); ?></label>
                        <div class="col-sm-4"><input type="text" name="smtp_host" class="form-control" value="<?php echo html_escape($settings['smtp_host']); ?>"></div>
                        <div class="col-sm-2"><input type="number" name="smtp_port" class="form-control" value="<?php echo html_escape($settings['smtp_port']); ?>" title="Port"></div>
                        <div class="col-sm-2">
                            <select name="smtp_crypto" class="form-control" title="Encryption">
                                <?php foreach (array('tls' => 'TLS', 'ssl' => 'SSL', '' => 'None') as $v => $l): ?>
                                    <option value="<?php echo $v; ?>" <?php if ($settings['smtp_crypto'] === $v) echo 'selected'; ?>><?php echo $l; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <label style="font-weight:normal;"><input type="checkbox" name="email_enabled" value="1" <?php if ($settings['email_enabled'] === '1') echo 'checked'; ?>> <?php echo get_phrase('send_emails'); ?></label><br>
                            <label style="font-weight:normal;"><input type="checkbox" name="email_copy_parent" value="1" <?php if ($settings['email_copy_parent'] === '1') echo 'checked'; ?>> <?php echo get_phrase('also_send_a_copy_to_the_parent'); ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8"><button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save'); ?></button></div>
                    </div>
                <?php echo form_close(); ?>

                <?php echo form_open(base_url() . 'index.php?admin/email_settings/test', array('class' => 'form-inline')); ?>
                    <label><?php echo get_phrase('send_a_test_email_to'); ?>:</label>
                    <input type="email" name="test_email" class="form-control" placeholder="you@example.com" required>
                    <button type="submit" class="btn btn-info"><i class="entypo-paper-plane"></i> <?php echo get_phrase('send_test'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('gmail_setup_steps'); ?></div></div>
            <div class="panel-body">
                <ol style="padding-left:18px;margin:0;">
                    <li>Sign in to the Gmail account that should send the emails.</li>
                    <li>Turn on <strong>2-Step Verification</strong> at <em>myaccount.google.com/security</em>.</li>
                    <li>Open <em>myaccount.google.com/apppasswords</em>, create an app password named "School", and copy the 16 letters.</li>
                    <li>Enter the Gmail address and that app password here, save, then send a test email.</li>
                </ol>
                <p class="text-muted" style="margin-top:10px;">Your normal Gmail password will not work. Gmail allows about 500 emails a day.</p>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('daily_exam_reminders'); ?></div></div>
            <div class="panel-body">
                <p>Reminders are emailed one day before each exam. Run this once a day (Windows Task Scheduler, e.g. 6:00 PM):</p>
                <pre style="white-space:pre-wrap;font-size:11px;">C:\xampp\php\php.exe C:\xampp\htdocs\sms\index.php cron exam_reminders</pre>
                <p class="text-muted">Or open: <code style="word-break:break-all;"><?php echo base_url(); ?>index.php?cron/exam_reminders/<?php echo html_escape($settings['cron_key']); ?></code></p>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('email_log'); ?> (<?php echo get_phrase('last_100'); ?>)</div></div>
    <div class="panel-body">
        <div class="text-right" style="margin-bottom:8px;"><?php echo sms_export_buttons('email_log'); ?></div>
        <table class="table table-bordered table-condensed">
            <thead><tr><th><?php echo get_phrase('date'); ?></th><th><?php echo get_phrase('to'); ?></th><th><?php echo get_phrase('subject'); ?></th><th><?php echo get_phrase('type'); ?></th><th><?php echo get_phrase('status'); ?></th><th></th></tr></thead>
            <tbody>
            <?php if (empty($logs)): ?><tr><td colspan="6" class="text-center text-muted"><?php echo get_phrase('no_emails_yet'); ?></td></tr><?php endif; ?>
            <?php foreach ($logs as $l):
                $cls = array('sent' => 'success', 'resent' => 'success', 'failed' => 'danger', 'not_configured' => 'warning'); ?>
                <tr>
                    <td style="white-space:nowrap;"><?php echo date('d M, h:i A', strtotime($l['created_at'])); ?></td>
                    <td><?php echo html_escape($l['to_email']); ?></td>
                    <td><?php echo html_escape($l['subject']); ?></td>
                    <td><?php echo html_escape(str_replace('_', ' ', $l['event'])); ?></td>
                    <td><span class="label label-<?php echo $cls[$l['status']] ?? 'default'; ?>" title="<?php echo html_escape($l['error']); ?>"><?php echo html_escape(str_replace('_', ' ', $l['status'])); ?></span></td>
                    <td style="white-space:nowrap;">
                        <?php echo sms_preview_button('log', $l['log_id'], 0, get_phrase('view'), 'xs'); ?>
                        <?php if (in_array($l['status'], array('failed', 'not_configured'))): ?>
                            <?php echo form_open(base_url() . 'index.php?admin/email_settings/resend/' . $l['log_id'], array('style' => 'display:inline')); ?>
                                <button class="btn btn-xs btn-default"><?php echo get_phrase('resend'); ?></button>
                            <?php echo form_close(); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
