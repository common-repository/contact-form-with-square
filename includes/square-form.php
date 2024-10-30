<?php
/**
 * Define Form Field for square
 */
if (!function_exists('square_payment_field ')) 
{

  function square_payment_field() {
    echo '
    <div id="sq_container">
    <div id="sq-card-number"></div>
    <div class="third">
    <div id="sq-expiration-date"></div>
    </div>
    <div class="third">
    <div id="sq-cvv"></div>
    </div>
    <div class="third">
    <div id="sq-postal-code"></div>
    </div>
    </div>
    ';
  }

/**
 * Add action for square payment gateway field
 */
add_action( 'lm_form_action', 'square_payment_field');
}