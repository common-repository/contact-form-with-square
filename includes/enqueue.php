<?php
/**
 * Define All required jQuery and css
 */
if (!function_exists('cfws_front_style_script')) 
{
    function cfws_front_style_script()
    {
        global $post;

        wp_enqueue_style(
            'front-style',
            CFWS_URL.'assets/css/lmsqpaymentform.css',
            '',
            true
        );

        $mode = get_option( 'lm_settings' );

        if ($mode['square_api_type'] == '2') {
            wp_enqueue_script('square-script','https://js.squareupsandbox.com/v2/paymentform','',true);
        }else{
            wp_enqueue_script('square-script','https://js.squareup.com/v2/paymentform','',true);    
        }

        wp_enqueue_script(
            'front-script',
            CFWS_URL.'assets/js/front.js',
            '',
            true
        );

    }
    add_action( 'wp_footer', 'cfws_front_style_script' );
}

//ADD LOCALIZE
if (!function_exists('cfws_enqueue_scripts')) 
{
    function cfws_enqueue_scripts() {
        $mode = get_option( 'lm_settings' );
        wp_enqueue_script('jquery');
        wp_localize_script( 'jquery', 'lm_ajax', array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'nextNonce'     => wp_create_nonce( 'myajax-next-nonce' ),
            'application_id' => $mode['square_api_app_id'],
            'token_id'       => $mode['square_api_token_id'],
            'location_id'    => $mode['square_api_location_id'],
            'currency' => $mode['square_api_currency'],
            'first_name_error' => esc_html( __("Please Enter Valid First Name","lm-contact-square")),
            'last_name_error' => esc_html( __("Please Enter Valid Last Name","lm-contact-square")),
            'email_error' => esc_html( __("Please Enter Valid Email Address","lm-contact-square")),
            'phone_error' => esc_html( __("Please Enter Valid Phone Number","lm-contact-square")),
            'amount_error' => esc_html( __("Please Enter Valid Amount","lm-contact-square"))
        ));
    }
    add_action('wp_enqueue_scripts','cfws_enqueue_scripts');
}

// Update CSS within in Admin
if (!function_exists('cfws_admin_style')) 
{
  function cfws_admin_style() {
    wp_enqueue_style('admin-styles', CFWS_URL.'assets/css/square-admin.css');
  }
  add_action('admin_enqueue_scripts', 'cfws_admin_style');
}