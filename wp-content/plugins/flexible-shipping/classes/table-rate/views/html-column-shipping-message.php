<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="flexible_shipping_message">
    <span class="<?php echo $messages[$post->ID]['status']; ?>">
		<?php echo $messages[$post->ID]['message']; ?>
	</span>
</div>