<?php
$edit_data = $this->db->get_where('student', ['student_id' => $param2])->result_array();

foreach ($edit_data as $row):

    // ================= PAYMENT HISTORY =================
    $payments = $this->db->order_by('timestamp', 'asc')
        ->get_where('student_payment_history', ['student_id' => $row['student_id']])
        ->result_array();

    // ================= FEE SUMMARY (SAFE) =================
    $fee = isset($fee_summary) ? $fee_summary : [
        'total_fees' => 0,
        'paid' => 0,
        'remaining' => 0
    ];

    $classes = $this->db->get('class')->result_array();
    usort($classes, function($a, $b) {
        return strnatcasecmp($a['name_numeric'], $b['name_numeric']);
    });

    $selected_class_id = $row['class_id'];
    $legacy_standard = strtolower(trim($row['standard']));
    if (empty($selected_class_id) && preg_match('/^(\d+)/', $legacy_standard, $matches)) {
        $legacy_standard = (int) $matches[1];
    }
    foreach ($classes as $class) {
        $class_numeric = (int) $class['name_numeric'];
        if (empty($selected_class_id) && !empty($legacy_standard) && $legacy_standard == $class_numeric) {
            $selected_class_id = $class['class_id'];
            break;
        }
    }

    $boards = $this->db->order_by('sort_order', 'asc')->order_by('name', 'asc')->get('board')->result_array();

    $form_class_id = !empty($selected_class_id) ? $selected_class_id : 2;
    $form_action = htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES, 'UTF-8') .
        '?admin/student/' . $form_class_id . '/do_update/' . $row['student_id'];
?>

<style>
/* Edit modal — show data in UPPERCASE for quick scan-readability */
#student-edit-modal-root input[type=text],
#student-edit-modal-root input[type=email],
#student-edit-modal-root input[type=date],
#student-edit-modal-root textarea,
#student-edit-modal-root select,
#student-edit-modal-root .well,
#student-edit-modal-root .form-control-static {
    text-transform: uppercase;
}
/* Don't uppercase the values inside file pickers / numbers */
#student-edit-modal-root input[type=file],
#student-edit-modal-root input[type=number] {
    text-transform: none;
}
</style>

<div id="student-edit-modal-root" class="row">
<div class="col-md-12">
<div class="panel panel-primary">

<div class="panel-heading">
    <div class="panel-title">Edit Student</div>
</div>

<div class="panel-body">

<form action="<?php echo $form_action; ?>" class="form-horizontal form-groups-bordered" enctype="multipart/form-data" method="post">

<!-- ================= BASIC INFO ================= -->
<h4>Basic Info</h4>

<?php
function inputField($label, $name, $value) {
    echo "
    <div class='form-group'>
        <label class='col-sm-3 control-label'>$label</label>
        <div class='col-sm-5'>
            <input type='text' class='form-control' name='$name' value='$value'>
        </div>
    </div>";
}
?>

<?php inputField('First Name', 'first_name', $row['first_name']); ?>
<?php inputField('Middle Name', 'middle_name', $row['middle_name']); ?>
<?php inputField('Last Name', 'last_name', $row['last_name']); ?>

<div class="form-group">
    <label class="col-sm-3 control-label">DOB</label>
    <div class="col-sm-5">
        <input type="date" class="form-control" name="birthday" value="<?php echo $row['birthday']; ?>">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Age</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="age" readonly>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Gender</label>
    <div class="col-sm-5">
        <label><input type="radio" name="sex" value="male" <?php if($row['sex']=='male') echo 'checked'; ?>> Male</label>
        <label><input type="radio" name="sex" value="female" <?php if($row['sex']=='female') echo 'checked'; ?>> Female</label>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Father Name</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($row['father_name'] ?? ''); ?>">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Father Mobile</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="fmobile" value="<?php echo $row['fmobile']; ?>" pattern="^[0-9]{10}$" title="Please enter a 10-digit mobile number" required>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Mother Name</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="mother_name" value="<?php echo htmlspecialchars($row['mother_name'] ?? ''); ?>">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Mother Mobile</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="mmobile" value="<?php echo htmlspecialchars($row['mmobile'] ?? ''); ?>" pattern="^[0-9]{10}$" title="Please enter a 10-digit mobile number">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Emergency Contact</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="emergency_contact" value="<?php echo htmlspecialchars($row['emergency_contact'] ?? ''); ?>" placeholder="Name &amp; phone of someone to contact in emergencies">
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Email</label>
    <div class="col-sm-5">
        <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>" required>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Home Address</label>
    <div class="col-sm-5">
        <textarea class="form-control" name="home" required><?php echo $row['address']; ?></textarea>
    </div>
