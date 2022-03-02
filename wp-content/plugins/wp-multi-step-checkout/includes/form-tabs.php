<?php
/**
 * The steps tabs
 *
 * @package WPMultiStepCheckout
 */

defined( 'ABSPATH' ) || exit;

$i                  = 0;
$number_of_steps    = ( $show_login_step ) ? count( $steps ) + 1 : count( $steps );
$current_step_title = ( $show_login_step ) ? 'login' : key( array_slice( $steps, 0, 1, true ) );

do_action( 'wpmc_before_tabs' );

?>

<!-- The steps tabs -->
<div class="wpmc-tabs-wrapper">
	<ul class="wpmc-tabs-list wpmc-<?php echo $number_of_steps; ?>-tabs" data-current-title="<?php echo $current_step_title; ?>">
	<?php if ( $show_login_step ) : ?>
		<li class="wpmc-tab-item current wpmc-login" data-step-title="login">
			<div class="wpmc-tab-number"><?php echo $i = $i + 1; ?></div>
			<div class="wpmc-tab-text"><?php echo $options['t_login']; ?></div>
		</li>
	<?php endif; ?>
	<?php
	foreach ( $steps as $_id => $_step ) :
		$class = ( ! $show_login_step && $i == 0 ) ? ' current' : '';
		?>
		<li class="wpmc-tab-item<?php echo $class; ?> wpmc-<?php echo $_id; ?>" data-step-title="<?php echo $_id; ?>">
			<div class="wpmc-tab-number"><?php echo $i = $i + 1; ?></div>
			<div class="wpmc-tab-text"><?php echo $_step['title']; ?></div>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
