<?php
/**
 * Sidebar template.
 *
 * @package woodmart
 */

?>

<?php if ( isset( $_GET['step'] ) && 'done' === $_GET['step'] ) : // phpcs:ignore ?>
	<div class="xts-wizard-done-nav">
		<div class="xts-wizard-done-nav-img">
			<img src="<?php echo esc_url( $this->get_image_url( 'check.png' ) ); ?>" alt="check">
		</div>

		<h3>
			<?php esc_html_e( 'Congratulations!', 'woodmart' ); ?>
		</h3>

		<p>
			<?php esc_html_e( 'You have successfully installed our theme.', 'woodmart' ); ?>
		</p>
	</div>

	<img class="xts-wizard-nav-bg-img" src="<?php echo esc_url( $this->get_image_url( 'themes.png' ) ); ?>" alt="themes">
<?php else : ?>
	<ul>
		<?php
		$index        = 0;
		$current_page = 'welcome';

		if ( isset( $_GET['step'] ) && ! empty( $_GET['step'] ) ) { // phpcs:ignore
			$current_page = trim( wp_unslash( $_GET['step'] ) ); // phpcs:ignore
		}

		$current_page_index = array_search( $current_page, array_keys( $this->available_pages ), true );

		?>
		<?php foreach ( $this->available_pages as $slug => $text ) : ?>
			<?php
			$classes = '';
			if ( $index > $current_page_index ) {
				$classes .= ' xts-disabled';
			}

			if ( $this->is_active_page( $slug ) ) {
				$classes .= ' xts-active';
			}

			?>
			<li class="<?php echo esc_attr( $classes ); ?>">
				<a href="<?php echo esc_url( $this->get_page_url( $slug ) ); ?>">
					<?php echo esc_html( $text ); ?>
				</a>
			</li>
			<?php $index++; ?>
		<?php endforeach; ?>
	</ul>

	<?php if ( isset( $_GET['step'] ) && 'welcome' === $_GET['step'] || ! isset( $_GET['step'] ) ) : // phpcs:ignore ?>
		<img class="xts-wizard-nav-bg-img" src="<?php echo esc_url( $this->get_image_url( 'welcome.png' ) ); ?>" alt="welcome themes">
	<?php else : ?>
		<a class="xts-wizard-help" href="https://xtemos.com/forums/forum/woodmart-premium-template/" target="_blank">
			<?php esc_html_e( 'Get help', 'woodmart' ); ?>
		</a>
	<?php endif; ?>
<?php endif; ?>
