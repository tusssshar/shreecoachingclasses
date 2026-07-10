<?php
    $c = isset($edit_data) ? $edit_data : array();
    $is_edit = !empty($c);
    $val = function ($k, $d = '') use ($c) { return isset($c[$k]) && $c[$k] !== null ? htmlspecialchars($c[$k]) : $d; };
    $running_session = $this->db->get_where('settings', array('type' => 'session'))->row()->description;
    $action = $is_edit
        ? base_url().'index.php?admin/course/do_update/'.$c['course_id']
        : base_url().'index.php?admin/course/create';
?>
<div class="row">
  <div class="col-md-10">
    <div class="panel panel-primary">
      <div class="panel-heading"><div class="panel-title"><h3><?php echo $is_edit ? get_phrase('edit_course') : get_phrase('new_course'); ?></h3></div></div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="POST" class="form-horizontal form-groups-bordered validate">

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('course_name'); ?></label>
            <div class="col-sm-6">
              <input type="text" name="name" class="form-control" data-validate="required" data-message-required="<?php echo get_phrase('value_required');?>" value="<?php echo $val('name'); ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('standard'); ?> (Nursery – 12th)</label>
            <div class="col-sm-6">
              <select name="class_id" class="form-control select2">
                <option value="">-- <?php echo get_phrase('select_standard'); ?> --</option>
                <?php foreach ($classes as $cl): ?>
                    <option value="<?php echo $cl['class_id']; ?>" <?php echo ($is_edit && $c['class_id']==$cl['class_id'])?'selected':''; ?>><?php echo htmlspecialchars($cl['name']); ?></option>
                <?php endforeach; ?>
              </select>
              <span class="help-block"><?php echo get_phrase('standard_hint'); ?></span>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('year'); ?></label>
            <div class="col-sm-6">
              <select name="session_name" class="form-control select2">
                <?php foreach ($sessions as $s):
                    $sel = $is_edit ? ($c['session_name']==$s['name']) : ($s['name']==$running_session); ?>
                    <option value="<?php echo $s['name']; ?>" <?php echo $sel?'selected':''; ?>><?php echo $s['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('total_course_fees'); ?></label>
            <div class="col-sm-6">
              <input type="number" step="0.01" name="total_fees" class="form-control" value="<?php echo $val('total_fees', '0'); ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('number_of_installments'); ?></label>
            <div class="col-sm-6">
              <input type="number" min="1" name="installments" class="form-control" value="<?php echo $val('installments', '1'); ?>">
              <span class="help-block"><?php echo get_phrase('installments_hint'); ?></span>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('description'); ?></label>
            <div class="col-sm-6">
              <textarea name="description" class="form-control" rows="2"><?php echo $val('description'); ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
              <button type="submit" class="btn btn-success"><i class="entypo-check"></i> <?php echo get_phrase('save'); ?></button>
              <a href="<?php echo base_url();?>index.php?admin/course" class="btn btn-default"><?php echo get_phrase('cancel'); ?></a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    jQuery(window).load(function () { jQuery('.select2').select2(); });
</script>
