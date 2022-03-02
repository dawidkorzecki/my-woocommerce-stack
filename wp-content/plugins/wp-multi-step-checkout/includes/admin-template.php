<?php 
/**
 * Admin settings page
 */

defined( 'ABSPATH' ) || exit; 
?>

<h2>Multi-Step Checkout for WooCommerce by <img src="<?php echo plugins_url('/', WMSC_PLUGIN_FILE); ?>assets/images/silkypress_logo.png" /> <a href="https://www.silkypress.com/" target="_blank">SilkyPress</a></h2>

<div class="wrap">

	<h3 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<?php foreach ( $tabs as $_key => $_val ) : ?>
		<?php $active = ( $_key == $tab_current ) ? ' nav-tab-active' : ''; ?>
		<a href="?page=wmsc-settings&tab=<?php echo $_key ?>" class="nav-tab<?php echo $active; ?>"><?php _e($_val); ?></a>
		<?php endforeach; ?>
	</h3>

	<div class="panel panel-default">
    	<div class="panel-body">
			<div class="row">
				<div id="alert_messages">
				<?php echo $messages; ?>
				</div>
    
				<?php if( isset($without_form) && $without_form == true ) : ?>
				<div class="form-group">
					<?php echo $content; ?>
				</div>
				<?php else : ?>

				<form class="form-horizontal" method="post" action="" id="form_settings">
					<div class="form-group">
						<?php echo $content; ?>	

    					<div class="form-group">
	      					<div class="col-lg-6">
    	  						<button type="submit" class="btn btn-primary"><?php echo __('Save changes'); ?></button>
							</div>
						</div>
					</div>
				<?php wp_nonce_field( 'wmsc_' . $tab_current ); ?>
				</form>
				<?php endif; ?>
	    	</div>
		</div>
	</div>
</div>
