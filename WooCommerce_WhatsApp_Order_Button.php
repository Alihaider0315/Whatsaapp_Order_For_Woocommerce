<?php
/*
Plugin Name: WooCommerce WhatsApp Order Button
Description: Adds a WhatsApp order button to WooCommerce product pages with dynamic settings for the WhatsApp number and message.
Version: 1.0
Author: Muhammad Ali Haider Khan
*/

// Add settings page
add_action('admin_menu', 'whatsapp_order_button_menu');
function whatsapp_order_button_menu() {
    add_options_page('WhatsApp Order Button Settings', 'WhatsApp Order Button', 'manage_options', 'whatsapp-order-button-settings', 'whatsapp_order_button_settings_page');
}

// Register settings
add_action('admin_init', 'whatsapp_order_button_settings');
function whatsapp_order_button_settings() {
    register_setting('whatsapp_order_button_options_group', 'whatsapp_number');
    register_setting('whatsapp_order_button_options_group', 'whatsapp_message');
}

// Display settings page
function whatsapp_order_button_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Order Button Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('whatsapp_order_button_options_group'); ?>
            <?php do_settings_sections('whatsapp_order_button_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Number</th>
                    <td><input type="text" name="whatsapp_number" value="<?php echo esc_attr(get_option('whatsapp_number')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">WhatsApp Message</th>
                    <td><textarea name="whatsapp_message" rows="5" cols="50"><?php echo esc_textarea(get_option('whatsapp_message')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Add WhatsApp order button to WooCommerce product pages
add_action('woocommerce_after_add_to_cart_button', 'custom_whatsapp_order_button', 20);
function custom_whatsapp_order_button() {
    global $product;

    // Get dynamic settings
    $whatsapp_number = get_option('whatsapp_number');
    $whatsapp_message = get_option('whatsapp_message');
    
    // Get product details
    $product_name = $product->get_name();
    $product_price = $product->get_price();
    $formatted_price = wc_get_price_to_display($product); // Get price without HTML
    $product_url = get_permalink($product->get_id());
    
    // Replace placeholders in message
    $whatsapp_message = str_replace(
        array('{product_name}', '{price}', '{link}'),
        array($product_name, $formatted_price, $product_url),
        $whatsapp_message
    );

    // Encode message
    $whatsapp_message = urlencode($whatsapp_message);
    
    // WhatsApp URL
    $whatsapp_url = "https://api.whatsapp.com/send?phone=$whatsapp_number&text=$whatsapp_message";
    
    // Button HTML
    echo '<a href="' . $whatsapp_url . '" target="_blank" class="button alt whatsapp-order-button">Order via WhatsApp</a>';
}
