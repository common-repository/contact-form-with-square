<?php

/**
 * Init square payment gateway info settings
 */
add_action( 'admin_init', 'cfws_square_init' );

/**
 * Displaying all the setting form
 */
function cfws_square_setting_page( ) {
?>
<div class="wrap">
   <div class="welcome-panel square-admin-form">
     <form action='options.php' method='post'>
        <h2><?php esc_html_e("SQUARE PAYMENT SETTINGS","lm-contact-square"); ?></h2>
        <?php
            settings_fields( 'lmPlugin' );
            do_settings_sections( 'lmPlugin' );
            submit_button();
        ?>
     </form>
     <div class="how-to-use">
       <h2>How To Use?</h2>
       <ul>
        <li>Please use the shortcode <code>[lm_contact_form]</code> to any pages or post where you want.</li>
       </ul>
      </div>
      <div class="how-to-connect">
       <h2>How To Connect With Square?</h2>
       <ul>
        <li>1). Login to your <a href="https://squareup.com/login" target="_blank" >Square Account</a>.</li>
        <li>2). To Pay with Square payment acquirer you need to set Application Id, Access Token, and Location ID. To get your credentials Goto <a href="https://squareup.com/login?return_to=https://connect.squareup.com/apps" target="_blank" >https://squareup.com/login?return_to=https://connect.squareup.com/apps</a> . After Login to this login URL, you can see your Application Dashboard as shown below. Then click on 'Create Your First Application'.<br/><br/>
        <img src="<?php echo plugins_url( 'contact-form-with-square/assets/images/application1.png' ); ?>" width="100%">
        </li>
        <li>3). Set New Application Name and click on 'Create Application '.<br/><br/>
        <img src="<?php echo plugins_url( 'contact-form-with-square/assets/images/application2.png' ); ?>" width="100%">
        <img src="<?php echo plugins_url( 'contact-form-with-square/assets/images/application3.png' ); ?>" width="100%">
        </li>
        <li>4). Select Your Application and get Your Credentials. Use SANDBOX credentials.Do not use 'Personal Access Token'. It is for a live account.In the Sandbox area, you can find your Sandbox Application ID and Sandbox Access Token.<br/>
        <img src="<?php echo plugins_url( 'contact-form-with-square/assets/images/application4.png' ); ?>" width="100%">
        </li>
        <li>5). To Get your Location Id, Click on Location Tab.There also you have to use Sandbox Locations.It will give you multiple locations you can use any of them from Sandbox.<br/>
        <img src="<?php echo plugins_url( 'contact-form-with-square/assets/images/application5.png' ); ?>" width="100%">
        </li>
        <li>6). Set this Application ID, Access Token, and Location ID into your payment acquirer and get started to payment.</li>
       </ul>
      </div>
   </div>
</div>
<?php
}

/**
 * Define all the required fields for square payment gateway
 */
if (!function_exists('square_api_type_render')) 
{
    function cfws_square_init(  ) {
        register_setting( 'lmPlugin', 'lm_settings' );
        add_settings_section(
            'lm_settings_lmPlugin_section',
            esc_html__( '', 'lm-contact-square' ),
            'cfws_settings_section_callback',
            'lmPlugin'
        );

        add_settings_field(
            'square_api_type',
            esc_html__( 'Mode', 'lm-contact-square' ),
            'square_api_type_render',
            'lmPlugin',
            'lm_settings_lmPlugin_section'
        );

        add_settings_field(
            'square_api_app_id',
            esc_html__( 'App Id', 'lm-contact-square' ),
            'square_api_app_id_render',
            'lmPlugin',
            'lm_settings_lmPlugin_section'
        );

        add_settings_field(
            'square_api_token_id',
            esc_html__( 'Token Id', 'lm-contact-square' ),
            'square_api_token_id_render',
            'lmPlugin',
            'lm_settings_lmPlugin_section'
        );

        add_settings_field(
            'square_api_location_id',
            __( 'Location Id', 'lm-contact-square' ),
            'square_api_app_id_render',
            'lmPlugin',
            'lm_settings_lmPlugin_section'
        );

        add_settings_field(
            'square_api_currency',
            esc_html__( 'Default Currency', 'lm-contact-square' ),
            'square_api_currency_render',
            'lmPlugin',
            'lm_settings_lmPlugin_section'
        );


        add_settings_field(
            'square_api_sucess_page',
            esc_html__( 'Success Page', 'lm-contact-square' ),
            'square_api_sucess_page_render',
            'lmPlugin',
            'lm_settings_lmPlugin_section'
        );
    }
}

