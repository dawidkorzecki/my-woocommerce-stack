<?php
/**
 * Popup Data.
 *
 * @package WPDesk\FS\Onboarding
 */

namespace WPDesk\FS\Onboarding\TableRate;

/**
 * Class PopupData
 *
 * @package WPDesk\FS\Onboarding
 */
class PopupData {
	/**
	 * @return array[]
	 */
	public function get_popups(): array {
		return [
			$this->get_popup_data_step_0(),
			$this->get_popup_data_step_1(),
			$this->get_popup_data_step_2(),
			$this->get_popup_data_step_3(),
			$this->get_popup_data_step_4(),
		];
	}

	/**
	 * @return array
	 */
	private function get_popup_data_step_0(): array {
		return [
			'id'      => 'step_0',
			'logo'    => true,
			'title'   => null,
			'step'    => null,
			'show'    => false,
			'image'   => 'start@2x.png',
			'heading' => __( 'Find out how the rules table works', 'flexible-shipping' ),
			'text'    => [
				sprintf(
				// Translators: open and close strong tag.
					__( '%1$sWe do our best to develop our plugins in response to your expectations. Not only do we want them to fit your needs but also offer a vast array of features and be easy to use at the same time. Explore the possibilities and learn how to use the rules table in just 4 steps!%2$s', 'flexible-shipping' ),
					'<strong>',
					'</strong>'
				),
			],
			'buttons' => [
				[
					'label'   => __( 'No, thank you.', 'flexible-shipping' ),
					'action'  => 'cancel',
					'classes' => 'btn-link',
				],
				[
					'label'   => __( 'Start the tutorial', 'flexible-shipping' ),
					'popup'   => 'step_1',
					'classes' => 'btn-success',
				],
			],
		];
	}

	/**
	 * @return array
	 */
	private function get_popup_data_step_1(): array {
		return [
			'id'      => 'step_1',
			'logo'    => false,
			'title'   => null,
			'step'    => 1,
			'show'    => false,
			'image'   => 'steps/' . $this->get_locale() . '/step-1.gif',
			'heading' => __( 'Choose what the rule should be based on', 'flexible-shipping' ),
			'text'    => [
				sprintf(
				// Translators: open and close strong tag.
					__( '%1$sIn the \'When\' column select the condition which the rule you are about to add will be based on and calculated.%2$s', 'flexible-shipping' ),
					'<strong>',
					'</strong>'
				),
				sprintf(
				// Translators: open and close strong tag.
					__( '%1$sExample 1:%2$s If the shipping cost should be calculated based on the weight of the products added to the cart — select %3$sWeight%4$s, if&nbsp;based on price — similarly select %5$sPrice%6$s.', 'flexible-shipping' ),
					'<strong class="highlight">',
					'</strong>',
					'<strong>',
					'</strong>',
					'<strong>',
					'</strong>'
				),
				sprintf(
				// Translators: open and close strong tag.
					__( '%1$sExample 2:%2$s If the shipping cost should be fixed for good — select %3$sAlways%4$s.', 'flexible-shipping' ),
					'<strong class="highlight">',
					'</strong>',
					'<strong>',
					'</strong>'
				),
			],
			'buttons' => [
				[
					'label'   => __( 'Previous step', 'flexible-shipping' ),
					'popup'   => 'step_0',
					'classes' => 'btn-link',
				],
				[
					'label'   => __( 'Next step', 'flexible-shipping' ),
					'popup'   => 'step_2',
					'classes' => 'btn-success',
				],
			],
		];
	}

