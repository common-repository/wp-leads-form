<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       https://profiles.wordpress.org/itpixelz/
 * @package    Wp_Leads
 * @subpackage Wp_Leads/public
 * @author     Umar Draz <umar.draz001@gmail.com>
 */
class Wp_Leads_Public
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
    private $nonce_key = 'wpl_leads_nonce_field';

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
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version )
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-leads-public.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-leads-public.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'wp_leads_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'error_msg' => __( 'Server error!', 'wp-leads' ) ) );
    }


    /**
     * Register shortcode
     *
     * @since    1.0.0
     */
    public function leads_form_shortcode( $atts )
    {

        $lead_form_html = '';

        // Attributes
        $default_atts = array(
            'label-name' => __( 'Name (required)', 'wp-leads' ),
            'label-phone' => __( 'Phone Number', 'wp-leads' ),
            'label-email' => __( 'Email Address (required)', 'wp-leads' ),
            'label-budget' => __( 'Desired Budget', 'wp-leads' ),
            'label-message' => __( 'Message', 'wp-leads' ),
            'max-name' => 70,
            'max-phone' => 30,
            'max-email' => 50,
            'rows-message' => 5,
            'cols-message' => 50,
        );

        $atts = shortcode_atts(
            $default_atts,
            $atts
        );



        $nonce = '';

        if ( function_exists( 'wp_nonce_field' ) ) {
            $nonce = wp_nonce_field( 'wpl_nonce', $this->nonce_key, true, false );
        }

        $lead_form_html .= '
<!-- wp-leads-form start -->
                    <div class="wpl_container">
                    <form onsubmit="return wp_leads_form_submit();" id="wpl_lead_form" action="" method="post">
                    ' . $nonce . '
                    
                        <!-- Name -->
                        <div class="wpl_fieldset">
                            <label for="wpl_name" class="wpl_name">' . esc_html($atts['label-name']) . '</label>
                            <input type="text" name="wpl_name" id="wpl_name" class="wpl_text_input"  required="required" maxlength="'. $this->get_numeric($atts['max-name'], $default_atts['max-name']) .'"/>
                        </div>
                        
                        <!-- Phone number -->
                        <div class="wpl_fieldset">
                            <label for="wpl_phone" class="wpl_label">' . esc_html($atts['label-phone']) . '</label>
                            <input type="tel" name="wpl_phone" id="wpl_phone" class="wpl_text_input" maxlength="'. $this->get_numeric($atts['max-phone'], $default_atts['max-phone']) .'"/>
                        </div>
                    
                        <!-- Email address -->
                        <div class="wpl_fieldset">
                            <label for="wpl_email" class="wpl_label">' . esc_html($atts['label-email']) . '</label>
                            <input type="email" name="wpl_email" id="wpl_email" class="wpl_text_input" required="required" maxlength="'. $this->get_numeric($atts['max-email'], $default_atts['max-email']) .'"/>
                        </div>
                    
                        <!-- Budget -->
                        <div class="wpl_fieldset">
                            <label for="wpl_budget" class="wpl_label">' . esc_html($atts['label-budget']) . '</label>
                    
                            <select name="wpl_budget" id="wpl_budget" class="wpl_select">
                                <option value="">' . __( '-- select --', 'wp-leads' ) . '</option>
                                <option value="100-500">' . __( '$100$ - $500', 'wp-leads' ) . '</option>
                                <option value="500-1000">' . __( '$500$ - $1000', 'wp-leads' ) . '</option>
                                <option value="1000-5000">' . __( '$1000$ - $5000', 'wp-leads' ) . '</option>
                                <option value="5000+">' . __( '$5000+', 'wp-leads' ) . '</option>
                            </select>
                    
                        </div>
                    
                        <!-- Message -->
                        <div class="wpl_fieldset">
                            <label for="wpl_message" class="wpl_label">' . esc_html($atts['label-message']) . '</label>
                            <textarea rows="'. $this->get_numeric($atts['rows-message'], $default_atts['rows-message']) .'" cols="'. $this->get_numeric($atts['cols-message'], $default_atts['cols-message']) .'" name="wpl_message" id="wpl_message" class="wpl_textarea"></textarea>
                        </div>
                        
                        
                        <!-- submit button -->
                        <div class="wpl_fieldset">
                        <button class="wpl_submit" id="wpl_submit" type="submit">' . __( 'Submit', 'wp-leads' ) . '</button>
                        </div>
                        
                        <!-- response message -->
                        <div class="wpl_alert" id="wpl_response" style="display: none;"><span></span></div>
                        
                        </form>
                    
                    </div>
                    
                    <!-- wp-leads-form end -->
                ';


        return $lead_form_html;

    }



    /**
     * validate numbers for shortcode attribute, if invalid use default
     *
     * @since    1.0.0
     */
    public function get_numeric($number, $default = 0){

        if(is_numeric($number) && $number > 0 ){
            return $number;
        }

        return $default;
    }


    /**
     * Ajax lead form save handler
     *
     * @since    1.0.0
     */
    function wp_lead_save_handler()
    {

        header( 'Content-Type: application/json' );
        $response = array( 'status' => 0, 'msg' => '' );

         // if this fails, check_admin_referer() will automatically print a "failed" page and die.
        if ( ! isset( $_POST[ $this->nonce_key ] ) || ! check_ajax_referer( 'wpl_nonce', $this->nonce_key ) ) {
            $response['msg'] = __( 'Security check issue', 'wp-leads' );
            echo json_encode( $response );
            wp_die();
        }

        if ( ! empty( $_POST['wpl_name'] ) ) {

            // Gather post data.
            $lead_data = array(
                'post_title' => sanitize_text_field( $_POST['wpl_name'] ),
                'post_content' => '',
                'post_type' => 'customer',
                'post_status' => 'publish',
//                'post_author'   => 1,
            );

            // Insert the post into the database.
            $post_id = wp_insert_post( $lead_data );

            if ( ! is_wp_error( $post_id ) ) {
                //the post is valid

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


                // save creation time
                update_post_meta( $post_id, 'wpl_creation_time', current_time( 'mysql' ) );


                $response['status'] = 1;
                $response['msg'] = __( 'Thanks, your information has been successfully stored.', 'wp-leads' );

            } else {
                //there was an error in the post insertion,
                $response['msg'] = $post_id->get_error_message();
            }

        } else {
            $response['msg'] = __( 'Please enter name and email!', 'wp-leads' );
        }


        echo json_encode( $response );

        // Don't forget to stop execution afterward.
        wp_die();
    }


}
