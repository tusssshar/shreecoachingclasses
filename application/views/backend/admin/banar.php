<hr> 
<div class="panel panel-gradient" >
            
                <div class="panel-heading">
                    <div class="panel-title">
					 <?php echo get_phrase('bannar_information_page'); ?>
					</div>
					</div>
<div class="table-responsive">
<br>
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-6">
                    <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_banar_add/');" 
                        class="btn btn-primary">
                        <i class="entypo-plus-circled"></i>
                        <?php echo get_phrase('add_new_bannar');?>
                    </a>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="<?php echo base_url();?>index.php?admin/export_list/banar/excel" target="_blank" class="btn btn-info btn-sm">Excel</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/banar/pdf" target="_blank" class="btn btn-danger btn-sm">PDF</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/banar/print" target="_blank" class="btn btn-default btn-sm">Print</a>
                </div>
            </div>
               <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th width="80"><div><?php echo get_phrase('front_end_banar');?></div></th>
                            <th><div><?php echo get_phrase('b_text_one');?></div></th>
                            <th><div><?php echo get_phrase('b_text_two');?></div></th>
                            <th><div><?php echo get_phrase('options');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                                $banars	=	$this->db->get('banar' )->result_array();
                                foreach($banars as $row):?>
                        <tr>
                            <td><img src="<?php echo $this->crud_model->get_image_url('banar',$row['banar_id']);?>" class="img-circle" width="30" /></td>
                            <td><?php echo $row['b_namea'];?></td>
                            <td><?php echo $row['b_namea'];?></td>
                            <td>
                                
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-default pull-right" role="menu">
                                        
                                        <!-- accountant EDITING LINK -->
                                        <li>
                                        	<a href="#" onclick="showAjaxModal('<?php echo base_url();?>index.php?modal/popup/modal_banar_edit/<?php echo $row['banar_id'];?>');">
                                            	<i class="entypo-pencil"></i>
													<?php echo get_phrase('edit');?>
                                               	</a>
                                        				</li>
                                        <li class="divider"></li>
                                        
                                        <!-- accountant DELETION LINK -->
                                        <li>
                                        	<a href="#" onclick="confirm_modal('<?php echo base_url();?>index.php?admin/banar/delete/<?php echo $row['banar_id'];?>');">
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
