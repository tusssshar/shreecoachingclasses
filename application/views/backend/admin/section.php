<hr />

<?php if ($this->session->flashdata('error_message')): ?>
    <div class="alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('flash_message')): ?>
    <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
<?php endif; ?>

<div style="margin-bottom:12px;">
    <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/section_add/');"
        class="btn btn-primary pull-right">
            <i class="entypo-plus-circled"></i>
            Add Time Table
    </a>
    <a href="#" onclick="if(confirm('Send today\'s lecture reminder email to every teacher whose timetable includes today?')) window.location='<?php echo base_url();?>index.php?admin/send_timetable_reminders';"
        class="btn btn-warning pull-right" style="margin-right:8px;">
            <i class="entypo-mail"></i>
            Send Today's Reminders
    </a>
    <div class="clearfix"></div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered datatable" id="table_export">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Nick Name</th>
                    <th>Class</th>
                    <th>Teacher</th>
                    <th>Days</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th><?php echo get_phrase('options');?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $count = 1;
                    $sections = $this->db->order_by('class_id', 'asc')->order_by('teacher_id', 'asc')->get('section')->result_array();
                    foreach ($sections as $row):
                        $class = $this->db->get_where('class', array('class_id' => $row['class_id']))->row();
                        $teacher = $this->db->get_where('teacher', array('teacher_id' => $row['teacher_id']))->row();
                ?>
                    <tr>
                        <td><?php echo $count++;?></td>
                        <td><?php echo $row['name'];?></td>
                        <td><?php echo $row['nick_name'];?></td>
                        <td><?php echo !empty($class) ? $class->name : '';?></td>
                        <td><?php echo !empty($teacher) ? $teacher->name : '';?></td>
                        <td><?php echo !empty($row['days']) ? str_replace(',', ', ', $row['days']) : '';?></td>
                        <td><?php echo !empty($row['start_time']) ? date('h:i A', strtotime($row['start_time'])) : '';?></td>
                        <td><?php echo !empty($row['end_time']) ? date('h:i A', strtotime($row['end_time'])) : '';?></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-default pull-right" role="menu">
                                    <li>
                                        <a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/section_edit/<?php echo $row['section_id'];?>');">
                                            <i class="entypo-pencil"></i>
                                            <?php echo get_phrase('edit');?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#" onclick="confirm_modal('<?php echo base_url();?>index.php?admin/sections/delete/<?php echo $row['section_id'];?>');">
                                            <i class="entypo-trash"></i>
                                            <?php echo get_phrase('delete');?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#table_export").dataTable();
    });
</script>
