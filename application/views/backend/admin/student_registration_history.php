<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">Student Registration History (Read-Only Archive)</div>
    </div>
    <div class="panel-body">

        <?php if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?></div>
        <?php endif; ?>

        <div class="alert alert-info">
            <i class="entypo-info"></i> <strong>Archive View:</strong> This shows all previous student registrations from past academic years. 
            All information is <strong>read-only</strong> and cannot be edited. To re-register a student, 
            go to <a href="<?php echo base_url(); ?>index.php?admin/student_reregister"><strong>Re-register Student</strong></a>.
        </div>

        <!-- SEARCH FORM -->
        <form method="POST" style="margin-bottom:18px;">
            <div class="form-inline">
                <div class="form-group">
                    <input type="text" name="q" class="form-control" style="min-width:320px;"
                           value="<?php echo htmlspecialchars($q); ?>"
                           placeholder="Search by Student ID, Name, or Mobile...">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="entypo-search"></i> Search History
                </button>
                <?php if ($q !== ''): ?>
                    <a href="<?php echo base_url(); ?>index.php?admin/student_registration_history" class="btn btn-default">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($q !== ''): ?>
            <h4>Archive Results for "<?php echo htmlspecialchars($q); ?>"
                <small class="text-muted"><?php echo count($matches); ?> record(s)</small>
            </h4>

            <?php if (empty($matches)): ?>
                <div class="alert alert-warning">No previous registrations found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th>#</th>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Class / Standard</th>
                                <th>Academic Year</th>
                                <th>Father Mobile</th>
                                <th>Total Fees</th>
                                <th>Amount Paid</th>
                                <th>Pending Fees</th>
                                <th>Details (Read-Only)</th>
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
                                <td class="text-right">₹<?php echo number_format((float)($s['total_fees'] ?? 0), 2); ?></td>
                                <td class="text-right text-success"><strong>₹<?php echo number_format((float)($s['payment_done'] ?? 0), 2); ?></strong></td>
                                <td class="text-right">
                                    <?php $pending = $s['pending_fees'] ?? 0; ?>
                                    <?php if ($pending > 0): ?>
                                        <span class="label label-danger"><strong>₹<?php echo number_format($pending, 2); ?></strong></span>
                                    <?php else: ?>
                                        <span class="label label-success">Paid ✓</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-xs"
                                            onclick='showStudentDetails(<?php echo json_encode($s); ?>)'>
                                        <i class="entypo-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- STUDENT DETAILS MODAL (READ-ONLY) -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Student Record — <span id="modal_name"></span></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="entypo-info"></i> <strong>Archive Record (Read-Only):</strong> This is a historical registration record. 
                    All information is frozen and cannot be modified.
                </div>

                <!-- BASIC INFO -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Personal Information (Archived)</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <span id="modal_name_full"></span></p>
                                <p><strong>DOB:</strong> <span id="modal_dob"></span></p>
                                <p><strong>Gender:</strong> <span id="modal_sex"></span></p>
                                <p><strong>Email:</strong> <span id="modal_email"></span></p>
                                <p><strong>Address:</strong> <span id="modal_address"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Father Name:</strong> <span id="modal_father_name"></span></p>
                                <p><strong>Father Mobile:</strong> <span id="modal_fmobile"></span></p>
                                <p><strong>Mother Name:</strong> <span id="modal_mother_name"></span></p>
                                <p><strong>Mother Mobile:</strong> <span id="modal_mmobile"></span></p>
                                <p><strong>Student Mobile:</strong> <span id="modal_student_mobile"></span></p>
                                <p><strong>Emergency Contact:</strong> <span id="modal_emergency"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACADEMIC INFO -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Academic Information (Archived)</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Class/Standard:</strong> <span id="modal_standard"></span></p>
                                <p><strong>Academic Year:</strong> <span id="modal_ay"></span></p>
                                <p><strong>Medium:</strong> <span id="modal_medium"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Board:</strong> <span id="modal_board"></span></p>
                                <p><strong>School:</strong> <span id="modal_school"></span></p>
                                <p><strong>Status:</strong> <span id="modal_status"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FEES INFO -->
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <div class="panel-title">Fees Information (Archived)</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Total Fees:</strong><br><span id="modal_total_fees" style="font-size: 18px; color: #007bff;"></span></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Amount Paid:</strong><br><span id="modal_paid" style="font-size: 18px; color: #28a745;"></span></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Pending Fees:</strong><br><span id="modal_pending" style="font-size: 18px;"></span></p>
                            </div>
                        </div>
                        <hr>
                        <h4>Payment History</h4>
                        <div id="modal_payment_history"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function ($) {
    var $m = $('#detailsModal');
    if ($m.length && $m.parent().prop('tagName') !== 'BODY') $m.appendTo('body');
});

