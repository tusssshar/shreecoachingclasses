<?php
    $e = $enquiry;
    $staff_lookup = array();
    foreach ($staff as $t) { $staff_lookup[$t['teacher_id']] = $t['name']; }
    $status = $e['status'] ?: 'in_progress';
?>
<div class="row">
  <!-- LEFT: enquiry summary + status form -->
  <div class="col-md-5">
    <div class="panel panel-primary">
      <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('enquiry'); ?> #<?php echo htmlspecialchars($e['enquiry_no'] ?: $e['enquiry_id']); ?></div></div>
      <div class="panel-body">
        <table class="table table-bordered">
          <tr><th><?php echo get_phrase('name'); ?></th><td><?php echo htmlspecialchars($e['name']); ?></td></tr>
          <tr><th><?php echo get_phrase('contact'); ?></th><td><?php echo htmlspecialchars($e['mobile']); ?></td></tr>
          <tr><th><?php echo get_phrase('course'); ?></th><td><?php echo htmlspecialchars($e['course']); ?></td></tr>
          <tr><th><?php echo get_phrase('enquiry_for'); ?></th><td><?php echo htmlspecialchars($e['enquiry_for']); ?></td></tr>
          <tr><th><?php echo get_phrase('source'); ?></th><td><?php echo htmlspecialchars($e['source']); ?><?php echo $e['source_student'] ? ' ('.htmlspecialchars($e['source_student']).')' : ''; ?></td></tr>
          <tr><th><?php echo get_phrase('year'); ?></th><td><?php echo htmlspecialchars($e['session_name']); ?></td></tr>
          <tr><th><?php echo get_phrase('enquiry_date'); ?></th><td><?php echo htmlspecialchars($e['enquiry_date']); ?></td></tr>
          <tr><th><?php echo get_phrase('created_by'); ?></th><td><?php echo htmlspecialchars($e['created_by']); ?></td></tr>
        </table>
      </div>
    </div>

    <div class="panel panel-gradient">
      <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('update_status'); ?></div></div>
      <div class="panel-body">
        <form action="<?php echo base_url();?>index.php?admin/enquiry/save_status/<?php echo $e['enquiry_id'];?>" method="POST">
          <div class="form-group">
            <label><?php echo get_phrase('status'); ?></label>
            <select name="status" class="form-control">
              <option value="in_progress" <?php echo $status=='in_progress'?'selected':''; ?>>In Progress</option>
              <option value="joined"      <?php echo $status=='joined'?'selected':''; ?>>Joined</option>
              <option value="not_joined"  <?php echo $status=='not_joined'?'selected':''; ?>>Not Joined</option>
            </select>
          </div>
          <div class="form-group">
            <label><?php echo get_phrase('assign_to'); ?> (Collaborator)</label>
            <select name="assign_to" class="form-control">
              <option value="">-- <?php echo get_phrase('select_staff'); ?> --</option>
              <?php foreach ($staff as $st): ?>
                  <option value="<?php echo $st['teacher_id']; ?>" <?php echo ($e['assign_to']==$st['teacher_id'])?'selected':''; ?>><?php echo htmlspecialchars($st['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label><?php echo get_phrase('handled_by'); ?></label>
            <select name="handled_by" class="form-control">
              <option value="">-- <?php echo get_phrase('select_staff'); ?> --</option>
              <?php foreach ($staff as $st): ?>
                  <option value="<?php echo $st['teacher_id']; ?>" <?php echo ($e['handled_by']==$st['teacher_id'])?'selected':''; ?>><?php echo htmlspecialchars($st['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label><?php echo get_phrase('remark'); ?></label>
            <textarea name="remark" class="form-control" rows="3"><?php echo htmlspecialchars($e['remark']); ?></textarea>
          </div>
          <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save_status'); ?></button>
          <a href="<?php echo base_url();?>index.php?admin/enquiry/nominate/<?php echo $e['enquiry_id'];?>"
             class="btn btn-primary" onclick="return confirm('Nominate this enquiry for admission and open the student form?');">
             <i class="entypo-user-add"></i> <?php echo get_phrase('nominate'); ?>
          </a>
        </form>
      </div>
    </div>
  </div>

  <!-- RIGHT: activity log -->
  <div class="col-md-7">
    <div class="panel panel-gradient">
      <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('all_activities'); ?></div></div>
      <div class="panel-body">
        <form action="<?php echo base_url();?>index.php?admin/enquiry/add_activity/<?php echo $e['enquiry_id'];?>" method="POST" class="form-inline" style="margin-bottom:15px;">
          <input type="text" name="note" class="form-control" placeholder="<?php echo get_phrase('add_activity_note'); ?>" style="width:60%;" required>
          <select name="status" class="form-control">
            <option value="in_progress">In Progress</option>
            <option value="joined">Joined</option>
            <option value="not_joined">Not Joined</option>
          </select>
          <button type="submit" class="btn btn-info"><i class="entypo-plus"></i> <?php echo get_phrase('add'); ?></button>
        </form>

        <table class="table table-bordered table-striped">
          <thead>
            <tr><th><?php echo get_phrase('date'); ?></th><th><?php echo get_phrase('status'); ?></th><th><?php echo get_phrase('remark'); ?></th><th><?php echo get_phrase('by'); ?></th></tr>
          </thead>
          <tbody>
            <?php if (empty($activities)): ?>
              <tr><td colspan="4" class="text-center"><?php echo get_phrase('no_activities_yet'); ?></td></tr>
            <?php else: foreach ($activities as $a): ?>
              <tr>
                <td><?php echo htmlspecialchars($a['created_at']); ?></td>
                <td><?php echo htmlspecialchars($a['status']); ?></td>
                <td><?php echo htmlspecialchars($a['note']); ?></td>
                <td><?php echo htmlspecialchars($a['created_by']); ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
        <a href="<?php echo base_url();?>index.php?admin/enquiry" class="btn btn-default"><i class="entypo-left"></i> <?php echo get_phrase('back_to_list'); ?></a>
      </div>
    </div>
  </div>
</div>
