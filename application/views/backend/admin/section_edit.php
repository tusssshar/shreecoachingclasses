<?php
$edit_data = $this->db->get_where('section', array('section_id' => $param2))->result_array();
foreach ($edit_data as $row):
    $selected_days = !empty($row['days']) ? explode(',', $row['days']) : array();
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="entypo-pencil"></i>
                    Edit Teacher Time Table
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open(base_url() . 'index.php?admin/sections/edit/' . $row['section_id'], array('class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data'));?>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="name" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" value="<?php echo $row['name'];?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nick Name</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="nick_name" value="<?php echo $row['nick_name'];?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Class</label>
                        <div class="col-sm-5">
                            <select name="class_id" class="form-control selectboxit" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
                                <option value=""><?php echo get_phrase('select');?></option>
                                <?php
                                    $classes = $this->db->get('class')->result_array();
                                    foreach ($classes as $row2):
                                ?>
                                    <option value="<?php echo $row2['class_id'];?>" <?php if ($row['class_id'] == $row2['class_id']) echo 'selected';?>>
                                        <?php echo $row2['name'];?>
                                    </option>
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
                                    foreach ($teachers as $row3):
                                ?>
                                    <option value="<?php echo $row3['teacher_id'];?>" <?php if ($row['teacher_id'] == $row3['teacher_id']) echo 'selected';?>>
                                        <?php echo $row3['name'];?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Days</label>
                        <div class="col-sm-8">
                            <?php foreach (array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') as $day): ?>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="days[]" value="<?php echo $day; ?>" <?php if (in_array($day, $selected_days)) echo 'checked'; ?>> <?php echo $day; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Start Time</label>
                        <div class="col-sm-5">
                            <input type="time" class="form-control" name="start_time" value="<?php echo $row['start_time']; ?>" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">End Time</label>
                        <div class="col-sm-5">
                            <input type="time" class="form-control" name="end_time" value="<?php echo $row['end_time']; ?>" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-5">
                            <button type="submit" class="btn btn-info"><?php echo get_phrase('update');?></button>
                        </div>
                    </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>
<?php endforeach;?>
