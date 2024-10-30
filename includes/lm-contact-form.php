<?php
/**
 * Fontend Contact Form
 */
if (!function_exists('cfws_html_form_code')) 
{
    function cfws_html_form_code() {
        echo '<div class="contact-form-with-square">';
    	echo '<div id="lm-message"></div>';
        echo '<form method="post">';
        echo '<p>';
        echo '<input type="text" name="first_name" id="first_name" placeholder="'.__("First Name","lm-contact-square").'*" class="required" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["first_name"] ) ? esc_attr( $_POST["first_name"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo '<input type="text" name="last_name" id="last_name" placeholder="'.__("Last Name","lm-contact-square").'*" class="required" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["last_name"] ) ? esc_attr( $_POST["last_name"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo '<input type="email" name="email" id="email" placeholder="'.__("Email","lm-contact-square").'*" class="required" value="' . ( isset( $_POST["email"] ) ? esc_attr( $_POST["email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo '<input type="tel" name="phone" id="phone" placeholder="'.__("Phone","lm-contact-square").'*" value="' . ( isset( $_POST["phone"] ) ? esc_attr( $_POST["phone"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo '<input type="text" pattern="[0-9]*" name="amount" id="amount" placeholder="'.__("Amount","lm-contact-square").'*" pattern="[0-9 ]+" value="' . ( isset( $_POST["amount"] ) ? esc_attr( $_POST["amount"] ) : '' ) . '" size="40" />';
        echo '</p>';
        do_action( 'lm_form_action');
        echo '<p><input type="submit" name="lm-submitted" onclick="onLmCardNonce(event)" value="'.__("Send","lm-contact-square").'"/></p>';
        echo '</form>';
        echo '<div id="loading" style="display:none">Loading&#8230;</div>';
        echo '</div>';
    }
}

/**
 * Handling Submit event 
 * @global type $wpdb
 */


if (!function_exists('cfws_contact_form_submit')) 
{
    add_action('wp_ajax_cfws_contact_form_submit', 'cfws_contact_form_submit');
    add_action('wp_ajax_nopriv_cfws_contact_form_submit', 'cfws_contact_form_submit');

    function cfws_contact_form_submit() {
                global $wpdb; 
                $result = array('status' => '', 'message' => '');
                $first_name = sanitize_text_field($_POST['first_name']);
                $last_name = sanitize_text_field($_POST['last_name']);
                $email = sanitize_email($_POST['email']);
                $phone = sanitize_text_field($POST['phone']);
                $amount = sanitize_text_field($_POST['amount']);
                $card_nonce = sanitize_text_field($_POST['nonce']);
                $buyerVerification = sanitize_text_field($_POST['buyerVerify']);
               
                if ($amount > 0 && $card_nonce) {
                    //get square settings
                    $mode = get_option( 'lm_settings' );
                    if ($mode['square_api_type'] == '2') {
                       $host_url = 'https://connect.squareupsandbox.com';
                    }else{
                      $host_url = 'https://connect.squareup.com';
                    }     

                    $token = $mode['square_api_token_id'];
                    $location_id = $mode['square_api_location_id'];
                    $access_token = $token;
                    # setup authorization
                    $api_config = new \SquareConnect\Configuration();
                    $api_config->setHost("https://connect.squareupsandbox.com");
                    $api_config->setAccessToken($access_token);
                    $api_client = new \SquareConnect\ApiClient($api_config);
                    # create an instance of the Location API
                    $locations_api = new \SquareConnect\Api\LocationsApi($api_client);

                    if ($token && $location_id) {
                        try {

                            $note_fields = __('Contact Form to Receive Payment','lm-contact-square');
                            $currency = $mode['square_api_currency'];
                            $currency = !empty($currency) ? $currency :'USD';
                            $payments_api = new \SquareConnect\Api\PaymentsApi($api_client);
                            $body = new \SquareConnect\Model\CreatePaymentRequest();
                            $amountMoney = new \SquareConnect\Model\Money();
                            $amountMoney->setAmount((int) round($amount, 2) * 100);
                            $currency = $mode['square_api_currency'];
                            $currency = !empty($currency) ? $currency :'USD';
                            $amountMoney->setCurrency($currency);
                            $body->setSourceId($card_nonce);
                            $body->setAmountMoney($amountMoney);
                            $body->setLocationId($location_id);
                            $body->setIdempotencyKey((string) time());
                            $body->setVerificationToken($buyerVerification);
                            $body->setNote($note_fields);
                            $transaction = $payments_api->createPayment($body);
                            $transactionData = json_decode($transaction, true);

                            if (isset($transactionData['payment']['id'])) {
                            	$transactionId = $transactionData['payment']['id'];
                                do_action('lm_payment_success', $transactionData, '');
                                //Insert into database
                                $table_name = $wpdb->prefix . "lm_contact"; 
                                //Insert into database
                                $insert = $wpdb->insert($table_name, 
                                    array('first_name' => $first_name,
                                          'last_name' => $last_name,
                                          'email' => $email,
                                          'phone' => $phone,
                                          'amount' => $amount,
                                          'date_time' => $transactionData['payment']['created_at'],
                                          'transaction_id' => $transactionId,
                                          'transaction_data' => json_encode($transactionData),
                                      ),array('%s','%s','%s','%s','%s','%s'));


                                //send email to admin
                                $to = get_option( 'admin_email' );
                                if ($to) {
                                    $subject = esc_html( __('New Payment Received','lm-contact-square'));
                                    $body = '<p>'.esc_html( __("New Payment","lm-contact-square")).'</p>
                                             <p><strong>'.esc_html( __("Amount:","lm-contact-square")).'</strong> ' . $amount . '</p>
                                             <p><strong>'.esc_html( __("Transaction ID:","lm-contact-square")).'</strong> ' . $transactionId . '</p>
                                             ';
                                    $headers = array(
                                        'Content-Type: text/html; charset=UTF-8',
                                        'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>'
                                    );

                                    wp_mail($to, $subject, $body, $headers);
                                }

                                //send email to customer for confirmation
                                $to_customer = $email;
                                if ($to_customer) {
                                    $subject = __('Payment Receipt For ' . get_option('blogname'),'lm-contact-square');
                                    $body = '<p>'.esc_html( __("Thanks for the payment","lm-contact-square")).'</p>
                                             <p><strong>'.esc_html( __("Amount:","lm-contact-square")).'</strong> ' . $amount . '</p>
                                             <p><strong>'.esc_html( __("Transaction ID:","lm-contact-square")).'</strong> ' . $transactionId . '</p>
                                             ';
                                    $headers = array(
                                        'Content-Type: text/html; charset=UTF-8',
                                        'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>'
                                    );

                                    wp_mail($to_customer, $subject, $body, $headers);
                                }
                                
                                if($mode['square_api_sucess_page']){ 
                                    $redirect_page  = get_permalink( get_page_by_path( $mode['square_api_sucess_page'] ) ); 
                                }else{
                                   $redirect_page  = '';
                                }
                                
                            	$result = array('status' => 'success', 'message' => esc_html( __('Thank you '.$first_name.' for your Payment', 'lm-contact-square')), 'redirect' => $redirect_page);                        
                            }
                           } catch (Exception $ex) {
                             $errors = $ex->getResponseBody()->errors;

                             $message = '';
                             foreach ($errors as $error) {
                                $message = $error->detail;
                                if (isset($error->field))
                                    $message = $error->field . ' - ' . $error->detail;
                            }
                            $result = array('status' => 'error', 'message' => $message);
                            do_action('lm_payment_failed', $result, '');
                        }
                    } else {
                     $result = array('status' => 'error', 'message' => esc_html( __('Invalid square token or location id.', 'lm-contact-square')));
                        do_action('lm_payment_failed', $result, '');
                    }
                }
                echo json_encode($result);
                wp_die();
            }
}

/**
 * Generate Shortcode to use front-end side
 */
if (!function_exists('cfws_shortcode')) 
{
    add_shortcode( 'lm_contact_form', 'cfws_shortcode' );
    function cfws_shortcode() {
        ob_start();
        cfws_html_form_code();
        return ob_get_clean();
    }
}