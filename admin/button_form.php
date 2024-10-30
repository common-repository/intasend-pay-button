<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$arrMsg = array();

// Check if the nonce field is set and verify the nonce
if (isset($_POST['INTAPYBTN_nonce'])) {
	if (isset($_POST['INTAPYBTN_nonce']) && wp_verify_nonce(sanitize_key($_POST['INTAPYBTN_nonce']), 'INTAPYBTN_form_action_unique') ) {
		// Nonce is valid, process the form
		if (isset($_POST['payment_amount']) && isset($_POST['payment_currency']) && isset($_POST['button_label'])) {
			global $wpdb;
			$payment_amount  	= trim(sanitize_key($_POST['payment_amount']));
			$button_label  		= sanitize_text_field(wp_unslash($_POST['button_label']));
			$payment_currency  	= strtoupper(trim(sanitize_key($_POST['payment_currency'])));
			// $method  			= trim($_POST['method']);
			$card_tarrif  		= strtoupper(trim(sanitize_key($_POST['card_tarrif'])));
			$mobile_tarrif  	= strtoupper(trim(sanitize_key($_POST['mobile_tarrif'])));
			$redirect_url  	    = sanitize_url($_POST['redirect_url']);
			$payment_amount		= ($payment_amount) ? $payment_amount : 0;
			if ($payment_amount >= 0) {
				$table   =   INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";
				$data    =   array(
					'payment_amount' 	=> $payment_amount,
					'button_label' 		=> $button_label,
					'payment_currency' 	=> $payment_currency,
					// 'method' 			=> $method,
					'card_tarrif' 		=> $card_tarrif,
					'mobile_tarrif' 	=> $mobile_tarrif,
					'redirect_url'      => $redirect_url,
					'is_active' 		=> '1'
				);
				$wpdb->insert($table, $data);
				$arrMsg[] = "<div class='notice notice-success is-dismissible'><p>Button created succesfully.</p></div>";
			} else {
				$arrMsg[] = "<div class='notice notice-error is-dismissible'><p>Please set a valid amount.</p></div>";
			}
		}
	} else {
		// Nonce is invalid, handle the error
		wp_die('Nonce verification failed!');
	}
}
?>
<div class="wrap">
	<h2 class="wp-heading-inline">Add Intasend Shortcode</h2>
	<?php if (is_array($arrMsg) && count($arrMsg)) { ?>
		<?php foreach ($arrMsg as $msg) { ?>
			<?php echo wp_kses_post($msg); ?><br />
		<?php } ?>
	<?php } ?>
	<form action="" class="was-validated" method="POST">
		<?php wp_nonce_field('INTAPYBTN_form_action_unique', 'INTAPYBTN_nonce'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="button_label">Button Label</label>
					</th>
					<td>
						<input type="text" class="regular-text" id="button_label" placeholder="Payment button label" value="Pay Now" name="button_label" required>
					</td>
				</tr>
				<tr>
					<th>
						<label for="payment_amount">Amount</label>
					</th>
					<td>
						<input type="text" class="regular-text" id="payment_amount" placeholder="Enter amount" name="payment_amount">
					</td>
				</tr>
				<tr>
					<th>
						<label for="payment_currency">Currency</label>
					</th>
					<td>
						<select name="payment_currency" class="regular-text">
							<option value="USD">USD</option>
							<option value="KES">KES</option>
							<option value="GBP">GBP</option>
							<option value="EUR">EUR</option>
						</select>
					</td>
				</tr>
				<!-- <tr>
					<th>
						<label for="method">Method</label>
					</th>
					<td>

						<select name="method" class="regular-text">
							<option>ALL</option>
							<option value="M-PESA">M-Pesa</option>
							<option value="CARD-PAYMENT">Card Payment</option>
						</select>
					</td>
				</tr> -->
				<tr>
					<th> <label for="card_tarrif">Card-tarif</label>
					</th>
					<td>

						<select name="card_tarrif" class="regular-text">
							<option value="BUSINESS-PAYS">Business Pays</option>
							<option value="CUSTOMER-PAYS">Customer Pays</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="mobile_tarrif">Mobile-tarif</label>
					</th>
					<td>
						<select name="mobile_tarrif" class="regular-text">
							<option value="BUSINESS-PAYS">Business Pays</option>
							<option value="CUSTOMER-PAYS">Customer Pays</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label for="redirect_url">Redirect URL</label>
					</th>
					<td>
						<input type="text" class="regular-text" id="redirect_url" placeholder="Enter redirect url" name="redirect_url">
					</td>
				</tr>
			</tbody>
		</table>
		<div class="submit">
			<button type="submit" class="button button-primary" name="submit">Create Button</button>
		</div>
	</form>
</div>