<hr>

 <div class="panel panel-gradient" >
            
                <div class="panel-heading">
                    <div class="panel-title">
					 <?php echo get_phrase('parent_information_page'); ?>
					</div>
					</div>
<div class="table-responsive">
<br>
           <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-6">
                    <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_parent_add/');" 
                        class="btn btn-primary">
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('add_new_parent');?>
                    </a>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo base_url();?>index.php?admin/export_list/parent/excel" target="_blank" class="btn btn-info btn-sm">Excel</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/parent/pdf" target="_blank" class="btn btn-danger btn-sm">PDF</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/parent/print" target="_blank" class="btn btn-default btn-sm">Print</a>
                </div>
            </div>
               <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><div><?php echo get_phrase('name');?></div></th>
                            <th><div><?php echo get_phrase('email');?></div></th>
                            <th><div><?php echo get_phrase('phone');?></div></th>
                            <th><div><?php echo get_phrase('profession');?></div></th>
                            <th><div><?php echo get_phrase('options');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $count = 1; 
                            $parents   =   $this->db->get('parent' )->result_array();
                            foreach($parents as $row):?>
                        <tr>
                            <td><?php echo $count++;?></td>
                            <td><?php echo $row['name'];?></td>
                            <td><?php echo $row['email'];?></td>
                            <td><?php echo $row['phone'];?></td>
                            <td><?php echo $row['profession'];?></td>
                            <td>
                                
                                <div class="btn-group">
                                    <button type="button" class="btn btn-blue btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-primary pull-right" role="menu">
                                        
                                        <!-- teacher EDITING LINK -->
                                        <li>
                                            <a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_parent_edit/<?php echo $row['parent_id'];?>');">
                                                <i class="entypo-pencil"></i>
                                                    <?php echo get_phrase('edit');?>
                                                </a>
                                                        </li>
                                        <li class="divider"></li>
                                        
                                        <!-- teacher DELETION LINK -->
                                        <li>
                                            <a href="#" onclick="confirm_modal('<?php echo base_url();?>index.php?admin/parent/delete/<?php echo $row['parent_id'];?>');">
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

<!-----  DATA TABLE EXPORT CONFIGURATIONS ---->                      
<script type="text/javascript">

    jQuery(document).ready(function($)
    {
        $("#table_export").dataTable();
        
        $(".dataTables_wrapper select").select2({
            minimumResultsForSearch: -1
        });
    });
        
</script>
