(function ($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */



    $( document ).ready( function () {


        // submit wp-leads form
        $( "#wpl_lead_form" ).submit( function (e) {
            // Stop form from submitting normally
            e.preventDefault();

            $( "#wpl_submit" ).attr( 'disabled', 'disabled' );
            $( "#wpl_submit" ).text( 'Please wait!' );

            $( '#wpl_response' ).removeClass( 'wpl_success wpl_fail' );
            $( '#wpl_response' ).hide();

            // Get some values from elements on from the form:
            var $form = $( this ),
                url = $form.attr( "action" ),
                form_data = $form.serialize();

            $.ajax( {
                type: 'POST',
                url: wp_leads_object.ajax_url,
                data: form_data + "&action=wp_lead_save"
            } ).done( function (data) {


                $( "#wpl_submit" ).removeAttr( 'disabled' );
                $( "#wpl_submit" ).text( 'Submit' );


                if ( data.status == 1 ) {
                    $( '#wpl_response' ).addClass( 'wpl_success' );

                    // reset other than nonce fields input data
                    $form.find( '[name=wpl_name]' ).val( '' );
                    $form.find( '[name=wpl_phone]' ).val( '' );
                    $form.find( '[name=wpl_email]' ).val( '' );
                    $form.find( '[name=wpl_budget]' ).val( '' );
                    $form.find( '[name=wpl_message]' ).val( '' );

                } else {
                    $( '#wpl_response' ).addClass( 'wpl_fail' );
                }


                $( '#wpl_response span' ).text( data.msg );
                $( '#wpl_response' ).show();

                // scroll near to response message div to show whats status after submission
                $( 'html, body' ).animate( {
                    scrollTop: $( "#wpl_message" ).offset().top
                }, 2000 );

            } ).fail(function () {

                $( "#wpl_submit" ).removeAttr( 'disabled' );
                $( "#wpl_submit" ).text( 'Submit' );

                $( '#wpl_response' ).addClass( 'wpl_fail' );
                $( '#wpl_response span' ).text( wp_leads_object.error_msg );
                $( '#wpl_response' ).show();

            });

            return false;
        } );
    } );

})( jQuery );

/**
 * this will prevent form submission other than Ajax
 * even if document is not ready
 */
function wp_leads_form_submit() {
    return false;
}
