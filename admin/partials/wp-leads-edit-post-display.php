<?php

/**
 * Provide a admin area edit post view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://profiles.wordpress.org/itpixelz
 * @since      1.0.0
 * @subpackage Wp_Leads/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// get post meta array of current post
$post_metas = get_post_meta( $post->ID );

// get fields value from post meta array
$phone = ( isset( $post_metas['wpl_phone'][0] ) ) ? $post_metas['wpl_phone'][0] : '';
$email = ( isset( $post_metas['wpl_email'][0] ) ) ? $post_metas['wpl_email'][0] : '';
$budget = ( isset( $post_metas['wpl_budget'][0] ) ) ? $post_metas['wpl_budget'][0] : '';
$message = ( isset( $post_metas['wpl_message'][0] ) ) ? $post_metas['wpl_message'][0] : '';
$creation_time = ( isset( $post_metas['wpl_creation_time'][0] ) ) ? $post_metas['wpl_creation_time'][0] : '';

?>
<!-- Metabox fields container start -->
<div class="wpl_container">

    <!-- Phone number -->
    <div class="wpl_fieldset">
        <label for="wpl_phone" class="wpl_label"><?php _e( 'Phone Number', 'wp-leads' ) ?></label>
        <input type="tel" name="wpl_phone" id="wpl_phone" class="wpl_text_input" value="<?php echo esc_attr( $phone ); ?>"/>
    </div>

    <!-- Email address -->
    <div class="wpl_fieldset">
        <label for="wpl_email" class="wpl_label"><?php _e( 'Email Address', 'wp-leads' ) ?></label>
        <input type="email" name="wpl_email" id="wpl_email" class="wpl_text_input" value="<?php echo esc_attr( $email ); ?>"/>
    </div>

    <!-- Budget -->
    <div class="wpl_fieldset">
        <label for="wpl_budget" class="wpl_label"><?php _e( 'Desired Budget', 'wp-leads' ) ?></label>

        <select name="wpl_budget" id="wpl_budget" class="wpl_select">
            <option value=""><?php _e( '-- select --', 'wp-leads' ); ?></option>
            <option value="100-500" <?php if ( $budget == '100-500' ) { ?>selected="selected"<?php } ?>><?php _e( '$100$ - $500', 'wp-leads' ); ?></option>
            <option value="500-1000" <?php if ( $budget == '500-1000' ) { ?>selected="selected"<?php } ?>><?php echo _e( '$500$ - $1000', 'wp-leads' ); ?></option>
            <option value="1000-5000" <?php if ( $budget == '1000-5000' ) { ?>selected="selected"<?php } ?>><?php echo _e( '$1000$ - $5000', 'wp-leads' ); ?></option>
            <option value="5000+" <?php if ( $budget == '5000+' ) { ?>selected="selected"<?php } ?>><?php echo _e( '$5000+', 'wp-leads' ); ?></option>
        </select>

    </div>

    <!-- Message -->
    <div class="wpl_fieldset">
        <label for="wpl_message" class="wpl_label"><?php _e( 'Message', 'wp-leads' ) ?></label>
        <textarea rows="5" cols="10" name="wpl_message" id="wpl_message" class="wpl_textarea"><?php echo esc_html( $message ); ?></textarea>
    </div>

    <!-- Creation time-->
    <div class="wpl_fieldset">
        <label for="wpl_creation_time" class="wpl_label"><?php _e( 'Creation Time', 'wp-leads' ) ?></label>
        <input type="text" name="wpl_creation_time" id="wpl_creation_time" class="wpl_text_input" value="<?php echo esc_attr( $creation_time ); ?>" disabled="disabled"/>
    </div>

</div>
<!-- Metabox fields container end -->
