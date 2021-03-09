<?php
/**
 * Process incoming Paystack webhooks.
 *
 * @package   Charitable Paystack/Classes/\Charitable\Pro\Paystack\Gateway\Webhook\Processor
 * @author    Eric Daams
 * @copyright Copyright (c) 2021, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * @version   1.0.0
 */

namespace Charitable\Pro\Paystack\Gateway\Webhook;

use Charitable\Pro\Paystack\Gateway\Api;
use Charitable\Webhooks\Processors\SubscriptionProcessor as BaseSubscriptionProcessor;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Charitable\Pro\Paystack\Gateway\Webhook\Processor' ) ) :

	/**
	 * Subscription webhook processor.
	 *
	 * @since 1.0.0
	 */
	class SubscriptionProcessor extends BaseSubscriptionProcessor {

		/**
		 * Process first payment.
		 *
		 * @since  1.0.0
		 *
		 * @return boolean
		 */
		public function process_first_payment() {
			$this->save_gateway_subscription_data();
			$this->update_meta();
			$this->update_logs();

			$this->set_response( __( 'Subscription Webhook: First payment processed', 'charitable-paystack' ) );

			return true;
		}

		/**
		 * Process a renewal.
		 *
		 * @since  1.0.0
		 *
		 * @return boolean
		 */
		public function process_renewal() {
			$donation_id = $this->recurring_donation->create_renewal_donation(
				array(
					'status' => 'charitable-completed'
				)
			);

			$this->donation = charitable_get_donation( $donation_id );

			$this->save_gateway_subscription_data();
			$this->update_meta();
			$this->update_logs();

			$this->recurring_donation->log()->add(
				sprintf(
					__( 'Renewal processed. <a href="%1$s">Donation #%2$d</a>', 'charitable' ),
					get_edit_post_link( $donation_id ),
					$donation_id
				)
			);

			$this->set_response( __( 'Subscription Webhook: Renewal processed', 'charitable' ) );

			return true;
		}
	}

endif;
