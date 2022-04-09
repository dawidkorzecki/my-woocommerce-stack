<?php
/**
 * The tabs for the steps
 *
 * @package WPMultiStepCheckoutPro
 */

defined( 'ABSPATH' ) || exit;


$i                  = 0;
$number_of_steps    = ( $show_login_step ) ? count( $steps ) + 1 : count( $steps );
$template           = ( isset( $options['template'] ) ) ? $options['template'] : 'default';
$clickable_steps    = ( isset( $options['clickable_steps'] ) && $options['clickable_steps'] ) ? 'wpmc-table-clickable': '';
$current_step_title = ( $show_login_step ) ? 'login' : key( array_slice( $steps, 0, 1, true ) );

do_action( 'wpmc_before_tabs' );

?>

<!-- The steps tabs -->
<div class="wpmc-tabs-wrapper wpmc-tabs-wrapper-<?php echo $template; ?> <?php echo $clickable_steps; ?>
">
	<ul class="wpmc-tabs-list wpmc-<?php echo $number_of_steps; ?>-tabs" data-current-title="<?php echo $current_step_title; ?>">
	<?php if ( $show_login_step ) : ?>
		<li class="wpmc-tab-item current wpmc-login" data-step-title="login">
			<div class="wpmc-tab-number"><?php echo $i = $i + 1; ?></div>
			<div class="wpmc-tab-text"><?php echo $options['t_login']; ?></div>
			<?php if ( $template === 'md' ) : ?>
			<div class="wpmc-tab-bar-left"></div>
			<div class="wpmc-tab-bar-right"></div>
			<?php endif; ?>
		</li>
	<?php endif; ?>
	<?php
	$class1 = ( $template === 'md' ) ? ' wpmc-ripple' : '';
	foreach ( $steps as $_id => $_step ) :
		$class2 = ( ! $show_login_step && $i == 0 ) ? $class1 . ' current' : $class1;
		?>
		<li class="wpmc-tab-item<?php echo $class2; ?> wpmc-<?php echo $_id; ?>" data-step-title="<?php echo $_id; ?>">
			<div class="wpmc-tab-number"><?php echo $i = $i + 1; ?></div>
			<div class="wpmc-tab-text"><?php echo $_step['title']; ?></div>
			<?php if ( $template === 'md' ) : ?>
			<div class="wpmc-tab-bar-left"></div>
			<div class="wpmc-tab-bar-right"></div>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
