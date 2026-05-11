<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs bordered">
            <li class="active">
                <a href="#list" data-toggle="tab">
                    <i class="entypo-menu"></i> Board List
                </a>
            </li>
            <li>
                <a href="#add" data-toggle="tab">
                    <i class="entypo-plus-circled"></i> Add Board
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane box active" id="list">
                <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th><div>#</div></th>
                            <th><div>Board</div></th>
                            <th><div><?php echo get_phrase('options'); ?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; foreach ($boards as $row): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td>
                                    <a href="#" class="btn btn-danger btn-sm" onclick="confirm_modal('<?php echo base_url(); ?>index.php?admin/academic_syllabus/delete/<?php echo $row['board_id']; ?>');">
                                        <i class="entypo-trash"></i>
                                        <?php echo get_phrase('delete'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane box" id="add" style="padding: 5px">
                <div class="box-content">
                    <?php echo form_open(base_url() . 'index.php?admin/academic_syllabus/create', array('class' => 'form-horizontal form-groups-bordered validate', 'target' => '_top')); ?>
                        <div class="padded">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Board</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="name" data-validate="required" data-message-required="<?php echo get_phrase('value_required'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-5">
                                <button type="submit" class="btn btn-info">Add Board</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#table_export").dataTable();
    });
</script>
