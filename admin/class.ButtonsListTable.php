<?php
    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
        require_once(ABSPATH . 'wp-admin/includes/template.php');

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
?>
