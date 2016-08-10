<?php
use Tygh\Registry;

include_once ('quikwallet/quikwallet_helper.inc');

if ( !defined('AREA') ) { die('Access denied'); }

//fn_print_r(" quikwallet.php came ");


// Return from payment
if (defined('PAYMENT_NOTIFICATION')) {

    //fn_print_r(" PAYMENT_NOTIFICATION CASE ");

    //if form is submit through quikwallet form on front end
    if (!empty($_REQUEST["quikwalletsubmit"])) {
        //fn_print_r("HERE IN RETURN MODE");
        //fn_print_r($_REQUEST["quikwalletsubmit"]);

        /*
        if (isset($view) === false)
        {
            $view = Registry::get('view');
        }


        $view->assign('order_action', __('placing_order'));
        $view->display('views/orders/components/placing_order.tpl');
        fn_flush();
         */

        $order_id = fn_quikwallet_place_order($_REQUEST['order_id']);

        //fn_print_r("order id is  ".$order_id);

        if(!empty($order_id)){
            if (fn_check_payment_script('quikwallet.php',$order_id,$processor_data)) {
                //fn_print_r("if case of fn_check_payment_script ");

                if (empty($processor_data)) {
                    $processor_data = fn_get_processor_data($_REQUEST['quik_email']);
                }

                // Getting data to build url
                $partnerid =  $processor_data['processor_params']['quikwallet_partnerid'];
                // Url to call
                $url       = $processor_data['processor_params']['quikwallet_url']  . "/" .$partnerid . "/requestpayment";
                $secret    =  $processor_data['processor_params']['quikwallet_secret'];

                //fn_print_r("URL is ".$url. "  partner id is ". $partnerid."  secret is ".$secret);

                /*
                 * force partnerurl to checkout url. Currently payment response only working on view
                 * cart page
                 */

                $mobile  = $_REQUEST["phone"];
                $amount  = $_REQUEST["amount"];
                $name    = $_REQUEST["firstname"];
                $email   = $_REQUEST["quik_email"];
                $address = $_REQUEST["address1"] . ", " . $_REQUEST["address2"];
                $city    = $_REQUEST["city"];
                $pincode = $_REQUEST["zipcode"];
                $orderid = $order_id;
                $date_c  = date('Y-m-d H:i');

                $_SESSION['order_id'] = $orderid;

                //fn_print_r(" DUMP ".var_export($_REQUEST,true));

                /*
                 * Record order details
                 *
                 */

                db_query("REPLACE INTO ?:quik_pay (
                    `order_no` ,
                    `date_c` ,
                    `name`,
                    `email_id`,
                    `address`,
                    `city` ,
                    `pincode` ,
                    `mobile`,
                    `amount` ,
                    `q_id`,
                    `hash`,
                    `checksum`,
                    `order_status`)
                    VALUES(
                        '$orderid',
                        '$date_c',
                        '$name',
                        '$email',
                        '$address',
                        '$city',
                        '$pincode',
                        '$mobile',
                        '$amount',
                        '','','','')");

                //fn_print_r("HERE query esxecuted");

                $return_url_fromQW =fn_url("payment_notification?payment=quikwallet", AREA, 'http');

                //fn_print_r("back URL is ".$return_url_fromQW);


                $postFields = Array(
                    "partnerid" => $partnerid, //fixed
                    "secret" => $secret, //fixed
                    //"outletid" => "39", //fixed - only for restaurant
                    "redirecturl" => $return_url_fromQW . "", //fixed
                    "mobile" => $mobile, //client mobile no
                    "billnumbers" => $orderid, //unique order no in the system
                    "email" => $email, //unique order no in the system
                    "amount" => $amount //amount for the transaction
                );


                //$this->log->debug("Post fields are " , $postFields);
                // AJAX call starts
                // Building post data
                $postFields = http_build_query($postFields);

                //cURL Request
                $ch = curl_init();

                //set the url, number of POST vars, POST data

                // defaults setting
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($ch,CURLOPT_HEADER,false);
                curl_setopt($ch,CURLOPT_ENCODING,'gzip,deflate');
                curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,15);
                curl_setopt($ch,CURLOPT_TIMEOUT,30);
                curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
                curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);

                // contextual info apart from defaults
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

                $info = curl_getinfo($ch);

                $result = curl_exec($ch);


                if ($result === false) {
                    $result = curl_error($ch);
                }

                // Fetching response
                $resp = $result;

                // Decode
                $r = json_decode($resp, true);

                if ($r['status'] == 'failed') {
                    $message = $r['message'];

                } else if ($r['status'] == 'success') {
                    $id     = $r['data']['id'];
                    $hash   = $r['data']['hash'];
                    $newurl = $r['data']['url'];


                    ////fn_print_r(" DATA jason ".var_export($r['data'],true));

                    $id2 = substr($id, 2);

                    // post API DB part
                    $sql = "UPDATE ?:quik_pay  SET `q_id` = '$id2' , `hash` = '$hash' WHERE
                        `order_no` = '$orderid' ";
                    db_query($sql);


                    //fn_print_r(" new location told is -> ".$newurl);

                    header("Location: " . $newurl);

                } else {
                    //print "Invalid Response";
                }
            }
        }
        else {
            fn_set_notification('E', __('error'), __('text_qw_failed_order').$_REQUEST['order_id']);
            fn_order_placement_routines('checkout_redirect');
        }
    }
    else  if (isset($_GET['status']) && isset($_GET['id']) && isset($_GET['checksum'])) {

        $status   = $_GET["status"];
        $id       = $_GET["id"];
        $checksum = $_GET["checksum"];
        $order_id = $_SESSION['order_id'];

        //fn_print_r(" checksum case -->  for order_id ".$order_id ."  status " .$status." id ".$id." checksum ".$checksum);

        if(!empty($order_id)){
            if (fn_check_payment_script('quikwallet.php',$order_id,$processor_data)) {
                //fn_print_r("if case of fn_check_payment_script ");

                if (empty($processor_data)) {
                    $processor_data = fn_get_processor_data($_REQUEST[$order_id]);
                }

                // Getting data to build url
                $partnerid =  $processor_data['processor_params']['quikwallet_partnerid'];
                // Url to call
                $url       = $processor_data['processor_params']['quikwallet_url']  . "/" .$partnerid . "/requestpayment";
                $secret    =  $processor_data['processor_params']['quikwallet_secret'];

                //fn_print_r("URL is ".$url. "  partner id is ". $partnerid."  secret is ".$secret);

                $text = "status=$status&id=$id&billnumbers=$order_id";
                $hmac = hash_hmac('sha256', $text, $secret);


                if ($hmac == $checksum) {

                    $escape_order_status =  $status;
                    $escape_checksum =  $checksum;
                    $escape_q_id = $id;

                    // post API DB part

                    $sql = "UPDATE ?:quik_pay  SET `order_status` = '$escape_order_status' , `checksum` = '$escape_checksum' WHERE
                        `q_id` = '$escape_q_id' ";

                    db_query($sql);

                    $status = strtolower($status);

                    if($status == "paid"){
                        $pp_response['order_status'] = 'P';
                        $pp_response['reason_text'] = "Thank you. Your order has been processed successfully. QuikWallet transcation id -> ".$id;
                        $pp_response['transaction_id'] = $order_id;
                        $pp_response['client_id'] = $id;

                        fn_finish_payment($order_id, $pp_response);
                        fn_order_placement_routines('route', $order_id);
                    }
                    else {
                        $pp_response['order_status'] = 'F';
                        $pp_response['reason_text'] = "Your order has been unsuccessfull. QuikWallet transcation id -> ".$id;
                        $pp_response['transaction_id'] = $order_id;
                        $pp_response['client_id'] = $id;

                        fn_finish_payment($order_id, $pp_response);
                        fn_set_notification('E', __('error'), __('text_qw_failed_order').$order_id);
                        //fn_order_placement_routines('checkout_redirect');
                        fn_order_placement_routines('route', $order_id);
                    }
                } else {
                    //print "Invalid response, please try again <hr>\n";
                    $pp_response['order_status'] = 'D';
                    $pp_response['reason_text'] = "Your Order #".$order_id." was not completed due to SECURITY Error!, please refer Quikwallet Payment reference ID ".$id;
                    $pp_response['transaction_id'] = $order_id;
                    $pp_response['client_id'] = $id;

                    fn_finish_payment($order_id, $pp_response);
                    //fn_set_notification('E', __('error'), "Your Order #".$order_id." was not completed due to SECURITY Error!, please refer Quikwallet Payment reference ID ".$id);
                    fn_order_placement_routines('route', $order_id);


                    //fn_order_placement_routines('checkout_redirect');
                }
            }
        }
    }

    exit;
}


