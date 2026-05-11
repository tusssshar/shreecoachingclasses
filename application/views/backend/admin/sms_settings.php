<hr> 
<div class="panel panel-gradient" >
            
                <div class="panel-heading">
                    <div class="panel-title">
					 <?php echo get_phrase('sms_information_page'); ?>
					</div>
					</div>
<div class="table-responsive">
<br>
<?php 
	$active_sms_service = $this->db->get_where('settings' , array(
		'type' => 'active_sms_service'
	))->row()->description;
?>
<div class="row">
	<div class="col-md-12">
	
		<div class="tabs-vertical-env">
		
			<ul class="nav tabs-vertical">
			<li class="active"><a href="#b-profile" data-toggle="tab">Select A SMS Service</a></li>
				<li>
					<a href="#v-home" data-toggle="tab">
						Clickatell Settings
						<?php if ($active_sms_service == 'clickatell'):?>  
							<span class="badge badge-success"><?php echo get_phrase('active');?></span>
						<?php endif;?>
					</a>
				</li>
				<li>
					<a href="#v-profile" data-toggle="tab">
						Twilio Settings
						<?php if ($active_sms_service == 'twilio'):?>
							<span class="badge badge-success"><?php echo get_phrase('active');?></span>
						<?php endif;?>
					</a>
				</li>
				<li>
					<a href="#v-whatsapp" data-toggle="tab">
						WhatsApp Settings
						<?php
							$active_whatsapp_row = $this->db->get_where('settings', array('type' => 'active_whatsapp'))->row();
							if ($active_whatsapp_row && $active_whatsapp_row->description == 'enabled'):
						?>
							<span class="badge badge-success"><?php echo get_phrase('active');?></span>
						<?php endif;?>
					</a>
				</li>
			</ul>
			
			<div class="tab-content">

				<div class="tab-pane active" id="b-profile">

					<?php echo form_open(base_url() . 'index.php?admin/sms_settings/active_service' , 
						array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>

					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo get_phrase('select_a_service');?></label>
                        <div class="col-sm-5">
							<select name="active_sms_service" class="form-control">
                              <option value=""<?php if ($active_sms_service == '') echo 'selected';?>>
                              		<?php echo get_phrase('not_selected');?>
                              	</option>
                        		<option value="clickatell"
                        			<?php if ($active_sms_service == 'clickatell') echo 'selected';?>>
                        				Clickatell
                        		</option>
                        		<option value="twilio"
                        			<?php if ($active_sms_service == 'twilio') echo 'selected';?>>
                        				Twilio
                        		</option>
                        		<option value="disabled"<?php if ($active_sms_service == 'disabled') echo 'selected';?>>
                        			<?php echo get_phrase('disabled');?>
                        		</option>
                          </select>
						</div> 
					</div>
					<div class="form-group">
	                    <div class="col-sm-offset-3 col-sm-5">
                                  <button type="submit" class="btn btn-success btn-sm btn-icon icon-left"><i class="entypo-pencil"></i><?php echo get_phrase('save');?></button>
	                    </div>
	                </div>
	            <?php echo form_close();?>
				</div>

				<div class="tab-pane" id="v-home">
					<?php echo form_open(base_url() . 'index.php?admin/sms_settings/clickatell' , 
						array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>
						<div class="form-group">
	                      <label  class="col-sm-3 control-label"><?php echo get_phrase('clickatell_username');?></label>
	                      	<div class="col-sm-5">
	                          	<input type="text" class="form-control" name="clickatell_user" 
	                            	value="<?php echo $this->db->get_where('settings' , array('type' =>'clickatell_user'))->row()->description;?>">
	                      	</div>
	                  	</div>
	                  	<div class="form-group">
	                        <label  class="col-sm-3 control-label"><?php echo get_phrase('clickatell_password');?></label>
	                        <div class="col-sm-5">
	                            <input type="text" class="form-control" name="clickatell_password" 
	                                value="<?php echo $this->db->get_where('settings' , array('type' =>'clickatell_password'))->row()->description;?>">
	                        </div>
	                    </div>
	                    <div class="form-group">
	                      <label  class="col-sm-3 control-label"><?php echo get_phrase('clickatell_api_id');?></label>
	                        <div class="col-sm-5">
	                            <input type="text" class="form-control" name="clickatell_api_id" 
	                                value="<?php echo $this->db->get_where('settings' , array('type' =>'clickatell_api_id'))->row()->description;?>">
	                        </div>
	                    </div>
	                    <div class="form-group">
		                    <div class="col-sm-offset-3 col-sm-5">
                                  <button type="submit" class="btn btn-success btn-sm btn-icon icon-left"><i class="entypo-pencil"></i><?php echo get_phrase('save');?></button>
		                    </div>
		                </div>
	                <?php echo form_close();?>
				</div>
				<div class="tab-pane" id="v-profile">
					<?php echo form_open(base_url() . 'index.php?admin/sms_settings/twilio' , 
						array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>
						<div class="form-group">
	                      <label  class="col-sm-3 control-label"><?php echo get_phrase('twilio_account');?> SID</label>
	                      	<div class="col-sm-5">
	                          	<input type="text" class="form-control" name="twilio_account_sid" 
	                            	value="<?php echo $this->db->get_where('settings' , array('type' =>'twilio_account_sid'))->row()->description;?>">
	                      	</div>
	                  	</div>
	                  	<div class="form-group">
	                        <label  class="col-sm-3 control-label"><?php echo get_phrase('authentication_token');?></label>
	                        <div class="col-sm-5">
	                            <input type="text" class="form-control" name="twilio_auth_token" 
	                                value="<?php echo $this->db->get_where('settings' , array('type' =>'twilio_auth_token'))->row()->description;?>">
	                        </div>
	                    </div>
	                    <div class="form-group">
	                      <label  class="col-sm-3 control-label"><?php echo get_phrase('registered_phone_number');?></label>
	                        <div class="col-sm-5">
	                            <input type="text" class="form-control" name="twilio_sender_phone_number" 
	                                value="<?php echo $this->db->get_where('settings' , array('type' =>'twilio_sender_phone_number'))->row()->description;?>">
	                        </div>
	                    </div>
	                    <div class="form-group">
		                    <div class="col-sm-offset-3 col-sm-5">
                                  <button type="submit" class="btn btn-success btn-sm btn-icon icon-left"><i class="entypo-pencil"></i><?php echo get_phrase('save');?></button>
		                    </div>
		                </div>
	                <?php echo form_close();?>
				</div>

				<div class="tab-pane" id="v-whatsapp">
					<?php
						$active_whatsapp_val = '';
						$row_aw = $this->db->get_where('settings', array('type' => 'active_whatsapp'))->row();
						if ($row_aw) $active_whatsapp_val = $row_aw->description;

						$wa_number_val = '';
						$row_wn = $this->db->get_where('settings', array('type' => 'twilio_whatsapp_number'))->row();
						if ($row_wn) $wa_number_val = $row_wn->description;

						$wa_msg_val = '';
						$row_wm = $this->db->get_where('settings', array('type' => 'whatsapp_welcome_message'))->row();
						if ($row_wm) $wa_msg_val = $row_wm->description;
					?>
					<?php echo form_open(base_url() . 'index.php?admin/sms_settings/whatsapp',
						array('class' => 'form-horizontal form-groups-bordered validate','target'=>'_top'));?>

						<div class="form-group">
							<label class="col-sm-3 control-label" for="active_whatsapp">WhatsApp Status</label>
							<div class="col-sm-5">
								<select id="active_whatsapp" name="active_whatsapp" class="form-control">
									<option value="enabled"  <?php if ($active_whatsapp_val == 'enabled')  echo 'selected'; ?>>Enabled</option>
									<option value="disabled" <?php if ($active_whatsapp_val != 'enabled') echo 'selected'; ?>>Disabled</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" for="twilio_whatsapp_number">WhatsApp From Number</label>
							<div class="col-sm-5">
								<input type="text" id="twilio_whatsapp_number" class="form-control" name="twilio_whatsapp_number"
									value="<?php echo htmlspecialchars($wa_number_val); ?>"
									placeholder="+14155238886">
								<small class="form-text text-muted">Twilio sandbox default: <code>+14155238886</code>. Include country code with leading +.</small>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label" for="whatsapp_welcome_message">Welcome Message Template</label>
							<div class="col-sm-7">
								<textarea id="whatsapp_welcome_message" class="form-control" name="whatsapp_welcome_message" rows="6"><?php echo htmlspecialchars($wa_msg_val); ?></textarea>
								<small class="form-text text-muted">
									Available placeholders:
									<code>{{studentname}}</code>,
									<code>{{studentid}}</code>,
									<code>{{schoolname}}</code>,
									<code>{{class}}</code>
								</small>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-5">
								<button type="submit" class="btn btn-success btn-sm btn-icon icon-left"><i class="entypo-pencil"></i><?php echo get_phrase('save');?></button>
							</div>
						</div>
					<?php echo form_close();?>
				</div>

			</div>

		</div>
	
	</div>
</div>
</div>
</div>