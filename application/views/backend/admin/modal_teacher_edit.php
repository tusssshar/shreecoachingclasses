<?php 
$edit_data		=	$this->db->get_where('teacher' , array('teacher_id' => $param2) )->result_array();
foreach ( $edit_data as $row):
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary" data-collapsed="0">
        	<div class="panel-heading">
            	<div class="panel-title" >
            		<i class="entypo-plus-circled"></i>
					<?php echo get_phrase('edit_teacher');?>
            	</div>
            </div>
			<div class="panel-body">
                    <?php echo form_open(base_url() . 'index.php?admin/teacher/do_update/'.$row['teacher_id'] , array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top', 'enctype' => 'multipart/form-data'));?>
                        		
                                <div class="form-group">
                                <label for="field-1" class="col-sm-3 control-label"><?php echo get_phrase('photo');?></label>
                                
                                <div class="col-sm-5">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 100px; height: 100px;" data-trigger="fileinput">
                                            <img src="<?php echo $this->crud_model->get_image_url('teacher' , $row['teacher_id']);?>" alt="...">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px"></div>
                                        <div>
                                            <span class="btn btn-white btn-file">
                                                <span class="fileinput-new">Select image</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="userfile" accept="image/*">
                                            </span>
                                            <a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('name');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="name" value="<?php echo $row['name'];?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('birthday');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="datepicker form-control" name="birthday" value="<?php echo $row['birthday'];?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('sex');?></label>
                                <div class="col-sm-5">
                                    <select name="sex" class="form-control selectboxit">
                                    	<option value="male" <?php if($row['sex'] == 'male')echo 'selected';?>><?php echo get_phrase('male');?></option>
                                    	<option value="female" <?php if($row['sex'] == 'female')echo 'selected';?>><?php echo get_phrase('female');?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Blood Group</label>
                                <div class="col-sm-5">
                                    <select name="blood_group" class="form-control">
                                        <option value="">-- select --</option>
                                        <?php foreach (array('A+','A-','B+','B-','AB+','AB-','O+','O-') as $bg): ?>
                                            <option value="<?php echo $bg; ?>" <?php if(($row['blood_group'] ?? '') === $bg) echo 'selected'; ?>><?php echo $bg; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('address');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="address" value="<?php echo $row['address'];?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('phone');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="phone" value="<?php echo $row['phone'];?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><?php echo get_phrase('email');?></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="email" value="<?php echo $row['email'];?>"/>
                                </div>
                            </div>

                            <hr>
                            <h4 style="margin-left:15px;">Employment &amp; Salary Details</h4>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Designation</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="designation" value="<?php echo htmlspecialchars($row['designation'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Joining Date</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control datepicker" name="joining_date" value="<?php echo htmlspecialchars($row['joining_date'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">PAN Number</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="pan_number" value="<?php echo htmlspecialchars($row['pan_number'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Bank Account</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="bank_account" value="<?php echo htmlspecialchars($row['bank_account'] ?? ''); ?>">
                                </div>
                            </div>

                            <?php
                                $this->load->model('crud_model');
                                $__struct = $this->crud_model->salary_structure();
                                $salary_pct   = $__struct['percentages'];
                                $salary_fixed = $__struct['fixed'];
                                $basic_init   = (float)($row['basic_salary'] ?? 0);
                            ?>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Basic Salary <span class="text-muted">(monthly)</span></label>
                                <div class="col-sm-5">
                                    <input type="number" step="1" min="0" class="form-control" id="basic_salary" name="basic_salary" value="<?php echo $basic_init; ?>" autocomplete="off">
                                    <small class="text-muted">Everything else is auto-calculated from this.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">HRA <span class="label label-info"><?php echo $salary_pct['hra']; ?>% of Basic</span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="hra" name="hra" value="<?php echo (float)($row['hra'] ?? 0); ?>" readonly></div>
                                <label class="col-sm-2 control-label">DA <span class="label label-info"><?php echo $salary_pct['da']; ?>% of Basic</span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="da" name="da" value="<?php echo (float)($row['da'] ?? 0); ?>" readonly></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Conveyance <span class="label label-info"><?php echo $salary_pct['conveyance']; ?>% of Basic</span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="conveyance" name="conveyance" value="<?php echo (float)($row['conveyance'] ?? 0); ?>" readonly></div>
                                <label class="col-sm-2 control-label">Medical <span class="label label-info"><?php echo $salary_pct['medical_allowance']; ?>% of Basic</span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="medical_allowance" name="medical_allowance" value="<?php echo (float)($row['medical_allowance'] ?? 0); ?>" readonly></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Other Allowance <span class="label label-default"><?php echo $salary_pct['other_allowance']; ?>% of Basic</span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="other_allowance" name="other_allowance" value="<?php echo (float)($row['other_allowance'] ?? 0); ?>" readonly></div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">PF Deduction <span class="label label-warning">fixed &#8377;<?php echo $salary_fixed['pf_deduction']; ?></span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="pf_deduction" name="pf_deduction" value="<?php echo $salary_fixed['pf_deduction']; ?>" readonly></div>
                                <label class="col-sm-2 control-label">Tax <span class="label label-warning">fixed &#8377;<?php echo $salary_fixed['tax_deduction']; ?></span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="tax_deduction" name="tax_deduction" value="<?php echo $salary_fixed['tax_deduction']; ?>" readonly></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Other Deduction <span class="label label-default"><?php echo $salary_pct['other_deduction']; ?>%</span></label>
                                <div class="col-sm-3"><input type="number" class="form-control" id="other_deduction" name="other_deduction" value="<?php echo (float)($row['other_deduction'] ?? 0); ?>" readonly></div>
                            </div>

                            <hr>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><strong>CTC (per month)</strong></label>
                                <div class="col-sm-5">
                                    <p class="form-control-static" id="ctc_display" style="font-size:16px;font-weight:bold;color:#1f3a68;">&#8377; 0.00</p>
                                    <small class="text-muted">Basic + HRA + DA + Conveyance + Medical + Other Allowance</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><strong>Net Take-Home</strong></label>
                                <div class="col-sm-5">
                                    <p class="form-control-static" id="net_display" style="font-size:18px;font-weight:bold;color:#1a8d3a;">&#8377; 0.00</p>
                                    <small class="text-muted">CTC &minus; PF &minus; Tax &minus; Other Deduction</small>
                                </div>
                            </div>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-5">
                                <button type="submit" class="btn btn-info"><?php echo get_phrase('edit_teacher');?></button>
                            </div>
                        </div>
                <?php echo form_close();?>

                <script>
                (function () {
                    var PCT   = <?php echo json_encode($salary_pct); ?>;
                    var FIXED = <?php echo json_encode($salary_fixed); ?>;
                    var $basic = document.getElementById('basic_salary');
                    if (!$basic) return;

                    function fmt(n) {
                        return '₹ ' + (Math.round(n * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                    function round2(n) { return Math.round(n * 100) / 100; }

                    function recalc() {
                        var basic = parseFloat($basic.value) || 0;
                        if (basic < 0) basic = 0;

                        var hra        = round2(basic * PCT.hra               / 100);
                        var da         = round2(basic * PCT.da                / 100);
                        var conveyance = round2(basic * PCT.conveyance        / 100);
                        var medical    = round2(basic * PCT.medical_allowance / 100);
                        var otherA     = round2(basic * PCT.other_allowance   / 100);
                        var otherD     = round2(basic * PCT.other_deduction   / 100);
                        var pf         = FIXED.pf_deduction;
                        var tax        = FIXED.tax_deduction;

                        document.getElementById('hra').value               = hra;
                        document.getElementById('da').value                = da;
                        document.getElementById('conveyance').value        = conveyance;
                        document.getElementById('medical_allowance').value = medical;
                        document.getElementById('other_allowance').value   = otherA;
                        document.getElementById('other_deduction').value   = otherD;
                        document.getElementById('pf_deduction').value      = pf;
                        document.getElementById('tax_deduction').value     = tax;

                        var ctc = basic + hra + da + conveyance + medical + otherA;
                        var net = Math.max(0, ctc - (pf + tax + otherD));
                        document.getElementById('ctc_display').textContent = fmt(ctc);
                        document.getElementById('net_display').textContent = fmt(net);
                    }
                    $basic.addEventListener('input', recalc);
                    $basic.addEventListener('change', recalc);
                    recalc();
                })();
                </script>
            </div>
        </div>
    </div>
</div>

<?php
endforeach;
?>