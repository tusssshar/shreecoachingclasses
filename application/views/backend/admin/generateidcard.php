
           <div class="row" style="margin-bottom: 15px;">
                <div class="col-sm-12 text-right">
                    <a href="<?php echo base_url();?>index.php?admin/export_list/teacher/excel" target="_blank" class="btn btn-info btn-sm">Excel</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/teacher/pdf" target="_blank" class="btn btn-danger btn-sm">PDF</a>
                    <a href="<?php echo base_url();?>index.php?admin/export_list/teacher/print" target="_blank" class="btn btn-default btn-sm">Print</a>
                </div>
            </div>
               <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th width="80"><div><?php echo get_phrase('photo');?></div></th>
                            <th><div><?php echo get_phrase('name');?></div></th>
                            <th><div><?php echo get_phrase('sex');?></div></th>
                            <th><div><?php echo get_phrase('email');?></div></th>
                            <th><div><?php echo get_phrase('address');?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
						$generateidcard		=	$this->db->get_where('teacher' , array('teacher_id' => $teacher_id) )->result_array();
						foreach ( $generateidcard as $row):
						?>
                        <tr>
                            <td><img src="<?php echo $this->crud_model->get_image_url('teacher',$row['teacher_id']);?>" class="img-circle" width="30" /></td>
                            <td><?php echo $row['name'];?></td>
                            <td><?php echo $row['sex'];?></td>
                            <td><?php echo $row['email'];?></td>
                            <td><?php echo $row['address'];?></td>
                           
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>



<!-----  DATA TABLE CONFIGURATION ---->
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#table_export").dataTable();

        $(".dataTables_wrapper select").select2({
            minimumResultsForSearch: -1
        });
    });
</script>
