<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$row = $this->db->get_where('transport', array('transport_id' => $param2))->row_array();
if (!$row) { echo '<div class="alert alert-danger">Picnic not found.</div>'; return; }
?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">Edit Picnic</div>
    </div>
    <div class="panel-body">
        <form action="<?php echo base_url(); ?>index.php?admin/transport/do_update/<?php echo $row['transport_id']; ?>"
              method="post" enctype="multipart/form-data" class="form-horizontal" target="_top">

            <div class="form-group">
                <label class="col-sm-3 control-label">Picnic Name</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="route_name" value="<?php echo htmlspecialchars($row['route_name']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Picnic Date</label>
                <div class="col-sm-7">
                    <input type="date" class="form-control" name="picnic_date" value="<?php echo htmlspecialchars($row['picnic_date'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Location</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($row['location'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Number of Vehicles</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="number_of_vehicle" value="<?php echo htmlspecialchars($row['number_of_vehicle']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Per-Student Fee (&#8377;)</label>
                <div class="col-sm-7">
                    <input type="number" step="0.01" class="form-control" name="route_fare" value="<?php echo htmlspecialchars($row['route_fare']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Total Expenses (&#8377;)</label>
                <div class="col-sm-7">
                    <input type="number" step="0.01" class="form-control" name="expenses" value="<?php echo htmlspecialchars($row['expenses'] ?? '0'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Bill</label>
                <div class="col-sm-7">
                    <?php if (!empty($row['bill_file'])): ?>
                        <p>
                            <a href="<?php echo base_url('uploads/picnic_bills/' . $row['bill_file']); ?>" target="_blank" class="btn btn-info btn-xs">
                                <i class="entypo-doc"></i> View Current Bill
                            </a>
                            <span class="text-muted" style="margin-left:8px;">Upload a new file to replace it.</span>
                        </p>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="bill_file" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Description</label>
                <div class="col-sm-7">
                    <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($row['description']); ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-7">
                    <button type="submit" class="btn btn-success">Update Picnic</button>
                </div>
            </div>
        </form>
    </div>
</div>
