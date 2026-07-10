<?php
$student = $this->db->get_where('student', array('student_id' => (int)$param2))->row_array();
if (!$student) { echo '<div class="alert alert-danger">Student not found.</div>'; return; }

$sum_row = $this->db->select_sum('amount')->where('student_id', $student['student_id'])->get('student_payment_history')->row();
$paid    = ($sum_row && $sum_row->amount) ? (float)$sum_row->amount : 0;
$total   = (float)($student['total_fees'] ?? 0);
$due     = max(0, $total - $paid);

$this->load->model('crud_model');
$pay_types = $this->crud_model->get_lookup_values('payment_type', array('Admission','Installment'));
$pay_modes = $this->crud_model->get_lookup_values('payment_mode', array('Cash','Online','UPI','Cheque'));
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="entypo-credit-card"></i>
                    Take Payment — <?php echo htmlspecialchars($student['name']); ?>
                </div>
            </div>
            <div class="panel-body">

                <div class="alert alert-info" style="margin-bottom:18px;">
                    <strong>Student:</strong> <?php echo htmlspecialchars($student['name']); ?>
                    &nbsp;|&nbsp; <strong>Class:</strong> <?php echo htmlspecialchars($student['standard'] ?? '-'); ?>
                    <br>
                    <strong>Total Fees:</strong> ₹ <?php echo number_format($total, 2); ?>
                    &nbsp;|&nbsp; <strong>Paid:</strong> ₹ <?php echo number_format($paid, 2); ?>
                    &nbsp;|&nbsp; <strong style="color:#c0392b;">Due:</strong> ₹ <?php echo number_format($due, 2); ?>
                </div>

                <form method="post"
                      action="<?php echo base_url(); ?>index.php?admin/student_payment_add/save/<?php echo (int)$student['student_id']; ?>"
                      class="form-horizontal form-groups-bordered validate" target="_top">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Amount</label>
                        <div class="col-sm-5">
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                                   value="<?php echo $due > 0 ? $due : ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Payment Date</label>
                        <div class="col-sm-5">
                            <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Type of Payment</label>
                        <div class="col-sm-5">
                            <select name="payment_type" class="form-control">
                                <option value="">-Select-</option>
                                <?php foreach ($pay_types as $t): ?>
                                    <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mode of Payment</label>
                        <div class="col-sm-5">
                            <select id="pmt_mode" name="payment_mode" class="form-control" onchange="ptToggle()">
                                <option value="">— e.g. UPI / Cash / Cheque —</option>
                                <?php foreach ($pay_modes as $m): ?>
                                    <option value="<?php echo htmlspecialchars($m); ?>"><?php echo htmlspecialchars($m); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Transaction ref (Online / UPI / NEFT) -->
                    <div class="form-group" id="pmt_txn_row" style="display:none;">
                        <label class="col-sm-3 control-label">Transaction / Reference ID</label>
                        <div class="col-sm-5">
                            <input type="text" name="transaction_id" class="form-control" placeholder="UPI Ref / NEFT UTR / Card txn id">
                        </div>
                    </div>

                    <!-- Cheque details -->
                    <div class="form-group" id="pmt_cheque_row1" style="display:none;">
                        <label class="col-sm-3 control-label">Cheque Number</label>
                        <div class="col-sm-3">
                            <input type="text" name="cheque_number" class="form-control" placeholder="123456">
                        </div>
                        <label class="col-sm-2 control-label">Bank</label>
                        <div class="col-sm-3">
                            <input type="text" name="cheque_bank" class="form-control" placeholder="Bank name">
                        </div>
                    </div>
                    <div class="form-group" id="pmt_cheque_row2" style="display:none;">
                        <label class="col-sm-3 control-label">Cheque Date</label>
                        <div class="col-sm-3">
                            <input type="date" name="cheque_date" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Description (optional)</label>
                        <div class="col-sm-7">
                            <input type="text" name="description" class="form-control" placeholder="e.g. Term 2 fees">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-5">
                            <button type="submit" class="btn btn-success">
                                <i class="entypo-check"></i> Save &amp; Print Receipt
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
function ptToggle() {
    var v = (document.getElementById('pmt_mode').value || '').toLowerCase();
    var isCheque = v === 'cheque' || v === 'check';
    var isTxn    = v === 'online' || v === 'upi' || v === 'neft' || v === 'rtgs' || v === 'card' || v === 'imps';
    document.getElementById('pmt_txn_row').style.display     = isTxn    ? '' : 'none';
    document.getElementById('pmt_cheque_row1').style.display = isCheque ? '' : 'none';
    document.getElementById('pmt_cheque_row2').style.display = isCheque ? '' : 'none';
}
</script>