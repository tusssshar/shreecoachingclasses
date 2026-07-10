<div class="card card-info card-outline mb-4">
    <div class="card-header">
        <div class="card-title">Add Student</div>
    </div>

    <div class="card-body">

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

				<form action="<?php echo base_url();?>index.php?admin/student/create/" method="POST" enctype="multipart/form-data" class="form-horizontal form-groups-bordered validate">

					<!-- First Name -->
					<div class="form-group">
						<label class="col-sm-3 control-label">First Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="first_name" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
						</div>
					</div>

					<!-- Middle Name -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Middle Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="middle_name" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
						</div>
					</div>

					<!-- Last Name -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Last Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="last_name" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
						</div>
					</div>

					<!-- DOB -->
					<div class="form-group">
						<label class="col-sm-3 control-label">DOB</label>
						<div class="col-sm-5">
							<input type="date" class="form-control" name="birthday" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
						</div>
					</div>

					<!-- Age -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Age</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="age" readonly>
						</div>
					</div>

					<!-- Father Name -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Father Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="father_name">
						</div>
					</div>

					<!-- Father Mobile -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Father Mobile</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="fmobile" pattern="^[0-9]{10}$" title="Please enter a 10-digit mobile number" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
						</div>
					</div>

					<!-- Mother Name -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Mother Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="mother_name">
						</div>
					</div>

					<!-- Mother Mobile -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Mother Mobile</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="mmobile" pattern="^[0-9]{10}$" title="Please enter a 10-digit mobile number">
						</div>
					</div>

					<!-- Emergency Contact -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Emergency Contact</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="emergency_contact" placeholder="Name &amp; phone of someone to contact in emergencies">
						</div>
					</div>

					<!-- Email -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Email</label>
						<div class="col-sm-5">
							<input type="email" class="form-control" name="email" data-validate="required,email" data-message-required="<?php echo get_phrase('value_required');?>">
						</div>
					</div>

					<!-- Home Address -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Home Address</label>
						<div class="col-sm-5">
							<textarea class="form-control" name="home" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"></textarea>
						</div>
					</div>

					<!-- Standard -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Standard</label>
						<div class="col-sm-5">
							<select class="form-control" name="class_id" id="add_class_id" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" onchange="toggleStudentMobile()">
								<option value="">-Select-</option>
								<?php
								$classes = $this->db->get('class')->result_array();
								usort($classes, function($a, $b) {
									return strnatcasecmp($a['name_numeric'], $b['name_numeric']);
								});
								foreach ($classes as $class):
									$nm = (int)($class['name_numeric'] ?? 0);
								?>
									<option value="<?php echo $class['class_id']; ?>" data-class-numeric="<?php echo $nm; ?>"><?php echo $class['name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<!-- Student Mobile (only when class >= 10) -->
					<div class="form-group" id="add_student_mobile_row" style="display:none;">
						<label class="col-sm-3 control-label">Student Mobile</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="student_mobile" pattern="^[0-9]{10}$" title="10-digit mobile number">
							<small class="text-muted">Shown for Class 10 and above only.</small>
						</div>
					</div>
					<script>
					function toggleStudentMobile() {
						var sel = document.getElementById('add_class_id');
						var row = document.getElementById('add_student_mobile_row');
						if (!sel || !row) return;
						var opt = sel.options[sel.selectedIndex];
						var n = opt ? parseInt(opt.getAttribute('data-class-numeric') || '0', 10) : 0;
						row.style.display = (n >= 10) ? '' : 'none';
					}
					document.addEventListener('DOMContentLoaded', toggleStudentMobile);
					</script>

					<!-- Medium -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Medium</label>
						<div class="col-sm-5">
							<?php $this->load->model('crud_model'); ?>
							<select class="form-control" name="medium" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
								<option value="">-Select-</option>
								<?php foreach ($this->crud_model->get_lookup_values('medium', array('English','Hindi')) as $opt): ?>
									<option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<!-- Board -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Board</label>
						<div class="col-sm-5">
							<select class="form-control" name="board" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
								<option value="">-Select-</option>
								<?php
								$boards = $this->db->order_by('sort_order', 'asc')->order_by('name', 'asc')->get('board')->result_array();
								foreach ($boards as $board):
								?>
									<option value="<?php echo $board['name']; ?>"><?php echo $board['name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<!-- Gender -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Gender</label>
						<div class="col-sm-5">
							<label class="radio-inline">
								<input type="radio" name="sex" value="male" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>"> Male
							</label>
							<label class="radio-inline">
								<input type="radio" name="sex" value="female" data-validate="required"> Female
							</label>
						</div>
					</div>

					<!-- School -->
					<div class="form-group">
						<label class="col-sm-3 control-label">School Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="school">
						</div>
					</div>

					<!-- Alumni -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Alumni</label>
						<div class="col-sm-5">
							<label class="radio-inline">
								<input type="radio" name="is_alumni" value="1"> Yes
							</label>
							<label class="radio-inline">
								<input type="radio" name="is_alumni" value="0" checked> No
							</label>
						</div>
					</div>

					<!-- Academic Year (Batch) -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Academic Year</label>
						<div class="col-sm-5">
							<?php
								$__ay_year  = (int)date('Y');
								$__ay_month = (int)date('n');
								$current_ay = $__ay_month >= 4 ? ($__ay_year . '-' . ($__ay_year + 1)) : (($__ay_year - 1) . '-' . $__ay_year);
								$__ays = $this->crud_model->academic_years(array($current_ay));
							?>
							<select class="form-control" name="academic_year" required>
								<?php foreach ($__ays as $ay): ?>
									<option value="<?php echo htmlspecialchars($ay); ?>" <?php if ($ay === $current_ay) echo 'selected'; ?>>
										<?php echo htmlspecialchars($ay); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<small class="text-muted">Manage available years via <strong>Manage Academic Year</strong>.</small>
						</div>
					</div>

					<!-- PAYMENT SECTION -->
					<hr>
					<h4 style="margin-left:20px;">Payment Section</h4>

					<div class="form-group">
						<label class="col-sm-3 control-label">Total Fees</label>
						<div class="col-sm-5">
							<input type="number" step="0.01" class="form-control" name="total_fees">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">Payment</label>
						<div class="col-sm-2">
							<input type="number" step="0.01" class="form-control" name="payment1_amount" placeholder="Amount">
						</div>
						<div class="col-sm-3">
							<input type="date" class="form-control" name="payment1_date" value="<?php echo date('Y-m-d'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">Type of Payment</label>
						<div class="col-sm-5">
							<select class="form-control" name="payment1_type">
								<option value="">-Select-</option>
								<?php foreach ($this->crud_model->get_lookup_values('payment_type', array('Admission','Installment')) as $opt): ?>
									<option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">Mode of Payment</label>
						<div class="col-sm-5">
							<select class="form-control payment-mode" data-pay-index="1" name="payment1_mode" onchange="togglePaymentDetails(1)">
								<option value="">— e.g. UPI / Cash / Cheque —</option>
								<?php foreach ($this->crud_model->get_lookup_values('payment_mode', array('Cash','Online','UPI','Cheque')) as $opt): ?>
									<option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<!-- Transaction details (shown when mode is Online / UPI / NEFT / etc.) -->
					<div class="form-group payment-txn-row" id="payment1_txn_row" style="display:none;">
						<label class="col-sm-3 control-label">Transaction / Reference ID</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="payment1_transaction_id" placeholder="UPI Ref / NEFT UTR / Card txn id">
						</div>
					</div>

					<!-- Cheque details (shown when mode is Cheque) -->
					<div class="form-group payment-cheque-row" id="payment1_cheque_row" style="display:none;">
						<label class="col-sm-3 control-label">Cheque Number</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" name="payment1_cheque_number" placeholder="123456">
						</div>
						<label class="col-sm-2 control-label">Bank</label>
						<div class="col-sm-3">
							<input type="text" class="form-control" name="payment1_cheque_bank" placeholder="Bank name">
						</div>
					</div>
					<div class="form-group payment-cheque-row" id="payment1_cheque_date_row" style="display:none;">
						<label class="col-sm-3 control-label">Cheque Date</label>
						<div class="col-sm-3">
							<input type="date" class="form-control" name="payment1_cheque_date">
						</div>
					</div>

					<script>
					function togglePaymentDetails(idx) {
						var sel = document.querySelector('.payment-mode[data-pay-index="' + idx + '"]');
						if (!sel) return;
						var mode = (sel.value || '').toLowerCase();
						var isCheque = mode === 'cheque' || mode === 'check';
						var isTxn    = mode === 'online' || mode === 'upi' || mode === 'neft' || mode === 'rtgs' || mode === 'card' || mode === 'imps';
						var ids = [
							'payment' + idx + '_txn_row',
							'payment' + idx + '_cheque_row',
							'payment' + idx + '_cheque_date_row',
						];
						ids.forEach(function (id) { var el = document.getElementById(id); if (el) el.style.display = 'none'; });
						if (isCheque) {
							var c1 = document.getElementById('payment' + idx + '_cheque_row');
							var c2 = document.getElementById('payment' + idx + '_cheque_date_row');
							if (c1) c1.style.display = '';
							if (c2) c2.style.display = '';
						} else if (isTxn) {
							var t = document.getElementById('payment' + idx + '_txn_row');
							if (t) t.style.display = '';
						}
					}
					</script>

					<!-- DOCUMENT SECTION -->
					<hr>
					<h4 style="margin-left:20px;">Documents Upload</h4>

					<div class="form-group">
						<label class="col-sm-3 control-label">Student Photo</label>
						<div class="col-sm-5">
							<input type="file" class="form-control" name="student_photo">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">Government Identity</label>
						<div class="col-sm-5">
							<input type="file" class="form-control" name="government_identity">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label">Last Year Marksheet</label>
						<div class="col-sm-5">
							<input type="file" class="form-control" name="mark_sheet">
						</div>
					</div>

					<!-- Submit -->
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-5">
							<button type="submit" class="btn btn-success btn-sm btn-icon icon-left">
								<i class="entypo-plus"></i> Submit
							</button>
						</div>
					</div>

				</form>

    </div>
</div>

<script type="text/javascript">
function get_class_sections(class_id) {
	$.ajax({
		url: '<?php echo base_url();?>index.php?admin/get_class_section/' + class_id,
		success: function(response) {
			jQuery('#section_selector_holder').html(response);
		}
	});
}

function calculateAgeFromDob(dobString) {
    if (!dobString) return '';
    var dob = new Date(dobString);
    if (isNaN(dob.getTime())) return '';
    var today = new Date();
    var age = today.getFullYear() - dob.getFullYear();
    var m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    return age;
}

jQuery(document).ready(function() {
    var $dob = jQuery('input[name="birthday"]');
    var $age = jQuery('input[name="age"]');
    function updateAgeField() {
        $age.val(calculateAgeFromDob($dob.val()));
    }
    $dob.on('change input', updateAgeField);
    updateAgeField();
});
</script>
