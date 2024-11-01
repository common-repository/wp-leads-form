<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://profiles.wordpress.org/itpixelz/
 * @package    Wp_Leads
 * @subpackage Wp_Leads/admin
 * @author     Umar Draz <umar.draz001@gmail.com>
 */
class Wp_Leads_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The nonce key
     *
     * @since    1.0.0
     * @access   private
     * @var      string $nonce_key The nonce key.
     */
    private $nonce_key = 'wpl_nonce_field';

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version )
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-leads-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {


    }


    /**
     * Add post meta box for customer leads in post edit screen
     *
     * @since    1.0.0
     */
    public function add_customer_leads_meta_box()
    {
        add_meta_box(
            'wpl_meta_box', // Unique ID
            __( 'Customer Data', 'wp-leads' ), // Box title
            array( $this, 'meta_box_display_callback' ), // Content callback, must be of type callable
            'customer', // Post type
            'normal', // context
            'high'     // priority
        );
    }


    /**
     * custom meta box html
     * includes checkbox, inputs
     *
     * @since    1.0.0
     */
    public function meta_box_display_callback( $post )
    {

        if ( function_exists( 'wp_nonce_field' ) ) {
            wp_nonce_field( 'wpl_nonce', $this->nonce_key );
        }

        include 'partials/wp-leads-edit-post-display.php';

    }

    /**
     * Save customer post meta data
     *
     * @since    1.0.0
     */
    function save_postdata( $post_id )
    {

        // if this fails, check_admin_referer() will automatically print a "failed" page and die.
        if ( ! isset( $_POST[ $this->nonce_key ] ) || ! check_admin_referer( 'wpl_nonce', $this->nonce_key ) ) {
            return $post_id;
        }


        // Check the user's permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        // Check the if is auotsave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }


        // save data
        if ( ! empty( $_POST['wpl_phone'] ) ) {
            update_post_meta( $post_id, 'wpl_phone', sanitize_text_field( $_POST['wpl_phone'] ) );
        }

        if ( ! empty( $_POST['wpl_email'] ) ) {
            update_post_meta( $post_id, 'wpl_email', sanitize_email( $_POST['wpl_email'] ) );
        }

        if ( ! empty( $_POST['wpl_budget'] ) ) {
            update_post_meta( $post_id, 'wpl_budget', sanitize_text_field( $_POST['wpl_budget'] ) );
        }

        if ( ! empty( $_POST['wpl_message'] ) ) {
            update_post_meta( $post_id, 'wpl_message', sanitize_textarea_field( $_POST['wpl_message'] ) );
        }


    }


    /**
     * change text "title" to "customer" edit screen
     *
     * @since    1.0.0
     */

    function change_title_text( $title )
    {

        $screen = get_current_screen();

        if ( 'customer' == $screen->post_type ) {
            $title = __( 'Customer name', 'wp-leads' );
        }

        return $title;
    }


    /**
     * change text "title" to "customer" in customer list table
     *
     * @since    1.0.0
     */
    function customer_title_table( $posts_columns )
    {

        $posts_columns['title'] = __( 'Customer name', 'wp-leads' );
        return $posts_columns;

    }


}