else {

    $service_provider = 'quikwallet';
    $return_url = fn_url("payment_notification?payment=quikwallet", AREA, 'current');

    ////fn_print_r($order_info);

    $quikwallet_args = array(
        'amount' => $order_info['total'],
        'firstname' => $order_info['b_firstname'],
        'quik_email' =>  $order_info['email'],
        'phone' => $order_info['b_phone'],
        'productinfo' => "Order# ".$order_id,
        'lastname' => $order_info['b_lastname'],
        'address1' => $order_info['b_address'],
        'address2' => $order_info['b_address'],
        'city' => $order_info['b_city'],
        'state' => $order_info['b_state'],
        'country' => $order_info['b_country'],
        'zipcode' => $order_info['b_zipcode'],
        'order_id' => $order_id,
        'service_provider' => $service_provider
    );

    $quikwallet_args_array = array();
    foreach ($quikwallet_args as $key => $value) {
        if (in_array($key, array(
            'quik_email',
            'phone'
        ))) {
            $quikwallet_args_array[] = "<input name='$key' value='$value'/>";
        } else {
            $quikwallet_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
        }
    }

    $inputs_array = implode('', $quikwallet_args_array);

    $html1 = '<form name="quikwallet-form" id="quikwallet-form" action="'.$return_url.'" target="_parent" method="POST">
        <p >Please check your email and mobile number</p>
        <p hidden name="check_message"><strong>Mobile number should be 10 digits</strong></p>
        <p hidden name="check_message_email"><strong>Enter valid email address</strong></p>
        '.$inputs_array.'
        <table>
        <tr>
        <td colspan="2" align="center" height="26">
        <input type="submit" name="quikwalletsubmit" id="quikwalletsubmit" value="Pay via QuikWallet" class="free_input" > <?php echo $cancel_url; ?>';

    $js1 = ' <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" data-no-defer=""></script>
        <script type="text/javascript">';

$js1 .= ' function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function validateMobile(mobile) {
    var re =/^\d{10}$/;
    return re.test(mobile);
}

jQuery(function(){

    jQuery("#quikwalletsubmit").click( function() {
        if (!(validateMobile(jQuery("input[name=phone]").val()))){
            jQuery("p[name=check_message]").show();
            return false;
}
else if (!(validateEmail(jQuery("input[name=quik_email]").val()))){
    jQuery("p[name=check_message_email]").show();
    return false;
}
else{

    jQuery("body").block({
        message: "' . __('Do not Refresh or press Back. Redirecting you to QuikWallet to complete the payment.', 'woo_quikwallet') . '",
            overlayCSS: {
                background     : "#fff",
                    opacity          : 0.6
},
css: {
    padding            : 20,
        textAlign        : "center",
        color            : "#555",
        border           : "3px solid #aaa",
        backgroundColor  : "#fff",
        cursor           : "wait",
        lineHeight       : "32px"
}
});
}
} );

} );

'  ;

$js1 .= '</script>';

$html2  = '             </td>
    </tr>
    </table>
    </form>'  ;

if (!$quikwallet_args['amount']) {
    echo __('text_unsupported_currency');
    exit;
}

echo <<<EOT
    {$html1}
    {$js1}
    {$html2}
</body>
</html>
EOT;
exit;
}

?>
