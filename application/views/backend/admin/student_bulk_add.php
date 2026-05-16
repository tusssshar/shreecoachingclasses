<div class="row">
    <div class="col-md-12">
        <div class="panel panel-gradient" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="entypo-plus-circled"></i>
                    <?php echo get_phrase('student_bulk_add_form'); ?>
                </div>
            </div>
            <div class="panel-body">

                <?php if ($this->session->flashdata('flash_message')): ?>
                    <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                <?php endif; ?>

                <?php echo form_open(base_url() . 'index.php?admin/student_bulk_add/import_excel/',
                    array('class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data')); ?>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Excel / CSV File</label>
                        <div class="col-sm-7">
                            <input type="file" name="userfile" class="form-control" accept=".csv,.xlsx"
                                   data-validate="required" data-message-required="<?php echo get_phrase('value_required'); ?>">
                            <p class="help-block" style="margin-top:10px;">
                                Accepted formats: <strong>.csv</strong> or <strong>.xlsx</strong>.
                                If a row includes an existing <strong>student_id</strong>, that student is updated instead of being inserted.
                            </p>
                            <a href="<?php echo base_url(); ?>index.php?admin/student_bulk_add/template"
                               class="btn btn-info btn-sm" style="margin-top:6px;">
                                <i class="entypo-download"></i> Download template (CSV)
                            </a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Standard (Class)</label>
                        <div class="col-sm-5">
                            <select name="class_id" class="form-control"
                                    data-validate="required" data-message-required="<?php echo get_phrase('value_required'); ?>">
                                <option value=""><?php echo get_phrase('select'); ?></option>
                                <?php
                                $classes = $this->db->get('class')->result_array();
                                usort($classes, function ($a, $b) {
                                    return strnatcasecmp($a['name_numeric'], $b['name_numeric']);
                                });
                                foreach ($classes as $row): ?>
                                    <option value="<?php echo $row['class_id']; ?>"><?php echo $row['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Applies to every row in the file.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-7">
                            <button type="submit" class="btn btn-success">
                                <i class="entypo-upload"></i> <?php echo get_phrase('upload_and_import'); ?>
                            </button>
                        </div>
                    </div>

                <?php echo form_close(); ?>

                <hr>
                <h4 style="margin-left:5px;">Expected Columns</h4>
                <p class="text-muted" style="margin-left:5px;">
                    These match the <em>Add Student</em> page (file-upload fields excluded).
                    Header names are case-insensitive; underscores and spaces are interchangeable.
                </p>
                <table class="table table-bordered table-condensed" style="font-size:13px; max-width:900px;">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Column header</th>
                            <th>Required?</th>
                            <th>Allowed values / format</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $this->load->model('crud_model');
                        $mediums = implode(' | ', $this->crud_model->get_lookup_values('medium', array('English','Hindi')));
                        $ptypes  = implode(' | ', $this->crud_model->get_lookup_values('payment_type', array('Admission','Installment')));
                        $pmodes  = implode(' | ', $this->crud_model->get_lookup_values('payment_mode', array('Cash','Online','Cheque')));
                    $rules = array(
                        'first_name'        => array('Yes', 'Text'),
                        'middle_name'       => array('No',  'Text'),
                        'last_name'         => array('No',  'Text'),
                        'birthday'          => array('Yes', 'YYYY-MM-DD'),
                        'father_name'       => array('No',  'Text'),
                        'fmobile'           => array('Yes', '10 digits, no spaces'),
                        'mother_name'       => array('No',  'Text'),
                        'mmobile'           => array('No',  '10 digits, no spaces'),
                        'emergency_contact' => array('No',  'Free text'),
                        'email'             => array('Yes', 'Valid email'),
                        'home_address'      => array('No',  'Text'),
                        'medium'            => array('No',  $mediums . ' (Master Data → Medium)'),
                        'board'             => array('No',  'CBSE | ICSE | State Board | …'),
                        'sex'               => array('No',  'male | female'),
                        'school_name'       => array('No',  'Text'),
                        'total_fees'        => array('No',  'Number'),
                        'payment_amount'    => array('No',  'Number — creates a payment history row if > 0'),
                        'payment_date'      => array('No',  'YYYY-MM-DD'),
                        'payment_type'      => array('No',  $ptypes . ' (Master Data → Type of Payment)'),
                        'payment_mode'      => array('No',  $pmodes . ' (Master Data → Mode of Payment)'),
                    );
                    $i = 1;
                    foreach ($template_columns as $col):
                        $meta = isset($rules[$col]) ? $rules[$col] : array('No', '');
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><code><?php echo htmlspecialchars($col); ?></code></td>
                            <td><?php echo $meta[0]; ?></td>
                            <td><?php echo $meta[1]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
