<?php
    if ( ! defined( 'ABSPATH' ) ) exit;

    if (!class_exists('INTAPYBTN_ButtonsListTable')) {
        function INTAPYBTN_usort_reorder($a, $b){
            $orderby = (!empty(sanitize_key($_REQUEST['orderby']))) ? sanitize_key($_REQUEST['orderby']) : 'shortcode';
            $order = (!empty(sanitize_key($_REQUEST['order']))) ? sanitize_key($_REQUEST['order']) : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        class INTAPYBTN_ButtonsListTable extends WP_List_Table{
            function __construct(){
                global $status, $page;
                //Set parent defaults
                parent::__construct(array(
                    'singular'  => 'button',
                    'plural'    => 'buttons',
                    'ajax'      => false
                ));
            }

            function column_default($item, $column_name){
                switch ($column_name) {
                    case 'shortcode':
                        return $item[$column_name];
                    case 'label':
                        return $item[$column_name];
                    case 'currency':
                        return $item[$column_name];
                    case 'amount':
                        return $item[$column_name];
                    // case 'method':
                    //     return $item[$column_name];
                    case 'card_tarrif':
                        return $item[$column_name];
                    case 'mobile_tarrif':
                        return $item[$column_name];
                    case 'redirect_url':
                        return $item[$column_name];
                    case 'edit':
                        $edit_url = add_query_arg(array(
                            'page' => 'intasend_edit_button',
                            'button_id' => $item['ID']
                        ), admin_url('admin.php'));
                        return sprintf('<a href="%s">Edit</a>', esc_url($edit_url));
                    default:
                        return $item;
                }
            }

            function column_cb($item){
                return sprintf(
                    '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                    /*$1%s*/
                    $this->_args['singular'],
                    /*$2%s*/
                    $item['ID']
                );
            }

            function get_columns(){
                $columns = array(
                    'cb'            => '<input type="checkbox" />',
                    'shortcode'     => 'ShortCode',
                    'label'         => 'Button Label',
                    'currency'      => 'Currency',
                    'amount'        => 'Amount',
                    // 'method'        => 'Method',
                    'card_tarrif'   => 'Card Tarrif',
                    'mobile_tarrif' => 'Mobile Tarrif',
                    'redirect_url' => 'Redirect URL',
                    'edit'          => 'Edit' // New edit column
                );
                return $columns;
            }

            function get_sortable_columns(){
                $sortable_columns = array(
                    'shortcode'     => array('shortcode', false),
                    'label'         => array('label', false),
                    'currency'      => array('currency', false),
                    'amount'        => array('amount', false),
                    // 'method'        => array('method', false),
                    'card_tarrif'   => array('card_tarrif', false),
                    'mobile_tarrif' => array('mobile_tarrif', false)
                );
                return $sortable_columns;
            }

            function get_bulk_actions(){
                $actions = array(
                    'delete'    => 'Delete'
                );
                return $actions;
            }

            function process_bulk_action(){

                //Detect when a bulk action is being triggered...
                if ('delete' === $this->current_action()) {
                    // Check if the nonce field is set and verify the nonce
                    if (isset($_REQUEST['INTAPYBTN_nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['INTAPYBTN_nonce']), 'INTAPYBTN_form_action_unique')) {

                        if (isset($_POST['button']) && !empty($_POST['button'])) {

                            // Unslash the POST data before sanitizing
                            $button_data = wp_unslash($_POST['button']);

                            // Ensure it's sanitized before processing
                            $sanitized_buttons = is_array($button_data) ? array_map('sanitize_text_field', $button_data) : sanitize_text_field($button_data);
                            // Nonce is valid, process the form
                            foreach ($sanitized_buttons as $sanitized_fID) {
                                // Sanitize the input
                                // $sanitized_fID = sanitize_text_field($fID);
                                global $wpdb;
                                $table = INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";
                                $wpdb->delete($table, array('id' => $sanitized_fID));
                            }
                            echo '<div class="notice notice-success is-dismissible"><p>Buttons deleted successfully!</p></div>';
                        }
                    }
                    else {
                        // Nonce is invalid, handle the error
                        wp_die('Nonce verification failed!');
                    }
                }
            }

            function prepare_items(){
                global $wpdb; //This is used only if making any database queries
                $per_page   = 10;
                $columns    = $this->get_columns();
                $table_data = $hidden  = array();
                $sortable   = $this->get_sortable_columns();
                $table      = INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";

                $this->_column_headers = array($columns, $hidden, $sortable);
                $this->process_bulk_action();
                $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i;",[$table]));

                foreach ($records as $record) {
                    if (isset($record->payment_amount) && isset($record->payment_currency)) {
                        $table_data[] = array(
                            "ID"            => $record->id,
                            "shortcode"     => '<b><h4>[INTAPYBTN id=' . $record->id . ']</h4></b>',
                            "label"         => $record->button_label,
                            "currency"      => $record->payment_currency,
                            "amount"        => $record->payment_amount,
                            // "method"        => $record->method,
                            "card_tarrif"   => $record->card_tarrif,
                            "mobile_tarrif" => $record->mobile_tarrif,
                            "redirect_url" => $record->redirect_url
                        );
                    }
                }

                $data = $table_data;

                // function INTAPYBTN_usort_reorder($a, $b){
                //     $orderby = (!empty(sanitize_key($_REQUEST['orderby']))) ? sanitize_key($_REQUEST['orderby']) : 'shortcode';
                //     $order = (!empty(sanitize_key($_REQUEST['order']))) ? sanitize_key($_REQUEST['order']) : 'asc';
                //     $result = strcmp($a[$orderby], $b[$orderby]);
                //     return ($order === 'asc') ? $result : -$result;
                // }

                usort($data, 'INTAPYBTN_usort_reorder');
                $current_page   = $this->get_pagenum();
                $total_items    = count($data);
                $data           = array_slice($data, (($current_page - 1) * $per_page), $per_page);
                $this->items    = $data;

                $this->set_pagination_args(array(
                    'total_items' => $total_items,
                    'per_page'    => $per_page,
                    'total_pages' => ceil($total_items / $per_page)
                ));
            }
        }
    }

    function intasend_register_settings_callback(){
        add_option('INTAPYBTN_publishable_key', '');
        add_option('INTAPYBTN_wpi_default_amount', '10');
        add_option('INTAPYBTN_redirect_url', '');
        register_setting('intasend_options_group', 'INTAPYBTN_publishable_key');
        register_setting('intasend_options_group', 'INTAPYBTN_wpi_default_amount');
        register_setting('intasend_options_group', 'INTAPYBTN_redirect_url');
        register_setting('intasend_options_group', 'INTAPYBTN_live_key');
    }
    add_action('admin_init', 'intasend_register_settings_callback');

    function INTAPYBTN_tt_add_menu_items_callback(){
        add_menu_page(
            'IntaSend Payment Buttons',
            'IntaSend Payment',
            'activate_plugins',
            'intasend_shortcode_list',
            'INTAPYBTN_tt_render_list_page_callback'
        );

        add_submenu_page(
            'intasend_shortcode_list',
            'IntaSend Payment Buttons',
            'Add New Shortcode',
            'activate_plugins',
            'intasend_add_button',
            'INTAPYBTN_wps_get_button_form_callback'
        );

        add_submenu_page(
            'intasend_shortcode_list',
            'IntaSend Payment Buttons',
            'Settings',
            'activate_plugins',
            'intasend_settings',
            'INTAPYBTN_wps_get_settings_form_callback'
        );
    }
    add_action('admin_menu', 'INTAPYBTN_tt_add_menu_items_callback');

    function INTAPYBTN_wps_get_settings_form_callback(){
        if( !get_option('INTAPYBTN_publishable_key')){
            echo '<div class="notice notice-error is-dismissible"><p>Please add your IntaSend Public Key.</p></div>';
        }
        if( !get_option('INTAPYBTN_redirect_url')){
            echo '<div class="notice notice-error is-dismissible"><p>Please add a Redirect Url</p></div>';
        }
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
            // wp_redirect('admin.php?page=intasend_settings');
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
        }
        include('settings_form.php');
    }

    function INTAPYBTN_wps_get_button_form_callback(){
        if( !get_option('INTAPYBTN_publishable_key') || !get_option('INTAPYBTN_redirect_url') ){
            wp_redirect('admin.php?page=intasend_settings');
            exit;
        }
        include('button_form.php');
    }

    function INTAPYBTN_tt_render_list_page_callback(){

        if( !get_option('INTAPYBTN_publishable_key') || !get_option('INTAPYBTN_redirect_url') ){
            wp_redirect('admin.php?page=intasend_settings');
            exit;
        }

        $buttonsListTable = new INTAPYBTN_ButtonsListTable();
        $buttonsListTable->prepare_items();
    ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br /></div>
            <h2>Payment Buttons</h2>
            <form id="buttons-filter" method="post">
                <?php wp_nonce_field('INTAPYBTN_form_action_unique', 'INTAPYBTN_nonce'); ?>
                <input type="hidden" name="page" value="<?php echo esc_attr(sanitize_key($_REQUEST['page'])) ?>" />
                <?php $buttonsListTable->display() ?>
            </form>
        </div>
    <?php
    }

    add_action('admin_menu', 'INTAPYBTN_register_button_edit_page');
    function INTAPYBTN_register_button_edit_page(){
        add_submenu_page(
            null, // This hides the page from the menu
            'Edit Button',
            'Edit Button',
            'manage_options',
            'intasend_edit_button',
            'INTAPYBTN_display_button_edit_form'
        );
    }

    function INTAPYBTN_display_button_edit_form(){
        global $wpdb;
    
        if (!isset($_GET['button_id'])) {
            echo '<div class="error"><p>No button ID provided!</p></div>';
            return;
        }
    
        // Sanitize the button ID
        $button_id = absint($_GET['button_id']);
        $table = INTAPYBTN_TABLE_PREFIX . "intasend_payment_codes_v1";
    
        // Fetch button data
        $button = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $button_id), ARRAY_A);
    
        if (!$button) {
            echo '<div class="error"><p>Button not found!</p></div>';
            return;
        }
    
        // Display the form with the current values
        ?>
        <div class="wrap">
            <h2>Edit Button</h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('INTAPYBTN_form_action_unique', 'INTAPYBTN_nonce'); ?>
                <input type="hidden" name="action" value="update_button" />
                <input type="hidden" name="button_id" value="<?php echo esc_attr($button_id); ?>" />
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="button_label">Button Label</label></th>
                        <td><input type="text" name="button_label" value="<?php echo esc_attr($button['button_label']); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="payment_currency">Currency</label></th>
                        <td>
                            <select name="payment_currency" value="<?php echo esc_attr($button['payment_currency']); ?>" class="regular-text">
                                <option value="USD">USD</option>
                                <option value="KES">KES</option>
                                <option value="GBP">GBP</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="payment_amount">Amount</label>
                        </th>
                        <td>
                            <input value="<?php echo esc_attr($button['payment_amount']); ?>" type="text" class="regular-text" id="payment_amount" placeholder="Enter amount" name="payment_amount">
                        </td>
                    </tr>
                    <tr>
                        <th> <label for="card_tarrif">Card-tarif</label>
                        </th>
                        <td>

                            <select name="card_tarrif" value="<?php echo esc_attr($button['card_tarrif']); ?>" class="regular-text">
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
                            <select name="mobile_tarrif" value="<?php echo esc_attr($button['mobile_tarrif']); ?>" class="regular-text">
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
                            <input type="text" class="regular-text" value="<?php echo esc_attr($button['redirect_url']); ?>" id="redirect_url" placeholder="Enter redirect url" name="redirect_url">
                        </td>
                    </tr>
                    <!-- Add more fields as necessary -->
                </table>
    
                <?php submit_button('Save Changes'); ?>
            </form>
        </div>
        <?php
    }
    
    add_action('admin_post_update_button', 'INTAPYBTN_process_button_edit');
    function INTAPYBTN_process_button_edit(){
        if (isset($_POST['INTAPYBTN_nonce'])) {
            if (isset($_POST['INTAPYBTN_nonce']) && wp_verify_nonce(sanitize_key($_POST['INTAPYBTN_nonce']), 'INTAPYBTN_form_action_unique') ) {
                if (!isset($_POST['button_id'])) {
                    wp_die('Invalid request.');
                }

                global $wpdb;
                $button_id  	= trim(sanitize_key($_POST['button_id']));
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

                    // Update the database
                    $wpdb->update($table, $data, array('id' => $button_id));

                    $arrMsg[] = "<div class='notice notice-success is-dismissible'><p>Button updated succesfully.</p></div>";
                } else {
                    $arrMsg[] = "<div class='notice notice-error is-dismissible'><p>Please set a valid amount.</p></div>";
                }

                wp_redirect(add_query_arg(array('page' => 'intasend_shortcode_list'), admin_url('admin.php')));
                exit;
            } else {
                // Nonce is invalid, handle the error
                wp_die('Nonce verification failed!');
            }
        }
    }

?>