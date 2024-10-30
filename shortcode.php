<?php
    if ( ! defined( 'ABSPATH' ) ) exit;
    function INTAPYBTN_payment_shortcode_callback( $atts ){
        global $wpdb;
        $a = shortcode_atts( array(
            'id' => '',
        ), $atts );

        $table              = INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";
        $button             = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %i WHERE id = %s",[$table, intval($a['id'])] ) );
        $payment_amount     = $button->payment_amount;
        $INTAPYBTN_publishable_key    = get_option('INTAPYBTN_publishable_key');
        $INTAPYBTN_wpi_default_amount = get_option('INTAPYBTN_wpi_default_amount');
        $INTAPYBTN_redirect_url       = ($button->redirect_url) ? $button->redirect_url : get_option('INTAPYBTN_redirect_url');
        $live               = false;
        $INTAPYBTN_live_key           = get_option('INTAPYBTN_live_key');

        if ($INTAPYBTN_live_key == 1) {
            $live = true;
        }

        if ($payment_amount == '') {
            echo 'Your shortcode is wrong';
        }
        else {
            if($payment_amount == 0){
                echo '
                <div>
                    <p>Enter Amount (' . esc_html( $button->payment_currency ) . '): </p>
                    <p><input id="isPayAmount-'.esc_attr($button->id).'" type="number" name="payment_amount" value="' . esc_attr( $INTAPYBTN_wpi_default_amount ) . '" min="1" style="border: 1px solid #ccc; padding: 8px;" /></p>
                    <button class="intaSendCustomBtn isPayBtn-'.esc_attr($button->id).'" id="isPayBtn-'.esc_attr($button->id).'" data-card_tarrif="' . esc_attr( $button->card_tarrif ) . '" data-mobile_tarrif="' . esc_attr( $button->mobile_tarrif ) . '" data-amount="' . esc_attr( $payment_amount ) . '" data-currency="' . esc_attr( $button->payment_currency ) . '" data-redirect_url="' . esc_attr( $INTAPYBTN_redirect_url ) . '" data-api_ref="wordpress-pay">' . esc_html( $button->button_label ) . '</button>
                </div>';
            }else{
                echo '<button class="intaSendCustomBtn isPayBtn-'.esc_attr($button->id).'" id="isPayBtn-'.esc_attr($button->id).'" data-card_tarrif="' . esc_attr($button->card_tarrif) . '" data-mobile_tarrif="' . esc_attr($button->mobile_tarrif) . '" data-amount="' . esc_attr($payment_amount) . '" data-currency="' . esc_attr($button->payment_currency) . '" data-redirect_url="' . esc_attr( $INTAPYBTN_redirect_url ) . '" data-api_ref="wordpress-pay">' . esc_html($button->button_label) . '</button>';
            }

        }
    }

    // Hook the shortcode to prevent wpautop
    function INTAPYBTN_prevent_wpautop_for_shortcode( $content ) {
        if ( has_shortcode( $content, 'INTAPYBTN' ) ) {
            remove_filter('the_content', 'wpautop');
        }
        return $content;
    }

    add_filter('the_content', 'INTAPYBTN_prevent_wpautop_for_shortcode', 0);
    add_shortcode('INTAPYBTN', 'INTAPYBTN_payment_shortcode_callback');
?>
