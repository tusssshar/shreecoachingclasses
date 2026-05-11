<hr>

<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo get_phrase('manage_alumni'); ?></div>
    </div>

    <div class="panel-body">
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-sm-12 text-right">
                <a href="<?php echo base_url();?>index.php?admin/export_list/alumni/excel" target="_blank" class="btn btn-info btn-sm">Excel</a>
                <a href="<?php echo base_url();?>index.php?admin/export_list/alumni/pdf" target="_blank" class="btn btn-danger btn-sm">PDF</a>
                <a href="<?php echo base_url();?>index.php?admin/export_list/alumni/print" target="_blank" class="btn btn-default btn-sm">Print</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped datatable" id="alumni_table" style="width:100%;">
                <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Photo</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Sex</th>
                    <th>Father Mobile</th>
                    <th>Standard</th>
                    <th>Medium</th>
                    <th>Board</th>
                    <th>Age</th>
                    <th>Remaining Fees</th>
                    <th>Email</th>
                    <th>View</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($alumni as $row): ?>
                    <?php
                    $age = '-';
                    if (!empty($row['birthday'])) {
                        $dob = date_create($row['birthday']);
                        if ($dob) {
                            $age = date_diff(new DateTime(), $dob)->y;
                        }
                    }

                    $remaining = '-';
                    if (isset($row['total_fees']) || isset($row['payment_done'])) {
                        $remaining = number_format(floatval($row['total_fees']) - floatval($row['payment_done']), 2);
                    }
                    ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td>
                            <img src="<?php echo $this->crud_model->get_image_url('student', $row['student_id']); ?>"
                                 width="30" class="img-circle">
                        </td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['middle_name']; ?></td>
                        <td><?php echo $row['last_name']; ?></td>
                        <td><?php echo $row['sex']; ?></td>
                        <td><?php echo $row['fmobile']; ?></td>
                        <td><?php echo $row['standard']; ?></td>
                        <td><?php echo $row['medium']; ?></td>
                        <td><?php echo $row['board']; ?></td>
                        <td><?php echo $age; ?></td>
                        <td><?php echo $remaining; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <a href="#" class="btn btn-default btn-sm"
                               onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_student_profile/<?php echo $row['student_id'];?>');">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $("#alumni_table").DataTable({
        responsive: true,
        autoWidth: false,
        scrollX: true
    });
});
</script>
