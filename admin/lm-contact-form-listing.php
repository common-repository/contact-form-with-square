<?php
/**
 * Listing page
 */
if (!function_exists('cfws_settings_page')) 
{
  function cfws_settings_page() {
    $customers_obj = new ContactForm_List();
    if(isset($_GET['action']) && $_GET['action'] == 'view'){ ?>
      <?php
      $details = $customers_obj->view_details($_GET['customer']);
      $name = $details['first_name'].' '.$details['last_name'];
      $email = $details['email'];
      $phone = $details['phone'];
      $amount = $details['amount'];
      $date_time = $details['date_time'];
      $transaction_id = $details['transaction_id'];
      $phone = $details['phone'];
      $card_details = json_decode($details['transaction_data']);
      $order_id = $card_details->payment->order_id;
      $order_status = $card_details->payment->status;
      ?>
      <div class="wrap">
        <h2><?php echo $name; ?> <?php esc_html_e("Details","lm-contact-square"); ?></h2>
        <span><a href="<?php echo "?page=lm-form-listing"; ?>"> Back To List </a></span>
        <div id="poststuff">
          <div id="post-body" class="metabox-holder ">
            <div id="post-body-content">
              <div class="welcome-panel view-details">
                <ul>
                 <li><span><?php esc_html_e("Name","lm-contact-square"); ?>: </span><?php echo $name; ?></li>
                 <li><span><?php esc_html_e("Email","lm-contact-square"); ?>: </span><?php echo $email; ?></li>
                 <li><span><?php esc_html_e("Phone","lm-contact-square"); ?>: </span><?php echo $phone; ?></li>
                 <li><span><?php esc_html_e("Amount","lm-contact-square"); ?>: </span><?php echo $amount; ?></li>
                 <li><span><?php esc_html_e("Date/Time","lm-contact-square"); ?>: </span><?php echo $date_time; ?></li>
                 <li><span><?php esc_html_e("Transaction Id","lm-contact-square"); ?>: </span><?php echo $transaction_id; ?></li>
                 <li><span><?php esc_html_e("Order Id","lm-contact-square"); ?>: </span><?php echo $order_id; ?></li>
                 <li><span><?php esc_html_e("Order Status","lm-contact-square"); ?>: </span><?php echo $order_status; ?></li>
               </ul>
             </div>
           </div>
         </div>
         <br class="clear">
       </div>
     </div>
   <?php }else{ ?>
     <div class="wrap">
      <h2><?php echo __("Contact Form Lists","lm-contact-square"); ?></h2>
      <div id="poststuff">
        <div id="post-body" class="metabox-holder ">
          <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
              <form method="post">
                <?php
                $customers_obj->prepare_items();
                $customers_obj->display(); 
                ?>
              </form>
            </div>
          </div>
        </div>
        <br class="clear">
      </div>
    </div>
    <?php
  }
}
}