<hr> 
<div class="panel panel-gradient" >
            
                <div class="panel-heading">
                    <div class="panel-title">
					 <?php echo get_phrase('expense_information_page'); ?>
					</div>
					</div>
<div class="table-responsive">
<br>
<div class="row" style="margin-bottom: 15px;">
    <div class="col-sm-5">
        <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/expense_category_add/');" class="btn btn-primary">
            <i class="entypo-plus-circled"></i>
            <?php echo get_phrase('add_new_expense_category');?>
        </a>
    </div>
    <div class="col-sm-7 text-right">
        <?php
            $years_query = $this->db->distinct()->select('year')->where('year IS NOT NULL', null, false)->order_by('year', 'desc')->get('expense_category')->result_array();
            $available_years = array();
            foreach ($years_query as $yr) {
                if (!empty($yr['year'])) $available_years[] = (int) $yr['year'];
            }
            $current_year = (int) date('Y');
            if (!in_array($current_year, $available_years)) array_unshift($available_years, $current_year);
        ?>
        <div class="btn-group" style="margin-right:8px;">
            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown">
                <i class="entypo-doc-text"></i> Export by Year <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <?php foreach ($available_years as $yr): ?>
                    <li>
                        <a href="<?php echo base_url(); ?>index.php?admin/export_expenses_by_year/<?php echo $yr; ?>" target="_blank">
                            <i class="entypo-calendar"></i> Year <?php echo $yr; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a href="<?php echo base_url();?>index.php?admin/export_list/expense_category/excel" target="_blank" class="btn btn-info btn-sm">Excel</a>
        <a href="<?php echo base_url();?>index.php?admin/export_list/expense_category/pdf" target="_blank" class="btn btn-danger btn-sm">PDF</a>
        <a href="<?php echo base_url();?>index.php?admin/export_list/expense_category/print" target="_blank" class="btn btn-default btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered datatable" id="table_export">
    <thead>
        <tr>
            <th><div>#</div></th>
            <th><div><?php echo get_phrase('name');?></div></th>
            <th><div>Amount</div></th>
            <th><div>Year</div></th>
            <th><div><?php echo get_phrase('options');?></div></th>
        </tr>
    </thead>
    <tbody>
        <?php
        	$count = 1;
        	$expenses = $this->db->get('expense_category')->result_array();
        	foreach ($expenses as $row):
        ?>
        <tr>
            <td><?php echo $count++;?></td>
            <td><?php echo $row['name'];?></td>
            <td>&#8377; <?php echo number_format(floatval($row['amount'] ?? 0), 2); ?></td>
            <td><?php echo htmlspecialchars($row['year'] ?? '-'); ?></td>
            <td>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                        Action <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-primary pull-right" role="menu">
                        
                        <!-- teacher EDITING LINK -->
                        <li>
                        	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/expense_category_edit/<?php echo $row['expense_category_id'];?>');">
                            	<i class="entypo-pencil"></i>
									<?php echo get_phrase('edit');?>
                               	</a>
                        				</li>
                        <li class="divider"></li>
                        
                        <!-- teacher DELETION LINK -->
                        <li>
                        	<a href="#" onclick="confirm_modal('<?php echo base_url();?>index.php?admin/expense_category/delete/<?php echo $row['expense_category_id'];?>');">
                            	<i class="entypo-trash"></i>
									<?php echo get_phrase('delete');?>
                               	</a>
                        				</li>
                    </ul>
                </div>
                
            </td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
</div>
</div>


<!-----  DATA TABLE CONFIGURATION ---->
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#table_export").dataTable();

        $(".dataTables_wrapper select").select2({
            minimumResultsForSearch: -1
        });
    });
</script>
