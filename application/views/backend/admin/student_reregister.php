<hr>
<style>
/* =========================
   Re-register PAGE layout
   ========================= */
.rer-page .panel-gradient { margin-bottom: 12px; }
.rer-page .form-inline .form-group { margin-right: 8px; }
.rer-page .results-table { font-size: 13px; }
.rer-page .results-table th { white-space: nowrap; background:#f4f6fa; }
.rer-page .results-table td { vertical-align: middle; }
.rer-page .lead-note { background:#f8f9fb; border-left:3px solid #2c5298; padding:8px 12px; margin-bottom:14px; }

/* =========================================================================
   Re-register MODAL — whole-page scroll (treat the modal like inline content)

   Previous attempts pinned .modal-dialog with position:fixed and absolute-
   positioned header/body/footer, or relied on Bootstrap 3's default
   scroll-container behaviour. On long forms / small viewports both dropped
   scroll halfway through ("Type of Payment" cut-off).

   Fix: nuke every height/overflow constraint Bootstrap puts on the modal
   stack, allow body to scroll, force an always-visible scrollbar on the
   modal container, and disable Bootstrap's modal-dialog transform that
   establishes a stacking context (clips children on some browsers).
   ========================================================================= */
body.modal-open {
    overflow: auto !important;            /* re-enable page scrolling */
    padding-right: 0 !important;          /* avoid layout shift from scrollbar removal */
}
#rerModal.modal {
    position: fixed;
    top: 0; right: 0; bottom: 0; left: 0;
    overflow-y: scroll !important;        /* always show scrollbar -> obvious it scrolls */
    -webkit-overflow-scrolling: touch;    /* smooth scroll on iOS */
    z-index: 1050;
}
#rerModal .modal-dialog {
    width: 94%;
    max-width: 1120px;
    margin: 24px auto 48px;               /* extra bottom margin so footer isn't at edge */
    /* No position / height / transform — flow naturally */
}
#rerModal .modal-content {
    border-radius: 6px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.25);
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
}
#rerModal .modal-header {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
    border-radius: 6px 6px 0 0;
}
#rerModal .modal-body   {
    padding: 14px 18px;
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;         /* no inner scroll container */
}
#rerModal .modal-footer {
    padding: 10px 16px;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    border-radius: 0 0 6px 6px;
}

/* Defeat Bootstrap 3's modal-dialog transform — it establishes a stacking
   context that clips children inside scrollable ancestors on some browsers. */
#rerModal.fade .modal-dialog,
#rerModal.in   .modal-dialog {
    -webkit-transform: none !important;
        -ms-transform: none !important;
         -o-transform: none !important;
            transform: none !important;
}

/* Inner form spacing — unchanged */
#rerModal .panel { margin-bottom: 12px; border-radius: 4px; }
#rerModal .panel-heading { padding: 7px 12px; background:#eef2f7; }
#rerModal .panel-title { font-size: 14px; font-weight: 700; }
#rerModal .panel-body { padding: 12px 14px; }
#rerModal .form-group { margin-bottom: 10px; }
#rerModal .control-label { padding-top: 6px; font-size: 12.5px; font-weight: 600; }
#rerModal .form-control { height: 32px; padding: 4px 8px; font-size: 13px; }
#rerModal textarea.form-control { height: 60px; min-height: 60px; }
#rerModal .alert { padding: 8px 12px; margin-bottom: 12px; font-size: 13px; }
#rerModal p { margin-bottom: 6px; }

@media (max-width: 991px) {
    #rerModal .modal-dialog { width: 96%; margin: 16px auto 48px; }
    #rerModal .modal-body { padding: 12px 14px; }
}
@media (max-width: 767px) {
    #rerModal .modal-dialog { width: 98%; margin: 8px auto 40px; }
    #rerModal .control-label { text-align: left !important; padding-top: 2px; }
}
</style>

