<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
   <h2 class="wp-heading-inline">API Settings</h2>

   <form method="post" action="options.php">
      <?php
         settings_fields('intasend_options_group');
         do_settings_sections('intasend_settings');
      ?>
      <table class="form-table">
         <tr valign="top">
            <th scope="row"><label for="INTAPYBTN_publishable_key">Publishable key</label></th>
            <td><input class="regular-text" type="text" id="INTAPYBTN_publishable_key" name="INTAPYBTN_publishable_key" value="<?php echo esc_attr(get_option('INTAPYBTN_publishable_key')); ?>" /></td>
         </tr>

         <tr valign="top">
            <th scope="row"><label for="INTAPYBTN_wpi_default_amount">Default Amount</label></th>
            <td><input class="regular-text" type="number" id="INTAPYBTN_wpi_default_amount" name="INTAPYBTN_wpi_default_amount" value="<?php echo esc_attr(get_option('INTAPYBTN_wpi_default_amount')); ?>" /></td>
         </tr>

         <tr valign="top">
            <th scope="row"><label for="INTAPYBTN_live_key">Live</label></th>
            <td><input type="checkbox" id="INTAPYBTN_live_key" name="INTAPYBTN_live_key" value="1" <?php checked(esc_attr(get_option('INTAPYBTN_live_key')), 1); ?> /></td>
         </tr>
         <tr valign="top">
            <th scope="row"><label for="INTAPYBTN_redirect_url">Redirect Url</label></th>
            <td><input class="regular-text" type="text" id="INTAPYBTN_redirect_url" name="INTAPYBTN_redirect_url" value="<?php echo esc_attr(get_option('INTAPYBTN_redirect_url')); ?>" /></td>
         </tr>
      </table>
      <div class="submit">
      <?php submit_button();?>
		</div>
   </form>
</div>