	/**
	 * @return array
	 */
	private function get_popup_data_step_2(): array {
		return [
			'id'      => 'step_2',
			'logo'    => false,
			'title'   => null,
			'step'    => 2,
			'show'    => false,
			'image'   => 'steps/' . $this->get_locale() . '/step-2.gif',
			'heading' => __( 'Define the rule’s range', 'flexible-shipping' ),
			'text'    => [
				__( 'Enter the minimum and maximum value for the selected condition to define the range when the rule will be applied.', 'flexible-shipping' ),
				sprintf(
				// Translators: open and close strong tag.
					__( '%1$sExample 1:%2$s If you want a particular shipping cost to be charged when the order’s total weight is between 1 kg and 5 kg - select %3$sWhen:%4$s Weight %5$sis from:%6$s 1 kg %7$sto:%8$s 5 kg.', 'flexible-shipping' ),
					'<strong class="highlight">',
					'</strong>',
					'<strong>',
					'</strong>',
					'<strong>',
					'</strong>',
					'<strong>',
					'</strong>'
				),
				sprintf(
				// Translators: open and close strong tag.
					__( '%1$sExample 2:%2$s If you want a particular shipping cost to be charged only when the order’s price exceeds $100, select %3$sWhen:%4$s Price %5$sis from:%6$s $100.', 'flexible-shipping' ),
					'<strong class="highlight">',
					'</strong>',
					'<strong>',
					'</strong>',
					'<strong>',
					'</strong>'
				),
			],
			'buttons' => [
				[
					'label'   => __( 'Previous step', 'flexible-shipping' ),
					'popup'   => 'step_1',
					'classes' => 'btn-link',
				],
				[
					'label'   => __( 'Next step', 'flexible-shipping' ),
					'popup'   => 'step_3',
					'classes' => 'btn-success',
				],
			],
		];
	}

	/**
	 * @return array
	 */
	private function get_popup_data_step_3(): array {
		return [
			'id'      => 'step_3',
			'logo'    => false,
			'title'   => null,
			'step'    => 3,
			'show'    => false,
			'image'   => 'steps/' . $this->get_locale() . '/step-3.gif',
			'heading' => __( 'Determine the shipping cost', 'flexible-shipping' ),
			'text'    => [
				__( 'Enter the shipping cost, which will be added to the order’s price when the condition you’ve set in the previous step is met.', 'flexible-shipping' ),
				sprintf(
				// Translators: open and close strong tag.
					__( '%1$sExample 1:%2$s If the cost of the shipping method you are currently configuring should be $12, enter %3$sCost is:%4$s 12.', 'flexible-shipping' ),
					'<strong class="highlight">',
					'</strong>',
					'<strong>',
					'</strong>'
				),
			],
			'buttons' => [
				[
					'label'   => __( 'Previous step', 'flexible-shipping' ),
					'popup'   => 'step_2',
					'classes' => 'btn-link',
				],
				[
					'label'   => __( 'Next step', 'flexible-shipping' ),
					'popup'   => 'step_4',
					'classes' => 'btn-success',
				],
			],
		];
	}

	/**
	 * @return array
	 */
	private function get_popup_data_step_4(): array {
		return [
			'id'      => 'step_4',
			'logo'    => false,
			'title'   => null,
			'step'    => 4,
			'show'    => false,
			'image'   => 'steps/' . $this->get_locale() . '/step-4.gif',
			'heading' => __( 'Add more and combine the rules!', 'flexible-shipping' ),
			'text'    => [
				sprintf(
				// Translators: open and close strong tag.
					__( 'Configure even the most advanced shipping scenarios by adding and combining the shipping cost calculation rules. Precisely define how the shipping cost should be calculated or import and adapt one of our %1$sready-to-use scenarios%2$s to your needs. Read the %3$sFlexible Shipping plugin documentation%4$s and discover its endless possibilities!', 'flexible-shipping' ),
					sprintf(
						'<a href="%s" target="_blank">',
						// Translators: open and close strong tag.
						esc_url( __( 'https://wpde.sk/onboarding-sc', 'flexible-shipping' ) )
					),
					'</a>',
					sprintf(
						'<a href="%s" target="_blank">',
						// Translators: open and close strong tag.
						esc_url( __( 'https://wpde.sk/onboarding-docs', 'flexible-shipping' ) )
					),
					'</a>'
				),
			],
			'buttons' => [
				[
					'label'   => __( 'Previous step', 'flexible-shipping' ),
					'popup'   => 'step_3',
					'classes' => 'btn-link',
				],
				[
					'label'   => __( 'Proceed to adding the rules', 'flexible-shipping' ),
					'action'  => 'finish',
					'classes' => 'btn-success',
				],
			],
		];
	}

	/**
	 * @return string
	 */
	private function get_locale(): string {
		$locale = get_user_locale();

		if ( 'pl_PL' === $locale ) {
			return $locale;
		}

		return 'en_US';
	}
}
