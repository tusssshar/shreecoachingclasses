<?php $running_session = $this->db->get_where('settings', array('type' => 'session'))->row()->description; ?>
<div class="row">
  <div class="col-md-8">
    <div class="panel panel-primary">
      <div class="panel-heading"><div class="panel-title"><h3><?php echo get_phrase('bulk_enquiry_import'); ?></h3></div></div>
      <div class="panel-body">

        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

        <blockquote>
          <p><?php echo get_phrase('upload_xls_csv_instructions'); ?></p>
          <p>Columns: <code>enquiry_no, enquiry_date, enquiry_for, name, course, source, gender, mobile, address</code></p>
        </blockquote>

        <a href="<?php echo base_url();?>index.php?admin/enquiry_bulk_add/template" class="btn btn-default btn-icon icon-left">
          <i class="entypo-download"></i> <?php echo get_phrase('download_template'); ?>
        </a>
        <hr>

        <form action="<?php echo base_url();?>index.php?admin/enquiry_bulk_add/import_excel" method="POST" enctype="multipart/form-data" class="form-horizontal form-groups-bordered">
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('year'); ?></label>
            <div class="col-sm-6">
              <select name="session_name" class="form-control">
                <?php foreach ($sessions as $s): ?>
                    <option value="<?php echo $s['name']; ?>" <?php echo ($s['name']==$running_session)?'selected':''; ?>><?php echo $s['name']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo get_phrase('choose_file'); ?> (.xlsx / .csv)</label>
            <div class="col-sm-6">
              <input type="file" name="userfile" class="form-control" accept=".xlsx,.csv" required>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
              <button type="submit" class="btn btn-success"><i class="entypo-upload"></i> <?php echo get_phrase('import'); ?></button>
              <a href="<?php echo base_url();?>index.php?admin/enquiry" class="btn btn-default"><?php echo get_phrase('cancel'); ?></a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
