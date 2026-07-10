<?php
    // Staff lookup (assign to / handled by) — id => name.
    $staff_lookup = array();
    foreach ($this->db->get('teacher')->result_array() as $t) {
        $staff_lookup[$t['teacher_id']] = $t['name'];
    }
    // Status label rendering lives in sms_admissions_helper (unit tested).
?>
<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title"><?php echo get_phrase('manage_enquiries'); ?></div>
    </div>
    <div class="panel-body">
        <a href="<?php echo base_url();?>index.php?admin/enquiry_add" class="btn btn-primary btn-icon icon-left">
            <i class="entypo-plus"></i> <?php echo get_phrase('add_enquiry'); ?>
        </a>
        <a href="<?php echo base_url();?>index.php?admin/enquiry_bulk_add" class="btn btn-info btn-icon icon-left">
            <i class="entypo-upload"></i> <?php echo get_phrase('bulk_enquiry_import'); ?>
        </a>
    </div>

<div class="table-responsive">
<table class="table table-bordered table-striped datatable" id="table-2">
    <thead>
        <tr>
            <th><?php echo get_phrase('enquiry_no');?></th>
            <th><?php echo get_phrase('year');?></th>
            <th><?php echo get_phrase('name');?></th>
            <th><?php echo get_phrase('contact');?></th>
            <th><?php echo get_phrase('course');?></th>
            <th><?php echo get_phrase('source');?></th>
            <th><?php echo get_phrase('assign_to');?></th>
            <th><?php echo get_phrase('handled_by');?></th>
            <th><?php echo get_phrase('status');?></th>
            <th><?php echo get_phrase('options');?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($enquiries as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['enquiry_no']); ?></td>
                <td><?php echo htmlspecialchars($row['session_name']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                <td><?php echo htmlspecialchars($row['course']); ?></td>
                <td><?php echo htmlspecialchars($row['source']); ?></td>
                <td><?php echo isset($staff_lookup[$row['assign_to']]) ? htmlspecialchars($staff_lookup[$row['assign_to']]) : '-'; ?></td>
                <td><?php echo isset($staff_lookup[$row['handled_by']]) ? htmlspecialchars($staff_lookup[$row['handled_by']]) : '-'; ?></td>
                <td><?php echo sms_enquiry_status_label($row['status']); ?></td>
                <td>
                    <a href="<?php echo base_url();?>index.php?admin/enquiry_follow/<?php echo $row['enquiry_id'];?>"
                        class="btn btn-success btn-sm btn-icon icon-left" title="Follow up">
                        <i class="entypo-flow-tree"></i> Follow up
                    </a>
                    <a href="<?php echo base_url();?>index.php?admin/enquiry_add/edit/<?php echo $row['enquiry_id'];?>"
                        class="btn btn-info btn-sm btn-icon icon-left">
                        <i class="entypo-pencil"></i> Edit
                    </a>
                    <a href="<?php echo base_url();?>index.php?admin/enquiry/delete/<?php echo $row['enquiry_id'];?>"
                        class="btn btn-danger btn-sm btn-icon icon-left" onclick="return confirm('Are you sure to delete?');">
                        <i class="entypo-cancel"></i> Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<script type="text/javascript">
    jQuery(window).load(function () {
        var $ = jQuery;
        $("#table-2").dataTable({
            "sPaginationType": "bootstrap",
            "sDom": "<'row'<'col-xs-3 col-left'l><'col-xs-9 col-right'<'export-data'T>f>r>t<'row'<'col-xs-3 col-left'i><'col-xs-9 col-right'p>>"
        });
        $(".dataTables_wrapper select").select2({ minimumResultsForSearch: -1 });
    });
</script>
