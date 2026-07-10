<hr>
<div class="panel panel-gradient">
  <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('manage_courses'); ?></div></div>
  <div class="panel-body">
    <a href="<?php echo base_url();?>index.php?admin/course_add" class="btn btn-primary btn-icon icon-left">
      <i class="entypo-plus"></i> <?php echo get_phrase('new_course'); ?>
    </a>
  </div>

<div class="table-responsive">
<table class="table table-bordered table-striped datatable" id="table-2">
  <thead>
    <tr>
      <th>#</th>
      <th><?php echo get_phrase('course_name'); ?></th>
      <th><?php echo get_phrase('standard'); ?></th>
      <th><?php echo get_phrase('year'); ?></th>
      <th><?php echo get_phrase('total_fees'); ?></th>
      <th><?php echo get_phrase('installments'); ?></th>
      <th><?php echo get_phrase('options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($courses as $row): ?>
      <tr>
        <td><?php echo $row['course_id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['standard_name']); ?></td>
        <td><?php echo htmlspecialchars($row['session_name']); ?></td>
        <td><?php echo number_format((float)$row['total_fees'], 2); ?></td>
        <td><?php echo (int)$row['installments']; ?></td>
        <td>
          <a href="<?php echo base_url();?>index.php?admin/course_view/<?php echo $row['course_id'];?>" class="btn btn-success btn-sm btn-icon icon-left">
            <i class="entypo-eye"></i> <?php echo get_phrase('view_fees'); ?>
          </a>
          <a href="<?php echo base_url();?>index.php?admin/course_add/edit/<?php echo $row['course_id'];?>" class="btn btn-info btn-sm btn-icon icon-left">
            <i class="entypo-pencil"></i> Edit
          </a>
          <a href="<?php echo base_url();?>index.php?admin/course/delete/<?php echo $row['course_id'];?>" class="btn btn-danger btn-sm btn-icon icon-left" onclick="return confirm('Are you sure to delete?');">
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
        jQuery("#table-2").dataTable({ "sPaginationType": "bootstrap" });
    });
</script>
