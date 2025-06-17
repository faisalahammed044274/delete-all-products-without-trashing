<?php
/**
 * Plugin Name: Delete All WooCommerce Products
 * Description: Adds a sidebar menu to delete all WooCommerce products in one click.
 * Version: 1.1
 * Author: Faisal's AI Assistant
 */

if (!defined('ABSPATH')) exit; // No direct access

// Create a sidebar menu
add_action('admin_menu', function () {
    add_menu_page(
        'Delete All Products',           // Page title
        'Delete Products',               // Menu title
        'manage_woocommerce',            // Capability
        'delete-all-products',           // Menu slug
        'dap_render_admin_page',         // Callback function
        'dashicons-trash',               // Icon
        60                               // Position
    );
});

// Render the admin page
function dap_render_admin_page() {
    if (!current_user_can('manage_woocommerce')) {
        wp_die('You do not have permission to access this page.');
    }

    if (isset($_POST['dap_delete_all']) && check_admin_referer('dap_delete_all_nonce')) {
        global $wpdb;

        // Get all product IDs
        $product_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product'");

        if (!empty($product_ids)) {
            foreach ($product_ids as $product_id) {
                wp_delete_post($product_id, true);
            }

            echo '<div class="notice notice-success is-dismissible"><p><strong>Success:</strong> All products have been deleted.</p></div>';
        } else {
            echo '<div class="notice notice-warning is-dismissible"><p>No products found to delete.</p></div>';
        }
    }

    echo '<div class="wrap">';
    echo '<h1>⚠️ Delete All WooCommerce Products</h1>';
    echo '<form method="POST">';
    wp_nonce_field('dap_delete_all_nonce');
    echo '<p><strong>Warning:</strong> Clicking the button below will permanently delete all products from your store. This cannot be undone.</p>';
    echo '<p><input type="submit" name="dap_delete_all" class="button button-primary" value="🔥 Delete All Products" onclick="return confirm(\'Are you sure? This will delete EVERYTHING!\');"></p>';
    echo '</form>';
    echo '</div>';
}