</div>

<hr>

<!-- ================= ACADEMIC ================= -->
<h4>Academic</h4>

<div class="form-group">
    <label class="col-sm-3 control-label">Standard</label>
    <div class="col-sm-5">
        <select name="class_id" id="edit_class_id" class="form-control" onchange="toggleEditStudentMobile()">
            <option value="">-Select-</option>
            <?php foreach ($classes as $class): $nm = (int)($class['name_numeric'] ?? 0); ?>
                <option value="<?php echo $class['class_id']; ?>" data-class-numeric="<?php echo $nm; ?>" <?php if($selected_class_id == $class['class_id']) echo 'selected'; ?>>
                    <?php echo $class['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Student Mobile (only when class >= 10) -->
<div class="form-group" id="edit_student_mobile_row" style="display:none;">
    <label class="col-sm-3 control-label">Student Mobile</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="student_mobile" value="<?php echo htmlspecialchars($row['student_mobile'] ?? ''); ?>" pattern="^[0-9]{10}$" title="10-digit mobile number">
        <small class="text-muted">Shown for Class 10 and above only.</small>
    </div>
</div>
<script>
function toggleEditStudentMobile() {
    var sel = document.getElementById('edit_class_id');
    var row = document.getElementById('edit_student_mobile_row');
    if (!sel || !row) return;
    var opt = sel.options[sel.selectedIndex];
    var n = opt ? parseInt(opt.getAttribute('data-class-numeric') || '0', 10) : 0;
    row.style.display = (n >= 10) ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleEditStudentMobile);
// Also run immediately for AJAX-loaded modal (DOMContentLoaded already fired in some cases)
toggleEditStudentMobile();
</script>

<div class="form-group">
    <label class="col-sm-3 control-label">Board</label>
    <div class="col-sm-5">
        <select name="board" class="form-control">
            <option value="">-Select-</option>
            <?php foreach ($boards as $board): ?>
                <option value="<?php echo $board['name']; ?>" <?php if($row['board'] == $board['name']) echo 'selected'; ?>>
                    <?php echo $board['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Medium</label>
    <div class="col-sm-5">
        <?php $this->load->model('crud_model'); ?>
        <select name="medium" class="form-control">
            <option value="">-Select-</option>
            <?php foreach ($this->crud_model->get_lookup_values('medium', array('English','Hindi','Marathi','Semi-English')) as $opt): ?>
                <option value="<?php echo htmlspecialchars($opt); ?>" <?php if (($row['medium'] ?? '') === $opt) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($opt); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Alumni</label>
    <div class="col-sm-5">
        <label class="radio-inline">
            <input type="radio" name="is_alumni" value="1" <?php if(!empty($row['is_alumni'])) echo 'checked'; ?>> Yes
        </label>
        <label class="radio-inline">
            <input type="radio" name="is_alumni" value="0" <?php if(empty($row['is_alumni'])) echo 'checked'; ?>> No
        </label>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Re-registered</label>
    <div class="col-sm-5">
        <label class="radio-inline">
            <input type="radio" name="is_reregister" value="1" <?php if(!empty($row['is_reregister'])) echo 'checked'; ?>> Yes
        </label>
        <label class="radio-inline">
            <input type="radio" name="is_reregister" value="0" <?php if(empty($row['is_reregister'])) echo 'checked'; ?>> No
        </label>
        <small class="text-muted" style="display:block; margin-top:4px;">
            Auto-set when a student is created via the Re-register flow. Toggle here for manual correction.
            <?php if (!empty($row['previous_student_id'])): ?>
                <br><strong>Previous record:</strong> STU-<?php echo str_pad((int)$row['previous_student_id'], 5, '0', STR_PAD_LEFT); ?>
            <?php endif; ?>
        </small>
    </div>
</div>

<!-- Academic Year (Batch) -->
<div class="form-group">
    <label class="col-sm-3 control-label">Academic Year</label>
    <div class="col-sm-5">
        <?php
            $ay_val = $row['academic_year'] ?? '';
            if ($ay_val === '') {
                $__y = (int)date('Y'); $__m = (int)date('n');
                $ay_val = $__m >= 4 ? ($__y . '-' . ($__y + 1)) : (($__y - 1) . '-' . $__y);
            }
            $this->load->model('crud_model');
            $__ays = $this->crud_model->academic_years(array($ay_val));
        ?>
        <select class="form-control" name="academic_year">
            <?php foreach ($__ays as $ay): ?>
                <option value="<?php echo htmlspecialchars($ay); ?>" <?php if ($ay === $ay_val) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($ay); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Manage via <strong>Manage Academic Year</strong>.</small>
    </div>
</div>

<hr>

<!-- ================= FEES ================= -->
<h4>Fees Summary</h4>

<div class="alert alert-info">
    Total: ₹ <?php echo number_format($fee['total_fees'],2); ?> |
    Paid: ₹ <?php echo number_format($fee['paid'],2); ?> |
    <b style="color:red;">Remaining: ₹ <?php echo number_format($fee['remaining'],2); ?></b>
    <br>
    <a href="#"
       onclick="event.preventDefault(); jQuery('#mainModal').modal('hide'); showAjaxModal('<?php echo base_url(); ?>index.php?modal/popup/modal_student_payment_add/<?php echo (int)$row['student_id']; ?>'); return false;"
       class="btn btn-success btn-sm" style="margin-top:8px;">
        <i class="entypo-credit-card"></i> Take Payment Now
    </a>
</div>

<?php if (!empty($payments)): ?>
<!-- ================= PAYMENT HISTORY (read-only) ================= -->
<h4>Payment History</h4>
<?php foreach ($payments as $i => $p): ?>
<div class="form-group">
    <label class="col-sm-3 control-label">Payment <?php echo $i+1; ?></label>
    <div class="col-sm-9">
        <div class="well" style="margin-bottom:6px;">
            <strong>₹ <?php echo number_format((float)$p['amount'], 2); ?></strong>
            &nbsp;|&nbsp; <?php echo date('d M Y', (int)$p['timestamp']); ?>
            &nbsp;|&nbsp; <?php echo htmlspecialchars($p['payment_type'] ?? '-'); ?>
            &nbsp;|&nbsp; <?php echo htmlspecialchars($p['method'] ?? '-'); ?>
            <?php if (!empty($p['transaction_id'])): ?>
                <br><small><strong>Txn ID:</strong> <?php echo htmlspecialchars($p['transaction_id']); ?></small>
            <?php endif; ?>
            <?php if (!empty($p['cheque_number'])): ?>
                <br><small>
                    <strong>Cheque #:</strong> <?php echo htmlspecialchars($p['cheque_number']); ?>
                    <?php if (!empty($p['cheque_bank'])): ?> | <strong>Bank:</strong> <?php echo htmlspecialchars($p['cheque_bank']); ?><?php endif; ?>
                    <?php if (!empty($p['cheque_date'])): ?> | <strong>Date:</strong> <?php echo htmlspecialchars($p['cheque_date']); ?><?php endif; ?>
                </small>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<hr>

<!-- ================= DOCUMENTS ================= -->
<h4>Documents</h4>

<?php
function previewFile($file, $upload_path) {
    if (!$file) return;

    $url = $upload_path.$file;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg','jpeg','png'])) {
        echo "<img src='$url' style='max-width:120px;display:block;margin-top:10px'>";
    } elseif ($ext == 'pdf') {
        echo "<a href='$url' target='_blank'>View PDF</a>";
    }
}
?>

<div class="form-group">
    <label class="col-sm-3 control-label">Student Photo</label>
    <div class="col-sm-5">
        <input type="file" name="student_photo">
        <?php previewFile($row['student_photo'], 'uploads/student_files/'); ?>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Aadhar Card</label>
    <div class="col-sm-5">
        <input type="file" name="government_identity">
        <?php previewFile($row['aadhar_card'], 'uploads/student_documents/'); ?>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Marksheet</label>
    <div class="col-sm-5">
        <input type="file" name="mark_sheet">
        <?php previewFile($row['marksheet'], 'uploads/student_documents/'); ?>
    </div>
</div>

<hr>

<!-- ================= SUBMIT ================= -->
<div class="form-group">
    <div class="col-sm-offset-3 col-sm-5">
        <button type="submit" class="btn btn-success">Update Student</button>
    </div>
</div>

<?php echo form_close(); ?>

</div>
</div>
</div>
</div>

<?php endforeach; ?>

<!-- ================= JS ================= -->
<script>
$('.modal-dialog').addClass('modal-lg').css('width', '90%');

/* AGE */
function calculateAge(dob) {
    let d = new Date(dob);
    let diff = new Date() - d;
    return Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
}

$(function(){
    let dob = $('input[name="birthday"]');
    let age = $('input[name="age"]');

    function updateAge() {
        age.val(calculateAge(dob.val()));
    }
    updateAge();
    dob.on('change', updateAge);
});

/* Payments are now handled via the separate "Take Payment" action on the student row. */
</script>
