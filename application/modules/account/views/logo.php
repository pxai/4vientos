	<div id="logo">
	   <?php if ($this->authentication->is_signed_in()) : ?>
			<a href="<?=base_url()?>dir" title="4vientos">
		<?php else: ?>
			<a href="<?=base_url()?>account/sign_in" title="Login 4vientos">
		<?php endif; ?>
			<img src="<?=base_url()?>resource/images/logo.png" alt='4vientos logo' title="4vientos logo" />
		</a>
	</div>
