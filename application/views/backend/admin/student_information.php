<hr>

<div class="panel panel-gradient">

    <div class="panel-heading">
        <div class="panel-title">
            <?php echo get_phrase('student_information_page'); ?>
        </div>
    </div>

    <div>
        <br>

        <!-- ACTION BUTTONS -->
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-sm-6">
                <a href="<?php echo base_url();?>index.php?admin/student_add" class="btn btn-primary">
                    <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('add_new_student');?>
                </a>
            </div>

            <div class="col-sm-6 text-right">
                <?php $export_params = http_build_query($this->input->get()); ?>
                <a href="<?php echo base_url();?>index.php?admin/export_list/student_information/excel?<?php echo $export_params; ?>" target="_blank" class="btn btn-info btn-sm">Excel</a>
                <a href="<?php echo base_url();?>index.php?admin/export_list/student_information/pdf?<?php echo $export_params; ?>" target="_blank" class="btn btn-danger btn-sm">PDF</a>
                <a href="<?php echo base_url();?>index.php?admin/export_list/student_information/print?<?php echo $export_params; ?>" target="_blank" class="btn btn-default btn-sm">Print</a>
            </div>
        </div>

        <!-- TABS -->
        <ul class="nav nav-tabs bordered">
            <li class="active">
                <a href="#home" data-toggle="tab">
                    <?php echo get_phrase('all_students');?>
                </a>
            </li>
        </ul>

        <!-- TAB CONTENT -->
        <div class="tab-content">

            <!-- ALL STUDENTS -->
            <div class="tab-pane active" id="home">

                <!-- SEARCH FORM -->
                <div class="row" style="margin-bottom: 15px;">
                    <form id="searchForm">
                        <div class="col-sm-3">
                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" value="">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" value="">
                        </div>
                        <div class="col-sm-3">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="">
                        </div>
                        <div class="col-sm-3">
                            <button type="button" id="searchBtn" class="btn btn-primary">Search</button>
                            <button type="button" id="clearBtn" class="btn btn-default">Clear</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable" id="table_export" style="width:100%;">

                        <thead>
                        <tr>
                            <th>Roll</th>
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
                            <th>Options</th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php 
                        foreach ($students as $row):

                            // AGE CALCULATION
                            $age = '-';
                            if (!empty($row['birthday'])) {
                                $dob = date_create($row['birthday']);
                                if ($dob) {
                                    $age = date_diff(new DateTime(), $dob)->y;
                                }
                            }

                            // FEES CALCULATION
                            $remaining = '-';
                            if (isset($row['total_fees']) || isset($row['payment_done'])) {
                                $remaining = floatval($row['total_fees']) - floatval($row['payment_done']);
                                $remaining = number_format($remaining, 2);
                            }
                        ?>

                        <tr>
                            <td><?php echo $row['student_id'];?></td>

                            <td>
                                <img src="<?php echo $this->crud_model->get_image_url('student',$row['student_id']);?>" 
                                     width="30" class="img-circle">
                            </td>

                            <td><?php echo $row['first_name'];?></td>
                            <td><?php echo $row['middle_name'];?></td>
                            <td><?php echo $row['last_name'];?></td>
                            <td><?php echo $row['sex'];?></td>
                            <td><?php echo $row['fmobile'];?></td>
                            <td><?php echo $row['standard'];?></td>
                            <td><?php echo $row['medium'];?></td>
                            <td><?php echo $row['board'];?></td>
                            <td><?php echo $age;?></td>
                            <td><?php echo $remaining;?></td>
                            <td><?php echo $row['email'];?></td>

                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-blue btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>

                                    <ul class="dropdown-menu dropdown-primary pull-right">

                                        <li>
                                            <a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_student_profile/<?php echo $row['student_id'];?>');">
                                                Profile
                                            </a>
                                        </li>

                                        <li>
                                            <a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_student_edit/<?php echo $row['student_id'];?>');">
                                                Edit
                                            </a>
                                        </li>

                                        <li>
                                            <a href="<?php echo base_url();?>index.php?admin/student_export_invoice/<?php echo $row['student_id'];?>" target="_blank">
                                                <i class="entypo-doc-text"></i> Export Invoice
                                            </a>
                                        </li>

                                        <li class="divider"></li>

                                        <?php if (!empty($row['student_photo'])): ?>
                                            <li>
                                                <a href="<?php echo base_url('uploads/student_files/' . $row['student_photo']); ?>" target="_blank" download>
                                                    Download Student Photo
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($row['aadhar_card'])): ?>
                                            <li>
                                                <a href="<?php echo base_url('uploads/student_files/' . $row['aadhar_card']); ?>" target="_blank" download>
                                                    Download Aadhar Card
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($row['birth_certificate'])): ?>
                                            <li>
                                                <a href="<?php echo base_url('uploads/student_files/' . $row['birth_certificate']); ?>" target="_blank" download>
                                                    Download Birth Certificate
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($row['marksheet'])): ?>
                                            <li>
                                                <a href="<?php echo base_url('uploads/student_files/' . $row['marksheet']); ?>" target="_blank" download>
                                                    Download Marksheet
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (!empty($row['student_photo']) || !empty($row['aadhar_card']) || !empty($row['birth_certificate']) || !empty($row['marksheet'])): ?>
                                            <li class="divider"></li>
                                        <?php endif; ?>

                                        <li>
                                            <a href="<?php echo base_url();?>index.php?admin/student/<?php echo $class_id;?>/delete/<?php echo $row['student_id'];?>"
                                               onclick="return confirm('Are you sure?')">
                                                Delete
                                            </a>
                                        </li>

                                    </ul>
                                </div>
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

<script>
$(document).ready(function () {
    $("#table_export").DataTable({
        responsive: true,
        autoWidth: false,
        scrollX: true
    });

    // AJAX Search
    $('#searchBtn').click(function() {
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var email = $('#email').val();

        $.ajax({
            url: '<?php echo base_url(); ?>index.php?admin/search_students',
            type: 'POST',
            data: {
                first_name: first_name,
                last_name: last_name,
                email: email
            },
            success: function(response) {
                $('#table_export tbody').html(response);
            },
            error: function() {
                alert('Search failed. Please try again.');
            }
        });
    });

    // Clear Search
    $('#clearBtn').click(function() {
        $('#first_name').val('');
        $('#last_name').val('');
        $('#email').val('');
        // Reload the page to show all students
        location.reload();
    });
});
</script>
