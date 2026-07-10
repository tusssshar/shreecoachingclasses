<?php
    $e = isset($edit_data) ? $edit_data : array();
    $is_edit = !empty($e);
    $val = function ($k, $default = '') use ($e) { return isset($e[$k]) && $e[$k] !== null ? htmlspecialchars($e[$k]) : $default; };

    $running_session = $this->db->get_where('settings', array('type' => 'session'))->row()->description;
    $action = $is_edit
        ? base_url().'index.php?admin/enquiry/do_update/'.$e['enquiry_id']
        : base_url().'index.php?admin/enquiry/create';

    $sources = array('Word of mouth', 'Pamphlet', 'Banner', 'Google search', 'Existing student');
?>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-primary" data-collapsed="0">
      <div class="panel-heading">
        <div class="panel-title"><h3><?php echo $is_edit ? get_phrase('edit_enquiry') : get_phrase('add_enquiry'); ?></h3></div>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="POST" class="form-horizontal form-groups-bordered validate">

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('year'); ?></label>
            <div class="col-sm-5">
              <select name="session_name" class="form-control select2">
                <?php foreach ($sessions as $s):
                    $sel = $is_edit ? ($e['session_name'] == $s['name']) : ($s['name'] == $running_session); ?>
                    <option value="<?php echo $s['name']; ?>" <?php echo $sel ? 'selected' : ''; ?>><?php echo $s['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('enquiry_no'); ?></label>
            <div class="col-sm-3">
              <input type="text" name="enquiry_no" class="form-control" value="<?php echo $val('enquiry_no', isset($next_no) ? $next_no : ''); ?>">
            </div>
            <label class="col-sm-2 control-label"><?php echo get_phrase('date'); ?></label>
            <div class="col-sm-3">
              <input type="date" name="enquiry_date" class="form-control" value="<?php echo $val('enquiry_date', date('Y-m-d')); ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('enquiry_for'); ?></label>
            <div class="col-sm-5">
              <input type="text" name="enquiry_for" class="form-control" placeholder="e.g. Class 5 admission" value="<?php echo $val('enquiry_for'); ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('enquiry_name'); ?></label>
            <div class="col-sm-5">
              <input type="text" name="name" class="form-control" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" value="<?php echo $val('name'); ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('course'); ?></label>
            <div class="col-sm-5">
              <?php if (!empty($courses)): ?>
                <select name="course" class="form-control select2">
                  <option value="">-- <?php echo get_phrase('select_course'); ?> --</option>
                  <?php foreach ($courses as $c): ?>
                      <option value="<?php echo htmlspecialchars($c['name']); ?>" <?php echo ($is_edit && $e['course'] == $c['name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              <?php else: ?>
                <input type="text" name="course" class="form-control" value="<?php echo $val('course'); ?>">
              <?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('source'); ?></label>
            <div class="col-sm-5">
              <select name="source" id="source_select" class="form-control select2" onchange="toggleSourceStudent()">
                <option value="">-- <?php echo get_phrase('select'); ?> --</option>
                <?php foreach ($sources as $src): ?>
                    <option value="<?php echo $src; ?>" <?php echo ($is_edit && $e['source'] == $src) ? 'selected' : ''; ?>><?php echo $src; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group" id="source_student_group" style="display:none;">
            <label class="col-sm-3 control-label"><?php echo get_phrase('existing_student_name'); ?></label>
            <div class="col-sm-5">
              <input type="text" name="source_student" class="form-control" value="<?php echo $val('source_student'); ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('gender'); ?></label>
            <div class="col-sm-5">
              <select name="gender" class="form-control select2">
                <option value="Male"   <?php echo ($val('gender') == 'Male')   ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($val('gender') == 'Female') ? 'selected' : ''; ?>>Female</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('contact_no'); ?></label>
            <div class="col-sm-5">
              <input type="text" name="mobile" class="form-control" pattern="^[0-9]{10}$" title="Please enter a 10-digit mobile number" value="<?php echo $val('mobile'); ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('address'); ?></label>
            <div class="col-sm-5">
              <textarea name="address" class="form-control" rows="2"><?php echo $val('address'); ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('assign_to'); ?></label>
            <div class="col-sm-5">
              <select name="assign_to" class="form-control select2">
                <option value="">-- <?php echo get_phrase('select_staff'); ?> --</option>
                <?php foreach ($staff as $st): ?>
                    <option value="<?php echo $st['teacher_id']; ?>" <?php echo ($is_edit && $e['assign_to'] == $st['teacher_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($st['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('handled_by'); ?></label>
            <div class="col-sm-5">
              <select name="handled_by" class="form-control select2">
                <option value="">-- <?php echo get_phrase('select_staff'); ?> --</option>
                <?php foreach ($staff as $st): ?>
                    <option value="<?php echo $st['teacher_id']; ?>" <?php echo ($is_edit && $e['handled_by'] == $st['teacher_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($st['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-5">
              <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save'); ?></button>
              <a href="<?php echo base_url();?>index.php?admin/enquiry" class="btn btn-default"><?php echo get_phrase('cancel'); ?></a>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    function toggleSourceStudent() {
        var v = document.getElementById('source_select').value;
        document.getElementById('source_student_group').style.display = (v === 'Existing student') ? 'block' : 'none';
    }
    jQuery(window).load(function () {
        jQuery('.select2').select2();
        toggleSourceStudent();
    });
</script>
