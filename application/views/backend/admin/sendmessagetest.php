<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left"><?php echo get_phrase('test_whatsapp'); ?></h4>
            </div>

            <div class="panel-body">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <?php $wa_resp = $this->session->flashdata('whatsapp_response'); ?>
                <?php if (!empty($wa_resp)): ?>
                    <div class="panel panel-info">
                        <div class="panel-heading"><strong>Twilio API Response</strong></div>
                        <div class="panel-body">
                            <pre style="white-space: pre-wrap; word-break: break-word; max-height: 350px; overflow:auto;"><?php echo htmlspecialchars(print_r($wa_resp, true)); ?></pre>
                            <?php if (is_array($wa_resp) && !empty($wa_resp['status'])): ?>
                                <p><strong>Status:</strong> <code><?php echo htmlspecialchars($wa_resp['status']); ?></code>
                                — <em>queued/sent</em> means Twilio accepted it; actual delivery to your phone depends on the WhatsApp sandbox opt-in and number format.</p>
                            <?php endif; ?>
                            <?php if (is_array($wa_resp) && !empty($wa_resp['error_message'])): ?>
                                <p class="text-danger"><strong>Twilio error:</strong> <?php echo htmlspecialchars($wa_resp['error_message']); ?> (code <?php echo htmlspecialchars($wa_resp['error_code']); ?>)</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="<?php echo base_url(); ?>index.php?admin/sendmessagetest" method="post">
                    <div class="form-group">
                        <label for="phone"><strong>Phone Number</strong> (with country code, e.g., +14155238886)</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required>
                        <small class="form-text text-muted">Include country code (e.g., +1 for USA, +91 for India)</small>
                    </div>

                    <div class="form-group">
                        <label for="message"><strong>Message</strong></label>
                        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter message to send">Test message from SMS System</textarea>
                    </div>

                    <button type="submit" name="submit" value="1" class="btn btn-primary">
                        <i class="fa fa-whatsapp"></i> Send Test WhatsApp Message
                    </button>
                    <a href="<?php echo base_url(); ?>index.php?admin/dashboard" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // Format phone number input
        $('#phone').on('input', function() {
            let value = $(this).val();
            // Remove all non-digit characters except +
            value = value.replace(/[^\d+]/g, '');
            $(this).val(value);
        });
    });
</script>
