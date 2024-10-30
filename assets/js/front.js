// Create and initialize a payment form object
const paymentForm = new SqPaymentForm({
  // Initialize the payment form elements

  //TODO: Replace with your sandbox application ID
  applicationId: lm_ajax.application_id,
  locationId: lm_ajax.location_id,
  inputClass: 'sq-input',
  autoBuild: false,
  // Customize the CSS for SqPaymentForm iframe elements
  inputStyles: [{
      fontSize: '16px',
      lineHeight: '24px',
      padding: '16px',
      placeholderColor: '#a0a0a0',
      backgroundColor: 'transparent',
  }],
  // Initialize the credit card placeholders
  cardNumber: {
      elementId: 'sq-card-number',
      placeholder: 'Card Number'
  },
  cvv: {
      elementId: 'sq-cvv',
      placeholder: 'CVV'
  },
  expirationDate: {
      elementId: 'sq-expiration-date',
      placeholder: 'MM/YY'
  },
  postalCode: {
      elementId: 'sq-postal-code',
      placeholder: 'Postal'
  },
  // SqPaymentForm callback functions
  callbacks: {
      /*
      * callback function: cardNonceResponseReceived
      * Triggered when: SqPaymentForm completes a card nonce request
      */
      cardNonceResponseReceived: function (errors, nonce, cardData) {
                jQuery('#lm-message').html("");
                var first_name = jQuery('#first_name').val();
                var last_name = jQuery('#last_name').val();
                var email = jQuery('#email').val();
                var phone = jQuery('#phone').val();
                var amount = jQuery('#amount').val();
                var regex = '/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/';

                if(first_name == '' || validateName(first_name) == false){
                  errors.push({type : 'VALIDATION_ERROR',message: lm_ajax.first_name_error});
                  jQuery("#first_name").addClass('sq-input--error');
                }else{
                  jQuery("#first_name").removeClass('sq-input--error');
                }

                if(last_name == '' || validateName(last_name) == false){
                  errors.push({type : 'VALIDATION_ERROR',message: lm_ajax.last_name_error});
                  jQuery("#last_name").addClass('sq-input--error');
                }else{
                  jQuery("#last_name").removeClass('sq-input--error');
                }

                if(email == '' || validateEmail(email) == false){
                  errors.push({type : 'VALIDATION_ERROR',message: lm_ajax.email_error});
                  jQuery("#email").addClass('sq-input--error');
                }else{
                  jQuery("#email").removeClass('sq-input--error');
                }

                if(phone == ''){
                  errors.push({type : 'VALIDATION_ERROR',message: lm_ajax.phone_error});
                  jQuery("#phone").addClass('sq-input--error');
                }else{
                  jQuery("#phone").removeClass('sq-input--error');
                }
            
                if(amount == '' || validateAmount(amount) == false){
                  errors.push({type : 'VALIDATION_ERROR',message: lm_ajax.amount_error});
                  jQuery("#amount").addClass('sq-input--error');
                }else{
                  jQuery("#amount").removeClass('sq-input--error');
                }
               
                if (errors) {
                    var html = '<ul>';
                    // handle errors
                    errors.forEach(function (error) {
                        html += '<li class="lm-error">' + error.message + '</li>';
                    });
                    jQuery('#lm-message').html(html);
                    return;
                }

                var amount = parseFloat(jQuery('#amount').val());
                var verificationToken = 0;

                const verificationDetails = {
                    intent: 'CHARGE',
                    amount: amount.toString(),
                    currencyCode: lm_ajax.currency,
                    billingContact: {
                    }
                };
                
                paymentForm.verifyBuyer(
                    nonce,
                    verificationDetails,
                    function(err,result) {
                        if (err == null) {
                                jQuery("#loading").show();
                                var data = {
                                    action: 'cfws_contact_form_submit',
                                    nonce: nonce,
                                    buyerVerify: result.token,
                                    first_name:first_name,
                                    last_name:last_name,
                                    email:email,
                                    phone:phone,
                                    amount: amount
                                };
                                jQuery.ajax({
                                    url: lm_ajax.ajaxurl,
                                    data: data,
                                    type: 'post',
                                    success: function(message) {
                                        msg = jQuery.parseJSON(message);
                                        jQuery("#loading").hide();
                                        if (msg.status == 'error') {
                                            var error = '<ul><li class="lm-error">' + msg.message + '</li></ul>';
                                            jQuery('#lm-message').html(error);
                                        } else {
                                            var error = '<div class="lm-success-message"></div><ul><li class="lm-success">' + msg.message + '</li></ul>';
                                            jQuery('#lm-message').html(error);
                                            paymentForm.destroy();
                                            if (msg.redirect) {
                                                window.location.href = msg.redirect;
                                            }
                                        }
                                    }
                                });
                        }
                    });
        
      },
      paymentFormLoaded: function() {

      },
      unsupportedBrowserDetected: function() {

      }
  }
});
paymentForm.build();

// onGetCardNonce is triggered when the submit button is clicked
function onLmCardNonce(event) {
  var errors = [];
  // Don't submit the form until SqPaymentForm returns with a nonce
  event.preventDefault();
  // Request a nonce from the SqPaymentForm object
  paymentForm.requestCardNonce();
}

// Validation Email
function validateEmail(email) 
{
  var re = /^(?:[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/;
  return re.test(email);
}

// Validation Amount
function validateAmount(amount) 
{
  var re = /^\d+(?:[.,]\d+)*$/gm;
  return re.test(amount);
}

// Validation Name
function validateName(name) 
{
  var re =  /^[A-Za-z ]+$/;
  return re.test(name);
}