/**
 * Define field for Type
 */
if (!function_exists('square_api_type_render')) 
{
    function square_api_type_render(  ) {
        $options = get_option( 'lm_settings' );
        ?>
        <select name='lm_settings[square_api_type]'>
            <option value='1' <?php selected( $options['square_api_type'], 1 ); ?>>Production</option>
            <option value='2' <?php selected( $options['square_api_type'], 2 ); ?>>Sandbox</option>
        </select>

    <?php
    }
}

/**
 * Define field for app id
 */
if (!function_exists('square_api_app_id_render')) 
{
    function square_api_app_id_render(  ) {
        $options = get_option( 'lm_settings' );
        ?>
        <input type='text' name='lm_settings[square_api_app_id]' value='<?php echo $options['square_api_app_id']; ?>'>
        <?php
    }
}

/**
 * Define field for Token id
 */
if (!function_exists('square_api_token_id_render')) 
{
    function square_api_token_id_render(  ) {
        $options = get_option( 'lm_settings' );
        ?>
        <input type='text' name='lm_settings[square_api_token_id]' value='<?php echo $options['square_api_token_id']; ?>'>
        <?php
    }
}
/**
 * Define field for location id
 */
if (!function_exists('square_api_token_id_render')) 
{
    function square_api_location_id_render(  ) {
        $options = get_option( 'lm_settings' );
        ?>
        <input type='text' name='lm_settings[square_api_location_id]' value='<?php echo $options['square_api_location_id']; ?>'>
        <?php
    }
}

/**
 * Define field for Currency
 */
if (!function_exists('square_api_currency_render')) 
{
    function square_api_currency_render(  ) {
        $options = get_option( 'lm_settings' );
        ?>
        <select name='lm_settings[square_api_currency]'>
            <option value='USD' <?php selected( $options['square_api_currency'], 'USD' ); ?>>USD</option>
            <option value='AUD' <?php selected( $options['square_api_currency'], 'AUD' ); ?>>AUD</option>
            <option value='CAD' <?php selected( $options['square_api_currency'], 'CAD' ); ?>>CAD</option>
            <option value='JPY' <?php selected( $options['square_api_currency'], 'JPY' ); ?>>JPY</option>
            <option value='GBP' <?php selected( $options['square_api_currency'], 'GBP' ); ?>>GBP</option>
        </select>

    <?php
    }
}

/**
 * Define field for Sucess Page
 */
if (!function_exists('square_api_sucess_page_render')) 
{
    function square_api_sucess_page_render(  ) {
        $options = get_option( 'lm_settings' );
        ?>
        <select name='lm_settings[square_api_sucess_page]'>
            <option value=""><?php esc_html_e("Select Page","lm-contact-square"); ?></option>
            <?php 
            $pages = get_pages(); 
            foreach ( $pages as $page ) { 
            ?>
            <option value="<?php echo $page->post_name; ?>" <?php selected( $options['square_api_sucess_page'], $page->post_name ); ?>><?php echo $page->post_title; ?>
            </option>
            <?php } ?>
        </select>
    <?php
    }
}


/**
 * Define Description for the form
 */
if (!function_exists('cfws_settings_section_callback')) 
{
    function cfws_settings_section_callback(  ) {
        esc_html_e( 'All the Settings For square payment gateway', 'lm-contact-square' );
    }
}