<div class="rer-page">
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">Re-register Student</div>
    </div>
    <div class="panel-body">

        <?php if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?></div>
        <?php endif; ?>

        <p class="text-muted">
            Search for <strong>currently active students</strong> by Student ID, name, or mobile number. Pick one to create a fresh admission
            record for a new academic year. All personal and family details will be carried forward automatically.
            <br><br>
            <strong>Note:</strong> Previous/archived registrations are <strong>not shown here</strong>. 
            To view past enrollment records, see <a href="<?php echo base_url(); ?>index.php?admin/student_registration_history"><strong>Student Registration History (Read-Only)</strong></a>.
        </p>

        <!-- SEARCH FORM -->
        <form method="POST" style="margin-bottom:18px;">
            <div class="form-inline">
                <div class="form-group">
                    <input type="text" name="q" class="form-control" style="min-width:320px;"
                           value="<?php echo htmlspecialchars($q); ?>"
                           placeholder="Student ID, Name, or Mobile">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="entypo-search"></i> Search
                </button>
                <?php if ($q !== ''): ?>
                    <a href="<?php echo base_url(); ?>index.php?admin/student_reregister" class="btn btn-default">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($q !== ''): ?>
            <h4>Results for "<?php echo htmlspecialchars($q); ?>"
                <small class="text-muted"><?php echo count($matches); ?> match(es)</small>
            </h4>

            <?php if (empty($matches)): ?>
                <div class="alert alert-warning">No students found.</div>
            <?php else: ?>
                <table class="table table-bordered table-striped results-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class / Standard</th>
                            <th>Batch</th>
                            <th>Father Mobile</th>
                            <th>Total Fees</th>
                            <th>Pending Fees</th>
                            <th style="width:140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; foreach ($matches as $s): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><strong>STU-<?php echo str_pad((int)$s['student_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($s['standard'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($s['academic_year'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($s['fmobile'] ?? '-'); ?></td>
                            <td><?php echo '₹' . number_format((float)($s['total_fees'] ?? 0), 2); ?></td>
                            <td>
                                <?php $pending = $s['pending_fees'] ?? 0; ?>
                                <?php if ($pending > 0): ?>
                                    <span class="label label-danger"><strong>₹<?php echo number_format($pending, 2); ?></strong></span>
                                <?php else: ?>
                                    <span class="label label-success">Paid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm"
                                        onclick='openReregister(<?php echo json_encode($s); ?>)'>
                                    <i class="entypo-arrows-ccw"></i> Re-register
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</div><!-- /.rer-page -->

