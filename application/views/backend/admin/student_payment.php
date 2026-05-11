<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">
            Student Payment Section
        </div>
    </div>

    <div class="panel-body">
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-sm-12 text-right">
                <a href="<?php echo base_url();?>index.php?admin/export_list/student_payment/excel" target="_blank" class="btn btn-info btn-sm">
                    Export Excel
                </a>
                <a href="<?php echo base_url();?>index.php?admin/export_list/student_payment/pdf" target="_blank" class="btn btn-danger btn-sm">
                    Export PDF
                </a>
                <a href="<?php echo base_url();?>index.php?admin/export_list/student_payment/print" target="_blank" class="btn btn-default btn-sm">
                    Print
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-default panel-shadow" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">Create Payment Record</div>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open(base_url() . 'index.php?admin/student_payment/create', array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo get_phrase('student'); ?></label>
                            <div class="col-sm-8">
                                <select name="student_id" class="form-control" required>
                                    <option value=""><?php echo get_phrase('select_student'); ?></option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?php echo $student['student_id']; ?>">
                                            <?php echo $student['name']; ?> (<?php echo get_phrase('roll'); ?>: <?php echo $student['roll']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo get_phrase('title'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="title" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo get_phrase('description'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="description" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo get_phrase('total_fees'); ?></label>
                            <div class="col-sm-8">
                                <input type="number" step="0.01" class="form-control" name="amount" required />
                            </div>
                        </div>

                        <h4>Payment Entries</h4>

                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo get_phrase('payment'); ?> <?php echo $i; ?></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="number" step="0.01" class="form-control" name="payment_<?php echo $i; ?>_amount" placeholder="<?php echo get_phrase('amount'); ?>" />
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control datepicker" name="payment_<?php echo $i; ?>_date" placeholder="<?php echo get_phrase('date'); ?>" />
                                        </div>
                                        <div class="col-sm-4">
                                            <select name="payment_<?php echo $i; ?>_method" class="form-control">
                                                <option value="1"><?php echo get_phrase('cash'); ?></option>
                                                <option value="2"><?php echo get_phrase('check'); ?></option>
                                                <option value="3"><?php echo get_phrase('card'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>

                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-4">
                                <button type="submit" class="btn btn-success"><?php echo get_phrase('save_payment'); ?></button>
                            </div>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="panel panel-default panel-shadow" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title"><?php echo get_phrase('payment_history'); ?></div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered datatable" id="table_export">
                            <thead>
                                <tr>
                                    <th><?php echo get_phrase('student'); ?></th>
                                    <th><?php echo get_phrase('title'); ?></th>
                                    <th><?php echo get_phrase('total_fees'); ?></th>
                                    <th><?php echo get_phrase('paid'); ?></th>
                                    <th><?php echo get_phrase('due'); ?></th>
                                    <th><?php echo get_phrase('status'); ?></th>
                                    <th><?php echo get_phrase('payment_history'); ?></th>
                                    <th><?php echo get_phrase('date'); ?></th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $row):
                                    $is_paid = ($row['status'] == 'paid' || floatval($row['due']) <= 0);
                                ?>
                                    <tr>
                                        <td><?php echo $row['student_name']; ?></td>
                                        <td><?php echo $row['title']; ?></td>
                                        <td><?php echo $row['amount']; ?></td>
                                        <td><?php echo $row['amount_paid']; ?></td>
                                        <td><?php echo $row['due']; ?></td>
                                        <td>
                                            <span class="label label-<?php echo $is_paid ? 'success' : 'warning'; ?>">
                                                <?php echo $is_paid ? 'paid' : $row['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo !empty($row['payment_history']) ? $row['payment_history'] : 'No payments yet'; ?></td>
                                        <td><?php echo $row['creation_date']; ?></td>
                                        <td>
                                            <?php if ($is_paid): ?>
                                                <a href="<?php echo base_url(); ?>index.php?admin/student_invoice_receipt/<?php echo $row['invoice_id']; ?>"
                                                   target="_blank"
                                                   class="btn btn-success btn-xs">
                                                    <i class="entypo-doc-text"></i> Receipt
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo base_url(); ?>index.php?admin/student_invoice_receipt/<?php echo $row['invoice_id']; ?>"
                                                   target="_blank"
                                                   class="btn btn-default btn-xs"
                                                   title="Partial receipt — outstanding balance">
                                                    <i class="entypo-doc-text"></i> Receipt
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $("#table_export").DataTable();
});
</script>