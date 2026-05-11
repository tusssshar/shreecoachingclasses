<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">Manage Picnic Information</div>
    </div>

    <?php if ($this->session->flashdata('flash_message')): ?>
        <div class="alert alert-success alert-dismissable" style="margin: 12px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('flash_message'); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <ul class="nav nav-tabs bordered">
            <li class="active"><a href="#list" data-toggle="tab"><i class="entypo-menu"></i> Picnic List</a></li>
            <li><a href="#add" data-toggle="tab"><i class="entypo-plus-circled"></i> Add Picnic</a></li>
        </ul>

        <div class="tab-content">

            <!-- LIST -->
            <div class="tab-pane box active" id="list">
                <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th>Picnic Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Vehicles</th>
                            <th>Per-Student Fee</th>
                            <th>Expenses</th>
                            <th>Bill</th>
                            <th>Description</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transports as $row): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['route_name']); ?></strong></td>
                                <td><?php echo !empty($row['picnic_date']) ? date('d M Y', strtotime($row['picnic_date'])) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($row['location'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['number_of_vehicle']); ?></td>
                                <td>&#8377; <?php echo number_format(floatval($row['route_fare']), 2); ?></td>
                                <td>&#8377; <?php echo number_format(floatval($row['expenses'] ?? 0), 2); ?></td>
                                <td>
                                    <?php if (!empty($row['bill_file'])): ?>
                                        <a href="<?php echo base_url('uploads/picnic_bills/' . $row['bill_file']); ?>" target="_blank" class="btn btn-info btn-xs">
                                            <i class="entypo-doc"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-blue btn-sm dropdown-toggle" data-toggle="dropdown">
                                            Action <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-primary pull-right" role="menu">
                                            <li>
                                                <a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_transport_student/<?php echo $row['transport_id'];?>');">
                                                    <i class="entypo-users"></i> Students
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_edit_transport/<?php echo $row['transport_id'];?>');">
                                                    <i class="entypo-pencil"></i> Edit
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#" onclick="confirm_modal('<?php echo base_url();?>index.php?admin/transport/delete/<?php echo $row['transport_id'];?>');">
                                                    <i class="entypo-trash"></i> Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ADD -->
            <div class="tab-pane box" id="add" style="padding: 5px">
                <div class="box-content">
                    <form action="<?php echo base_url(); ?>index.php?admin/transport/create" method="post"
                          enctype="multipart/form-data" class="form-horizontal form-groups-bordered validate" target="_top">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Picnic Name <span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="route_name" required placeholder="e.g. Annual Picnic 2026">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Picnic Date</label>
                            <div class="col-sm-5">
                                <input type="date" class="form-control" name="picnic_date">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Location</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="location" placeholder="Destination / venue">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Number of Vehicles</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="number_of_vehicle" placeholder="e.g. 2 buses">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Per-Student Fee (&#8377;)</label>
                            <div class="col-sm-5">
                                <input type="number" step="0.01" class="form-control" name="route_fare" placeholder="0.00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Total Expenses (&#8377;)</label>
                            <div class="col-sm-5">
                                <input type="number" step="0.01" class="form-control" name="expenses" placeholder="0.00">
                                <small class="text-muted">Total amount spent by the institute on this picnic.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Upload Bill</label>
                            <div class="col-sm-5">
                                <input type="file" class="form-control" name="bill_file" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp">
                                <small class="text-muted">PDF, JPG, PNG, GIF, or WEBP.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-5">
                                <textarea class="form-control" name="description" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-5">
                                <button type="submit" class="btn btn-success btn-sm btn-icon icon-left">
                                    <i class="entypo-location"></i> Add Picnic
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