<!-- RE-REGISTER MODAL -->
<div class="modal fade" id="rerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo base_url(); ?>index.php?admin/student_reregister/save" enctype="multipart/form-data" class="form-horizontal form-groups-bordered">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Re-register — <span id="rer_name"></span></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="previous_student_id" id="rer_prev_id">

                    <div class="alert alert-info">
                        <strong>Note:</strong> A new student record will be created for the chosen Academic Year. 
                        All personal and family details from the previous registration are automatically copied over.
                        The previous enrollment is archived and can be viewed in <a href="<?php echo base_url(); ?>index.php?admin/student_registration_history"><strong>Registration History</strong></a>.
                    </div>

                    <!-- EDITABLE STUDENT INFO -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">Student Information</div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">First Name</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="first_name" id="rer_first_name" required></div>
                                <label class="col-sm-2 control-label">Middle</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="middle_name" id="rer_middle_name"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Last Name</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="last_name" id="rer_last_name"></div>
                                <label class="col-sm-2 control-label">DOB</label>
                                <div class="col-sm-3"><input type="date" class="form-control" name="birthday" id="rer_birthday" onchange="updateRerAge()"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Age</label>
                                <div class="col-sm-3"><input type="text" class="form-control" id="rer_age" readonly></div>
                                <label class="col-sm-2 control-label">Gender</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline"><input type="radio" name="sex" value="male" id="rer_sex_male"> Male</label>
                                    <label class="radio-inline"><input type="radio" name="sex" value="female" id="rer_sex_female"> Female</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Father Name</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="father_name" id="rer_father_name"></div>
                                <label class="col-sm-2 control-label">Father Mobile</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="fmobile" id="rer_fmobile" pattern="^[0-9]{10}$"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Mother Name</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="mother_name" id="rer_mother_name"></div>
                                <label class="col-sm-2 control-label">Mother Mobile</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="mmobile" id="rer_mmobile" pattern="^[0-9]{10}$"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Emergency Contact</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="emergency_contact" id="rer_emergency_contact"></div>
                                <label class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-3"><input type="email" class="form-control" name="email" id="rer_email"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Home Address</label>
                                <div class="col-sm-8"><textarea class="form-control" name="home" id="rer_home"></textarea></div>
                            </div>
                        </div>
                    </div>

                    <!-- PREVIOUS ACADEMY INFO (TO BE CARRIED FORWARD) -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">Information to be Carried Forward (from Previous Enrollment)</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Previous Class/Standard:</strong> <span id="rer_prev_class"></span></p>
                                    <p><strong>Previous Academic Year:</strong> <span id="rer_prev_ay"></span></p>
                                    <p><strong>Previous Medium:</strong> <span id="rer_prev_medium"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Previous Board:</strong> <span id="rer_prev_board"></span></p>
                                    <p><strong>School Name:</strong> <span id="rer_prev_school"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FEES INFORMATION -->
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <div class="panel-title">Previous Fees Information</div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Total Fees:</strong> <span id="rer_prev_total_fees" class="text-primary"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Amount Paid:</strong> <span id="rer_prev_paid" class="text-success"></span></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="padding: 10px; background-color: #fff3cd; border-left: 4px solid #ff6b6b; border-radius: 3px;">
                                        <h4 style="margin: 0 0 10px 0;">
                                            <strong>Pending Fees:</strong> 
                                            <span id="rer_pending_fees" class="text-danger" style="font-size: 18px;"></span>
                                        </h4>
                                        <label style="margin: 0; cursor: pointer;">
                                            <input type="checkbox" name="carry_forward_pending_fees" id="rer_carry_forward" value="1">
                                            <strong>☑ Carry forward pending fees to new registration</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">Previous Payment History</div>
                        </div>
                        <div class="panel-body" id="rer_payment_history"></div>
                    </div>

                    <!-- RE-REGISTRATION DETAILS -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">Re-registration Details</div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Academic Year</label>
                                <div class="col-sm-4">
                                    <select name="academic_year" class="form-control" required>
                                        <?php foreach ($academic_years as $ay): ?>
                                            <option value="<?php echo htmlspecialchars($ay); ?>" <?php if ($ay === $default_ay) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($ay); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Managed under <strong>Manage Academic Year</strong>.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">New Class / Standard</label>
                                <div class="col-sm-5">
                                    <select name="class_id" id="rer_class_id" class="form-control" onchange="toggleRerStudentMobile()" required>
                                        <option value="">-Select-</option>
                                        <?php foreach ($classes as $c): ?>
                                            <option value="<?php echo (int)$c['class_id']; ?>" data-class-numeric="<?php echo (int)($c['name_numeric'] ?? 0); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="rer_student_mobile_row" style="display:none;">
                                <label class="col-sm-3 control-label">Student Mobile</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="student_mobile" id="rer_student_mobile" pattern="^[0-9]{10}$">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Medium</label>
                                <div class="col-sm-4">
                                    <?php $this->load->model('crud_model'); ?>
                                    <select name="medium" id="rer_medium" class="form-control">
                                        <option value="">-Keep previous-</option>
                                        <?php foreach ($this->crud_model->get_lookup_values('medium', array('English','Hindi')) as $opt): ?>
                                            <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Board</label>
                                <div class="col-sm-4">
                                    <select name="board" id="rer_board" class="form-control">
                                        <option value="">-Keep previous-</option>
                                        <?php foreach ($boards as $b): ?>
                                            <option value="<?php echo htmlspecialchars($b['name']); ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">School Name</label>
                                <div class="col-sm-5">
                                    <input type="text" name="school" id="rer_school" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Total Fees (new year)</label>
                                <div class="col-sm-4">
                                    <input type="number" step="0.01" min="0" name="total_fees" id="rer_new_total_fees" class="form-control" value="0" required>
                                    <small id="rer_fees_note" class="text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <div class="panel-title">New Payment</div>
                        </div>
                        <div class="panel-body">
                            <!-- Outstanding-balance summary surfaced inside the payment panel
                                 so the admin sees it while collecting the new payment. -->
                            <div class="alert alert-warning" style="margin-bottom:14px; padding:10px 12px;">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <strong>Previous Total Fees:</strong><br>
                                        <span id="rer_pay_prev_total" style="font-size:14px;">&#8377; 0.00</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Paid So Far:</strong><br>
                                        <span id="rer_pay_prev_paid" class="text-success" style="font-size:14px;">&#8377; 0.00</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong>Remaining Balance:</strong><br>
                                        <span id="rer_pay_prev_due" class="text-danger" style="font-size:16px; font-weight:700;">&#8377; 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Payment</label>
                                <div class="col-sm-2"><input type="number" step="0.01" class="form-control" name="payment1_amount" placeholder="Amount"></div>
                                <div class="col-sm-3"><input type="date" class="form-control" name="payment1_date" value="<?php echo date('Y-m-d'); ?>"></div>
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
                                    <small class="text-muted">Add new modes via <strong>Master Data &rarr; Mode of Payment</strong>.</small>
                                </div>
                            </div>
                            <div class="form-group" id="payment1_txn_row" style="display:none;">
                                <label class="col-sm-3 control-label">Transaction / Reference ID</label>
                                <div class="col-sm-5"><input type="text" class="form-control" name="payment1_transaction_id"></div>
                            </div>
                            <div class="form-group" id="payment1_cheque_row" style="display:none;">
                                <label class="col-sm-3 control-label">Cheque Number</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="payment1_cheque_number"></div>
                                <label class="col-sm-2 control-label">Bank</label>
                                <div class="col-sm-3"><input type="text" class="form-control" name="payment1_cheque_bank"></div>
                            </div>
                            <div class="form-group" id="payment1_cheque_date_row" style="display:none;">
                                <label class="col-sm-3 control-label">Cheque Date</label>
                                <div class="col-sm-3"><input type="date" class="form-control" name="payment1_cheque_date"></div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading"><div class="panel-title">Documents Upload</div></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Student Photo</label>
                                <div class="col-sm-5"><input type="file" class="form-control" name="student_photo"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Government Identity</label>
                                <div class="col-sm-5"><input type="file" class="form-control" name="government_identity"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Last Year Marksheet</label>
                                <div class="col-sm-5"><input type="file" class="form-control" name="mark_sheet"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="entypo-check"></i> Confirm Re-registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function ($) {
    // Same Bootstrap-3 backdrop fix used on the attendance export modal
    var $m = $('#rerModal');
    if ($m.length && $m.parent().prop('tagName') !== 'BODY') $m.appendTo('body');
});

