<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$row = $this->db->get_where('expense_category', array('expense_category_id' => $param2))->row_array();
if (!$row) { echo '<div class="alert alert-danger">Expense category not found.</div>'; return; }
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><i class="entypo-pencil"></i> <?php echo get_phrase('edit_expense_category'); ?></div>
            </div>
            <div class="panel-body">
                <?php echo form_open(base_url() . 'index.php?admin/expense_category/edit/' . $row['expense_category_id'] . '/',
                    array('class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data')); ?>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo get_phrase('name'); ?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name" required value="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Amount (&#8377;)</label>
                        <div class="col-sm-6">
                            <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo htmlspecialchars($row['amount'] ?? '0'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Year</label>
                        <div class="col-sm-6">
                            <input type="number" min="1990" max="2100" class="form-control" name="year" value="<?php echo htmlspecialchars($row['year'] ?? date('Y')); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-5">
                            <button type="submit" class="btn btn-info">Update Expense Category</button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
