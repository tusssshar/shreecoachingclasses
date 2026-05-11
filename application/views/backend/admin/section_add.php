<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="entypo-plus-circled"></i>
                    Add Teacher Time Table
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open(base_url() . 'index.php?admin/sections/create/', array('class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data'));?>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="name" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nick Name</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="nick_name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Class</label>
                        <div class="col-sm-5">
                            <select name="class_id" class="form-control selectboxit" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
                                <option value=""><?php echo get_phrase('select');?></option>
                                <?php
                                    $classes = $this->db->get('class')->result_array();
                                    foreach ($classes as $row):
                                ?>
                                    <option value="<?php echo $row['class_id'];?>"><?php echo $row['name'];?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Teacher</label>
                        <div class="col-sm-5">
                            <select name="teacher_id" class="form-control selectboxit" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
                                <option value=""><?php echo get_phrase('select');?></option>
                                <?php
                                    $teachers = $this->db->get('teacher')->result_array();
                                    foreach ($teachers as $row):
                                ?>
                                    <option value="<?php echo $row['teacher_id'];?>"><?php echo $row['name'];?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Days</label>
                        <div class="col-sm-8">
                            <?php foreach (array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') as $day): ?>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="days[]" value="<?php echo $day; ?>"> <?php echo $day; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Start Time</label>
                        <div class="col-sm-5">
                            <input type="time" class="form-control" name="start_time" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">End Time</label>
                        <div class="col-sm-5">
                            <input type="time" class="form-control" name="end_time" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-5">
                            <button type="submit" class="btn btn-info">Add Time Table</button>
                        </div>
                    </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>
