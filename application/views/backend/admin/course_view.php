<?php
    $c = $course;
    $installment_total = 0;
    foreach ($installments as $i) { $installment_total += (float)$i['amount']; }
?>
<div class="row">
  <!-- Course + fees summary -->
  <div class="col-md-5">
    <div class="panel panel-primary">
      <div class="panel-heading"><div class="panel-title"><?php echo htmlspecialchars($c['name']); ?></div></div>
      <div class="panel-body">
        <table class="table table-bordered">
          <tr><th><?php echo get_phrase('standard'); ?></th><td><?php echo htmlspecialchars($c['standard_name']); ?></td></tr>
          <tr><th><?php echo get_phrase('year'); ?></th><td><?php echo htmlspecialchars($c['session_name']); ?></td></tr>
          <tr><th><?php echo get_phrase('total_course_fees'); ?></th><td><strong><?php echo number_format((float)$c['total_fees'], 2); ?></strong></td></tr>
          <tr><th><?php echo get_phrase('installments'); ?></th><td><?php echo (int)$c['installments']; ?></td></tr>
          <?php if (!empty($c['description'])): ?><tr><th><?php echo get_phrase('description'); ?></th><td><?php echo htmlspecialchars($c['description']); ?></td></tr><?php endif; ?>
        </table>
        <a href="<?php echo base_url();?>index.php?admin/course_add/edit/<?php echo $c['course_id'];?>" class="btn btn-info btn-sm"><i class="entypo-pencil"></i> <?php echo get_phrase('edit_course'); ?></a>
        <a href="<?php echo base_url();?>index.php?admin/course" class="btn btn-default btn-sm"><i class="entypo-left"></i> <?php echo get_phrase('back_to_list'); ?></a>
      </div>
    </div>

    <!-- Subjects -->
    <div class="panel panel-gradient">
      <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('subjects'); ?></div></div>
      <div class="panel-body">
        <form action="<?php echo base_url();?>index.php?admin/course_view/<?php echo $c['course_id'];?>/add_subject" method="POST" class="form-inline" style="margin-bottom:12px;">
          <input type="text" name="subject_name" class="form-control" placeholder="<?php echo get_phrase('subject_name'); ?>" style="width:45%;" required>
          <input type="text" name="subject_code" class="form-control" placeholder="<?php echo get_phrase('subject_id'); ?>" style="width:30%;">
          <button type="submit" class="btn btn-info"><i class="entypo-plus"></i> <?php echo get_phrase('add'); ?></button>
        </form>
        <table class="table table-bordered table-striped">
          <thead><tr><th><?php echo get_phrase('subject_name'); ?></th><th><?php echo get_phrase('subject_id'); ?></th><th></th></tr></thead>
          <tbody>
            <?php if (empty($subjects)): ?>
              <tr><td colspan="3" class="text-center"><?php echo get_phrase('no_subjects_yet'); ?></td></tr>
            <?php else: foreach ($subjects as $s): ?>
              <tr>
                <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                <td><?php echo htmlspecialchars($s['subject_code']); ?></td>
                <td><a href="<?php echo base_url();?>index.php?admin/course_view/<?php echo $c['course_id'];?>/delete_subject/<?php echo $s['csubject_id'];?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this subject?');"><i class="entypo-cancel"></i></a></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Installments / fee schedule -->
  <div class="col-md-7">
    <div class="panel panel-gradient">
      <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('fee_installments'); ?></div></div>
      <div class="panel-body">
        <div class="row" style="margin-bottom:6px;font-weight:bold;">
          <div class="col-sm-4"><?php echo get_phrase('installment'); ?></div>
          <div class="col-sm-3"><?php echo get_phrase('amount'); ?></div>
          <div class="col-sm-3"><?php echo get_phrase('due_date'); ?></div>
          <div class="col-sm-2"></div>
        </div>
        <?php foreach ($installments as $ins): ?>
          <form action="<?php echo base_url();?>index.php?admin/course_view/<?php echo $c['course_id'];?>/save_installment/<?php echo $ins['installment_id'];?>" method="POST">
            <div class="row" style="margin-bottom:8px;">
              <div class="col-sm-4"><input type="text" name="title" class="form-control input-sm" value="<?php echo htmlspecialchars($ins['title']); ?>"></div>
              <div class="col-sm-3"><input type="number" step="0.01" name="amount" class="form-control input-sm" value="<?php echo htmlspecialchars($ins['amount']); ?>"></div>
              <div class="col-sm-3"><input type="date" name="due_date" class="form-control input-sm" value="<?php echo htmlspecialchars($ins['due_date']); ?>"></div>
              <div class="col-sm-2"><button type="submit" class="btn btn-success btn-xs"><i class="entypo-check"></i> <?php echo get_phrase('save'); ?></button></div>
            </div>
          </form>
        <?php endforeach; ?>
        <hr>
        <div class="row" style="font-weight:bold;">
          <div class="col-sm-4 text-right"><?php echo get_phrase('installment_total'); ?></div>
          <div class="col-sm-8">
            <?php echo number_format($installment_total, 2); ?>
            <?php if (round($installment_total, 2) != round((float)$c['total_fees'], 2)): ?>
              <span class="label label-warning" title="Installment total does not match the course fee"><?php echo get_phrase('mismatch_with_course_fee'); ?></span>
            <?php endif; ?>
          </div>
        </div>
        <p class="text-muted"><?php echo get_phrase('installments_edit_hint'); ?></p>
      </div>
    </div>
  </div>
</div>
