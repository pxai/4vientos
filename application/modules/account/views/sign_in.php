<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo lang('website_title')." : " . lang('sign_in_page_name'); ?></title>
<?php echo $this->load->view('meta'); ?>
<base href="<?php echo base_url(); ?>" />
<link type="text/css" rel="stylesheet" href="resource/css/960gs/960gs.css" />
<link type="text/css" rel="stylesheet" href="resource/css/style.css" />
</head>
<body>
<?php echo $this->load->view('header'); ?>
<div id="content">





    <div class="container_12">
    <?php echo $this->load->view('logo'); ?>

 			<?php if (in_array("ldap",$this->config->item("validation"))): ?>

 			<div class="grid_12">
            <h2><?php echo anchor(uri_string().($this->input->get('continue')?'/?continue='.urlencode($this->input->get('continue')):''), lang('sign_in_page_name')); ?></h2>
         </div>
         <div class="clear"></div>
         <div id="ldap_validation" >

            <?php echo form_open(uri_string()."/sign_in_ldap".($this->input->get('continue')?'/?continue='.urlencode($this->input->get('continue')):'')); ?>
            <?php echo form_fieldset(); ?>

 	            <h3><?php echo lang('windows_validation'); ?></h3>
            <div class="grid_2 alpha">
                <?php echo form_label(lang('sign_in_ldap_username'), 'sign_in_ldap_username'); ?>
            </div>
            <div class="grid_4 omega">
                <?php echo form_input(array(
                        'name' => 'sign_in_ldap_username',
                        'id' => 'sign_in_ldap_username',
                        'value' => set_value('sign_in_ldap_username'),
                        'maxlength' => '255'
                    )); ?>
                <?php echo form_error('sign_in_ldap_username'); ?>
                <?php if (isset($sign_in_ldap_username_error)) : ?>
                <span class="field_error"><?php echo $sign_in_ldap_username_error; ?></span>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
            <div class="grid_2 alpha">
                <?php echo form_label(lang('sign_in_ldap_password'), 'sign_in_ldap_password'); ?>
            </div>
            <div class="grid_4 omega">
                <?php echo form_password(array(
                        'name' => 'sign_in_ldap_password',
                        'id' => 'sign_in_ldap_password',
                        'value' => set_value('sign_in_ldap_password')
                    )); ?>
                <?php echo form_error('sign_in_ldap_password'); ?>
            </div>
            <div class="clear"></div>
            <?php if (isset($recaptcha)) : ?>
            <div class="prefix_2 grid_4 alpha omega">
                <?php echo $recaptcha; ?>
            </div>
            <?php if (isset($sign_in_recaptcha_error)) : ?>
            <div class="prefix_2 grid_4 alpha omega">
                <span class="field_error"><?php echo $sign_in_recaptcha_error; ?></span>
            </div>
            <?php endif; ?>
            <div class="clear"></div>
            <?php endif; ?>
            <div class="prefix_2 grid_4 alpha omega">
                <span>
                    <?php echo form_button(array(
                            'type' => 'submit',
                            'class' => 'button',
                            'content' => lang('sign_in_ldap_sign_in')
                        )); ?>
                </span>
                <span>
                    <?php echo form_checkbox(array(
                            'name' => 'sign_in_ldap_remember',
                            'id' => 'sign_in_ldap_remember',
                            'value' => 'checked',
                            'checked' => $this->input->post('sign_in_ldap_remember'),
                            'class' => 'checkbox'
                        )); ?>
                    <?php echo form_label(lang('sign_in_ldap_remember_me'), 'sign_in_ldap_remember'); ?>
                </span>
            </div>
            <div class="clear"></div>
            <div class="prefix_2 grid_4 alpha omega">
                <p><?php echo lang('sign_in_ldap_forgot_your_password'); ?><br />
                <?php echo sprintf(lang('sign_in_ldap_dont_have_account'), lang('sign_in_ldap_sign_up_now')); ?></p>
            </div>
            <div class="clear"></div>
            <?php echo form_fieldset_close(); ?>
            <?php echo form_close(); ?>

         </div>
  			<?php endif; ?>
        
  <?php if (in_array("simple",$this->config->item("validation"))): ?>
         
        <div class="grid_12">
        		 			<?php if (in_array("ldap",$this->config->item("validation"))): ?>
        		 				<h2>Or</h2>
        		 			<?php else: ?>
            <h2><?php echo anchor(uri_string().($this->input->get('continue')?'/?continue='.urlencode($this->input->get('continue')):''), lang('sign_in_page_name')); ?></h2>
				  			<?php endif; ?>
        </div>
        <div class="clear"></div>
        <div class="grid_6">
            <?php echo form_open(uri_string().($this->input->get('continue')?'/?continue='.urlencode($this->input->get('continue')):'')); ?>
            <?php echo form_fieldset(); ?>
            <h3><?php echo lang('sign_in_heading'); ?></h3>
            <?php if (isset($sign_in_error)) : ?>
            <div class="grid_6 alpha omega">
                <div class="form_error"><?php echo $sign_in_error; ?></div>
            </div>
            <div class="clear"></div>
            <?php endif; ?>
            <div class="grid_2 alpha">
                <?php echo form_label(lang('sign_in_username_email'), 'sign_in_username_email'); ?>
            </div>
            <div class="grid_4 omega">
                <?php echo form_input(array(
                        'name' => 'sign_in_username_email',
                        'id' => 'sign_in_username_email',
                        'value' => set_value('sign_in_username_email'),
                        'maxlength' => '24'
                    )); ?>
                <?php echo form_error('sign_in_username_email'); ?>
                <?php if (isset($sign_in_username_email_error)) : ?>
                <span class="field_error"><?php echo $sign_in_username_email_error; ?></span>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
            <div class="grid_2 alpha">
                <?php echo form_label(lang('sign_in_password'), 'sign_in_password'); ?>
            </div>
            <div class="grid_4 omega">
                <?php echo form_password(array(
                        'name' => 'sign_in_password',
                        'id' => 'sign_in_password',
                        'value' => set_value('sign_in_password')
                    )); ?>
                <?php echo form_error('sign_in_password'); ?>
            </div>
            <div class="clear"></div>
            <?php if (isset($recaptcha)) : ?>
            <div class="prefix_2 grid_4 alpha omega">
                <?php echo $recaptcha; ?>
            </div>
            <?php if (isset($sign_in_recaptcha_error)) : ?>
            <div class="prefix_2 grid_4 alpha omega">
                <span class="field_error"><?php echo $sign_in_recaptcha_error; ?></span>
            </div>
            <?php endif; ?>
            <div class="clear"></div>
            <?php endif; ?>
            <div class="prefix_2 grid_4 alpha omega">
                <span>
                    <?php echo form_button(array(
                            'type' => 'submit',
                            'class' => 'button',
                            'content' => lang('sign_in_sign_in')
                        )); ?>
                </span>
                <span>
                    <?php echo form_checkbox(array(
                            'name' => 'sign_in_remember',
                            'id' => 'sign_in_remember',
                            'value' => 'checked',
                            'checked' => $this->input->post('sign_in_remember'),
                            'class' => 'checkbox'
                        )); ?>
                    <?php echo form_label(lang('sign_in_remember_me'), 'sign_in_remember'); ?>
                </span>
            </div>
            <div class="clear"></div>
            <div class="prefix_2 grid_4 alpha omega">
                <p><?php echo anchor('account/forgot_password', lang('sign_in_forgot_your_password')); ?><br />
                <?php echo sprintf(lang('sign_in_dont_have_account'), anchor('account/sign_up', lang('sign_in_sign_up_now'))); ?></p>
            </div>
            <div class="clear"></div>
            <?php echo form_fieldset_close(); ?>
            <?php echo form_close(); ?>
        </div>
        <div class="grid_6">
            <h3><?php echo sprintf(lang('sign_in_third_party_heading')); ?></h3>
            <ul>
                <?php foreach($this->config->item('third_party_auth_providers') as $provider) : ?>
                <li class="third_party <?php echo $provider; ?>"><?php echo anchor('account/connect_'.$provider, lang('connect_'.$provider), 
                    array('title'=>sprintf(lang('sign_in_with'), lang('connect_'.$provider)))); ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    
      	<?php endif; ?>
</div>
<?php echo $this->load->view('footer'); ?>
</body>
</html>