function openReregister(student) {
    var totalFees = parseFloat(student.total_fees) || 0;
    var paid = parseFloat(student.payment_done) || 0;
    var pending = Math.max(0, totalFees - paid);
    
    // Display student name
    document.getElementById('rer_name').textContent = student.name;
    document.getElementById('rer_prev_id').value    = student.student_id;

    var parts = (student.name || '').split(' ');
    setValue('rer_first_name', student.first_name || parts.shift() || '');
    setValue('rer_middle_name', student.middle_name || (parts.length > 1 ? parts.slice(0, -1).join(' ') : ''));
    setValue('rer_last_name', student.last_name || (parts.length ? parts[parts.length - 1] : ''));
    setValue('rer_birthday', student.birthday || '');
    setValue('rer_father_name', student.father_name || '');
    document.getElementById('rer_father_name').placeholder =
        (student.father_name && student.father_name.trim() !== '')
            ? '' : 'Previous record had no father name — please enter';
    setValue('rer_fmobile', student.fmobile || '');
    setValue('rer_mother_name', student.mother_name || '');
    setValue('rer_mmobile', student.mmobile || '');
    setValue('rer_emergency_contact', student.emergency_contact || '');
    setValue('rer_email', student.email || '');
    setValue('rer_home', student.address || '');
    setValue('rer_student_mobile', student.student_mobile || '');
    setValue('rer_school', student.school || '');
    jQuery('input[name="sex"]').prop('checked', false);
    if ((student.sex || '').toLowerCase() === 'male') jQuery('#rer_sex_male').prop('checked', true);
    if ((student.sex || '').toLowerCase() === 'female') jQuery('#rer_sex_female').prop('checked', true);
    updateRerAge();
    
    // Display previous academy info (to be carried forward)
    document.getElementById('rer_prev_class').textContent = student.standard || '-';
    document.getElementById('rer_prev_ay').textContent = student.academic_year || '-';
    document.getElementById('rer_prev_medium').textContent = student.medium || '-';
    document.getElementById('rer_prev_board').textContent = student.board || '-';
    document.getElementById('rer_prev_school').textContent = student.school || '-';
    
    // Display fees information
    document.getElementById('rer_prev_total_fees').textContent = '₹' + totalFees.toFixed(2);
    document.getElementById('rer_prev_paid').textContent = '₹' + paid.toFixed(2);
    document.getElementById('rer_pending_fees').textContent = '₹' + pending.toFixed(2);

    // Mirror the same balance into the New Payment panel so the admin sees it
    // while collecting the new payment.
    var payTotalEl = document.getElementById('rer_pay_prev_total');
    var payPaidEl  = document.getElementById('rer_pay_prev_paid');
    var payDueEl   = document.getElementById('rer_pay_prev_due');
    if (payTotalEl) payTotalEl.textContent = '₹ ' + totalFees.toFixed(2);
    if (payPaidEl)  payPaidEl.textContent  = '₹ ' + paid.toFixed(2);
    if (payDueEl)   payDueEl.textContent   = '₹ ' + pending.toFixed(2);
    
    // Set new class and medium defaults
    if (student.class_id) document.getElementById('rer_class_id').value = student.class_id;
    if (student.medium)   document.getElementById('rer_medium').value   = student.medium;
    if (student.board)    document.getElementById('rer_board').value    = student.board;
    toggleRerStudentMobile();
    
    // Reset carry forward checkbox and show note about pending fees.
    // Pre-fill the new year's total fees with the PREVIOUS total_fees so an admin
    // who forgets to update can't accidentally save total_fees=0 (which would
    // cause a negative balance once any payment is recorded — see student 29).
    var $carry = jQuery('#rer_carry_forward');
    var $newFees = jQuery('#rer_new_total_fees');
    var $note = jQuery('#rer_fees_note');

    $carry.prop('checked', false);
    $newFees.val(totalFees > 0 ? totalFees.toFixed(2) : '');
    $newFees.attr('placeholder', totalFees > 0
        ? 'Defaulting to previous: ₹' + totalFees.toFixed(2) + ' — override if changed'
        : 'Enter new academic-year fees');

    if (pending > 0) {
        $carry.closest('div').closest('div').show();
        $note.text('(Pending from previous year: ₹' + pending.toFixed(2) + ' will be added on top if checkbox is selected.)');
    } else {
        $carry.closest('div').closest('div').hide();
        $note.text('');
    }

    renderRerPaymentHistory(student.payment_history || []);
    jQuery('#rerModal').modal('show');
}

