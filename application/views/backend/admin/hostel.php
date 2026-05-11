<hr>

 <div class="panel panel-gradient" >
            
                <div class="panel-heading">
                    <div class="panel-title">
					 <?php echo get_phrase('hestel_information_page'); ?>
					</div>
					</div>
<div class="table-responsive">
<br>
           <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-6">
                    <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_hostel_add/');" 
                        class="btn btn-primary">
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('add_new_hostel');?>
                    </a>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo base_url();?>index.php?admin/export_list/hostel/excel" target="_blank" class="btn btn-info btn-sm">Excel</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/hostel/pdf" target="_blank" class="btn btn-danger btn-sm">PDF</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/hostel/print" target="_blank" class="btn btn-default btn-sm">Print</a>
                </div>
            </div>
				
               <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th width="80"><div><?php echo get_phrase('photo');?></div></th>
                            <th><div><?php echo get_phrase('name');?></div></th>
                            <th><div><?php echo get_phrase('email');?></div></th>
                            <th><div><?php echo get_phrase('options');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                                $hostels	=	$this->db->get('hostel' )->result_array();
                                foreach($hostels as $row):?>
                        <tr>
                            <td><img src="<?php echo $this->crud_model->get_image_url('hostel',$row['hostel_id']);?>" class="img-circle" width="30" /></td>
                            <td><?php echo $row['name'];?></td>
                            <td><?php echo $row['email'];?></td>
                            <td>
                                
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Actions <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-primary pull-right" role="menu">
                                        
                                        <!-- hostel EDITING LINK -->
                                        <li>
                                        	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_hostel_edit/<?php echo $row['hostel_id'];?>');">
                                            	<i class="entypo-pencil"></i>
													<?php echo get_phrase('edit');?>
                                               	</a>
                                        				</li>
                                        <li class="divider"></li>
                                        
                                        <!-- hostel DELETION LINK -->
                                        <li>
                                        	<a href="#" onclick="confirm_modal('<?php echo base_url();?>index.php?admin/hostel/delete/<?php echo $row['hostel_id'];?>');">
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
