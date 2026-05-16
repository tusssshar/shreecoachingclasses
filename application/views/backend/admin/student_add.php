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
							<select class="form-control" name="class_id" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
								<option value="">-Select-</option>
								<?php
								$classes = $this->db->get('class')->result_array();
								usort($classes, function($a, $b) {
									return strnatcasecmp($a['name_numeric'], $b['name_numeric']);
								});
								foreach ($classes as $class):
								?>
									<option value="<?php echo $class['class_id']; ?>"><?php echo $class['name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

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
							<select class="form-control" name="payment1_mode">
								<option value="">-Select-</option>
								<?php foreach ($this->crud_model->get_lookup_values('payment_mode', array('Cash','Online','Cheque')) as $opt): ?>
									<option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

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