// Client-side guard: payment_amount must not exceed new total_fees + carry-forward pending.
// Prevents the user from creating another -10000 record.
jQuery(document).on('submit', '#rerModal form', function (e) {
    var newFees = parseFloat(jQuery('#rer_new_total_fees').val()) || 0;
    var carry   = jQuery('#rer_carry_forward').is(':checked')
                ? (parseFloat(jQuery('#rer_pay_prev_due').text().replace(/[^\d.-]/g, '')) || 0)
                : 0;
    var pay = parseFloat(jQuery('input[name="payment1_amount"]').val()) || 0;
    var allowed = newFees + carry;

    if (pay > 0 && allowed <= 0) {
        e.preventDefault();
        alert('You entered a payment but the new academic-year Total Fees is 0.\n\n' +
              'Set the new Total Fees first (or tick "Carry forward pending fees") so the balance can be calculated correctly.');
        jQuery('#rer_new_total_fees').focus();
        return;
    }
    if (pay > allowed + 0.01) {
        e.preventDefault();
        alert('Payment (₹' + pay.toFixed(2) + ') is greater than Total Fees + carry-forward (₹' + allowed.toFixed(2) + ').\n\n' +
              'This would create a negative balance. Please correct one of the values.');
        jQuery('input[name="payment1_amount"]').focus();
        return;
    }
});

