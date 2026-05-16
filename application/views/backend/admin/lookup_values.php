<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">Master Data</div>
    </div>
    <div class="panel-body">

        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?></div>
        <?php endif; ?>

        <ul class="nav nav-tabs" style="margin-bottom:18px;">
            <?php foreach ($categories as $cat => $label): ?>
                <li class="<?php echo ($active_category === $cat) ? 'active' : ''; ?>">
                    <a href="<?php echo base_url(); ?>index.php?admin/lookup_values/<?php echo $cat; ?>">
                        <?php echo htmlspecialchars($label); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="text-muted">
            These values appear in the <strong><?php echo htmlspecialchars($categories[$active_category]); ?></strong>
            dropdown on the Add Student / Edit Student pages. Inactive values stay in the database but disappear from forms.
        </p>

        <table class="table table-bordered table-striped" style="max-width:780px;">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Value</th>
                    <th style="width:110px;">Sort order</th>
                    <th style="width:100px;">Active</th>
                    <th style="width:220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($values)): ?>
                <tr><td colspan="5" class="text-muted text-center">No values yet — add the first one below.</td></tr>
            <?php else: $i = 1; foreach ($values as $row): ?>
                <tr>
                    <form method="post" action="<?php echo base_url(); ?>index.php?admin/lookup_values/<?php echo $active_category; ?>/edit/<?php echo (int)$row['lookup_id']; ?>">
                        <td><?php echo $i++; ?></td>
                        <td>
                            <input type="text" class="form-control" name="value" value="<?php echo htmlspecialchars($row['value']); ?>" required>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="sort_order" value="<?php echo (int)$row['sort_order']; ?>" min="0">
                        </td>
                        <td>
                            <?php if ((int)$row['is_active'] === 1): ?>
                                <span class="label label-success">Active</span>
                            <?php else: ?>
                                <span class="label label-default">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-info btn-sm" title="Save changes">
                                <i class="entypo-pencil"></i> Save
                            </button>
                            <a href="<?php echo base_url(); ?>index.php?admin/lookup_values/<?php echo $active_category; ?>/toggle/<?php echo (int)$row['lookup_id']; ?>"
                               class="btn btn-default btn-sm" title="Toggle active/inactive">
                                <i class="entypo-cw"></i>
                            </a>
                            <a href="#" onclick="if(confirm('Delete this value?')) window.location='<?php echo base_url(); ?>index.php?admin/lookup_values/<?php echo $active_category; ?>/delete/<?php echo (int)$row['lookup_id']; ?>';"
                               class="btn btn-danger btn-sm" title="Delete">
                                <i class="entypo-trash"></i>
                            </a>
                        </td>
                    </form>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>

        <hr>
        <h4>Add new <?php echo htmlspecialchars($categories[$active_category]); ?> value</h4>
        <form method="post" action="<?php echo base_url(); ?>index.php?admin/lookup_values/<?php echo $active_category; ?>/add" class="form-inline">
            <div class="form-group">
                <input type="text" name="value" class="form-control" placeholder="e.g. UPI" required>
            </div>
            <div class="form-group" style="margin-left:8px;">
                <input type="number" name="sort_order" class="form-control" placeholder="Sort order" value="<?php echo count($values) + 1; ?>" min="0" style="width:140px;">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-left:8px;">
                <i class="entypo-plus"></i> Add
            </button>
        </form>

    </div>
</div>