function showStudentDetails(student) {
    var totalFees = parseFloat(student.total_fees) || 0;
    var paid = parseFloat(student.payment_done) || 0;
    var pending = Math.max(0, totalFees - paid);
    
    document.getElementById('modal_name').textContent = student.name;
    document.getElementById('modal_name_full').textContent = student.name;
    document.getElementById('modal_dob').textContent = student.birthday || '-';
    document.getElementById('modal_sex').textContent = student.sex || '-';
    document.getElementById('modal_email').textContent = student.email || '-';
    document.getElementById('modal_address').textContent = student.address || '-';
    document.getElementById('modal_father_name').textContent = student.father_name || '-';
    document.getElementById('modal_fmobile').textContent = student.fmobile || '-';
    document.getElementById('modal_mother_name').textContent = student.mother_name || '-';
    document.getElementById('modal_mmobile').textContent = student.mmobile || '-';
    document.getElementById('modal_student_mobile').textContent = student.student_mobile || '-';
    document.getElementById('modal_emergency').textContent = student.emergency_contact || '-';
    
    document.getElementById('modal_standard').textContent = student.standard || '-';
    document.getElementById('modal_ay').textContent = student.academic_year || '-';
    document.getElementById('modal_medium').textContent = student.medium || '-';
    document.getElementById('modal_board').textContent = student.board || '-';
    document.getElementById('modal_school').textContent = student.school || '-';
    document.getElementById('modal_status').textContent = 'Archived';
    
    document.getElementById('modal_total_fees').textContent = '₹' + totalFees.toFixed(2);
    document.getElementById('modal_paid').textContent = '₹' + paid.toFixed(2);
    
    var $pending = jQuery('#modal_pending');
    if (pending > 0) {
        $pending.html('<span class="label label-danger" style="font-size: 14px;">₹' + pending.toFixed(2) + '</span>');
    } else {
        $pending.html('<span class="label label-success" style="font-size: 14px;">Fully Paid ✓</span>');
    }
    
    renderPaymentHistory(student.payment_history || []);
    jQuery('#detailsModal').modal('show');
}

function renderPaymentHistory(payments) {
    var holder = document.getElementById('modal_payment_history');
    if (!holder) return;
    if (!payments.length) {
        holder.innerHTML = '<div class="alert alert-warning" style="margin-bottom:0;">No payment records found for this registration.</div>';
        return;
    }

    var rows = payments.map(function (p, idx) {
        var amount = parseFloat(p.amount) || 0;
        var date = '-';
        if (p.timestamp) {
            var d = new Date(parseInt(p.timestamp, 10) * 1000);
            if (!isNaN(d.getTime())) date = d.toLocaleDateString('en-IN');
        }
        var extra = [];
        if (p.transaction_id) extra.push('Txn: ' + escapeHtml(p.transaction_id));
        if (p.cheque_number) extra.push('Cheque: ' + escapeHtml(p.cheque_number));
        if (p.cheque_bank) extra.push('Bank: ' + escapeHtml(p.cheque_bank));
        if (p.cheque_date) extra.push('Cheque date: ' + escapeHtml(p.cheque_date));

        return '<tr>' +
            '<td>' + (idx + 1) + '</td>' +
            '<td>' + date + '</td>' +
            '<td>' + escapeHtml(p.payment_type || '-') + '</td>' +
            '<td>' + escapeHtml(p.method || '-') + '</td>' +
            '<td class="text-right"><strong>Rs. ' + amount.toFixed(2) + '</strong></td>' +
            '<td>' + (extra.length ? extra.join('<br>') : '-') + '</td>' +
            '</tr>';
    }).join('');

    holder.innerHTML = '<div class="table-responsive">' +
        '<table class="table table-bordered table-striped">' +
        '<thead><tr><th>#</th><th>Date</th><th>Type</th><th>Mode</th><th class="text-right">Amount</th><th>Reference</th></tr></thead>' +
        '<tbody>' + rows + '</tbody></table></div>';
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (c) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[c];
    });
}
</script>