function setValue(id, value) {
    var el = document.getElementById(id);
    if (el) el.value = value || '';
}

function updateRerAge() {
    var dobEl = document.getElementById('rer_birthday');
    var ageEl = document.getElementById('rer_age');
    if (!dobEl || !ageEl || !dobEl.value) {
        if (ageEl) ageEl.value = '';
        return;
    }
    var dob = new Date(dobEl.value);
    if (isNaN(dob.getTime())) {
        ageEl.value = '';
        return;
    }
    var today = new Date();
    var age = today.getFullYear() - dob.getFullYear();
    var m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    ageEl.value = age;
}

function toggleRerStudentMobile() {
    var sel = document.getElementById('rer_class_id');
    var row = document.getElementById('rer_student_mobile_row');
    if (!sel || !row) return;
    var opt = sel.options[sel.selectedIndex];
    var n = opt ? parseInt(opt.getAttribute('data-class-numeric') || '0', 10) : 0;
    row.style.display = (n >= 10) ? '' : 'none';
}

function togglePaymentDetails(idx) {
    var sel = document.querySelector('.payment-mode[data-pay-index="' + idx + '"]');
    if (!sel) return;
    var mode = (sel.value || '').toLowerCase();
    var isCheque = mode === 'cheque' || mode === 'check';
    var isTxn = mode === 'online' || mode === 'upi' || mode === 'neft' || mode === 'rtgs' || mode === 'card' || mode === 'imps';
    ['payment' + idx + '_txn_row', 'payment' + idx + '_cheque_row', 'payment' + idx + '_cheque_date_row'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
    if (isCheque) {
        if (document.getElementById('payment' + idx + '_cheque_row')) document.getElementById('payment' + idx + '_cheque_row').style.display = '';
        if (document.getElementById('payment' + idx + '_cheque_date_row')) document.getElementById('payment' + idx + '_cheque_date_row').style.display = '';
    } else if (isTxn && document.getElementById('payment' + idx + '_txn_row')) {
        document.getElementById('payment' + idx + '_txn_row').style.display = '';
    }
}

jQuery(document).on('change', '#rer_class_id', toggleRerStudentMobile);

function renderRerPaymentHistory(payments) {
    var holder = document.getElementById('rer_payment_history');
    if (!holder) return;
    if (!payments.length) {
        holder.innerHTML = '<div class="alert alert-warning" style="margin-bottom:0;">No previous payment records found.</div>';
        return;
    }
    var rows = payments.map(function (p, idx) {
        var amount = parseFloat(p.amount) || 0;
        var date = '-';
        if (p.timestamp) {
            var d = new Date(parseInt(p.timestamp, 10) * 1000);
            if (!isNaN(d.getTime())) date = d.toLocaleDateString('en-IN');
        }
        return '<tr><td>' + (idx + 1) + '</td><td>' + date + '</td><td>' + escapeRerHtml(p.payment_type || '-') + '</td><td>' + escapeRerHtml(p.method || '-') + '</td><td class="text-right"><strong>Rs. ' + amount.toFixed(2) + '</strong></td></tr>';
    }).join('');
    holder.innerHTML = '<div class="table-responsive"><table class="table table-bordered table-striped">' +
        '<thead><tr><th>#</th><th>Date</th><th>Type</th><th>Mode</th><th class="text-right">Amount</th></tr></thead>' +
        '<tbody>' + rows + '</tbody></table></div>';
}

function escapeRerHtml(value) {
    return String(value).replace(/[&<>"']/g, function (c) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[c];
    });
}
</script>
