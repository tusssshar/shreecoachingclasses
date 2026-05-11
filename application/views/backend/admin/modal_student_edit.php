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

<div class="row">
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
        <select name="class_id" class="form-control">
            <option value="">-Select-</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class['class_id']; ?>" <?php if($selected_class_id == $class['class_id']) echo 'selected'; ?>>
                    <?php echo $class['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

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
        <select name="medium" class="form-control">
            <option value="Marathi" <?php if($row['medium']=='Marathi') echo 'selected'; ?>>Marathi</option>
            <option value="Hindi" <?php if($row['medium']=='Hindi') echo 'selected'; ?>>Hindi</option>
            <option value="English" <?php if($row['medium']=='English') echo 'selected'; ?>>English</option>
            <option value="Semi-English" <?php if($row['medium']=='Semi-English') echo 'selected'; ?>>Semi-English</option>
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

<hr>

<!-- ================= FEES ================= -->
<h4>Fees Summary</h4>

<div class="alert alert-info">
    Total: ₹ <?php echo number_format($fee['total_fees'],2); ?> |
    Paid: ₹ <?php echo number_format($fee['paid'],2); ?> |
    <b style="color:red;">Remaining: ₹ <?php echo number_format($fee['remaining'],2); ?></b>
</div>

<!-- ================= PAYMENT HISTORY ================= -->
<h4>Payment History</h4>

<?php foreach ($payments as $i => $p): ?>
<div class="form-group">
    <label class="col-sm-3 control-label">Payment <?php echo $i+1; ?></label>
    <div class="col-sm-9">
        <div class="well">
            ₹ <?php echo $p['amount']; ?> |
            <?php echo date('d M Y',$p['timestamp']); ?> |
            <?php echo $p['payment_type']; ?> |
            <?php echo $p['method']; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div id="payment-container"></div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
        <button type="button" class="btn btn-info" onclick="addMorePayment()">+ Add Payment</button>
    </div>
</div>

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

/* ADD PAYMENT */
function addMorePayment() {
    let count = $('.payment-extra').length + 1;

    $('#payment-container').append(`
    <div class="form-group payment-extra">
        <label class="col-sm-3 control-label">Payment ${count}</label>
        <div class="col-sm-9">
            <input type="number" name="payment${count}_amount" placeholder="Amount" class="form-control"><br>
            <input type="date" name="payment${count}_date" class="form-control"><br>
            <select name="payment${count}_type" class="form-control">
                <option value="Admission">Admission</option>
                <option value="Installment">Installment</option>
            </select><br>
            <select name="payment${count}_mode" class="form-control">
                <option value="Cash">Cash</option>
                <option value="Online">Online</option>
            </select>
        </div>
    </div>
    `);
}
</script>
