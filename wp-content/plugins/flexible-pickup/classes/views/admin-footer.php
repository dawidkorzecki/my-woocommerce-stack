<script>
	jQuery(document).ready(function(){
	    jQuery('ul.subsubsub').append('<li> | <a href="<?php echo admin_url( 'edit.php?post_type=pickup_point&page=flexible-pickup-settings' ); ?>"><?php _e( 'Flexible Pickup', 'flexible-pickup' ); ?></a></li>');
	})
</script>