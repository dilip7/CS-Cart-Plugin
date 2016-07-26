REPLACE INTO cscart_payment_processors (`processor`,`processor_script`,`processor_template`,`admin_template`,`callback`,`type`) VALUES ('Quikwallet','quikwallet.php', 'views/orders/components/payments/cc_outside.tpl','quikwallet.tpl', 'Y', 'P');

REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','quikwallet_url','QuikWallet URL');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','quikwallet_partnerid','QuikWallet Partnetid');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','quikwallet_secret','QuikWallet Secret');


REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','text_qw_failed_order','Payment Failure.');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','text_qw_success','Payment Sucessful.');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','text_qw_auth_error','Payment failed, Invalid Authentication.');


CREATE TABLE IF NOT EXISTS  cscart_quik_pay (
        `order_no` int(11) NOT NULL AUTO_INCREMENT,
        `date_c` datetime NOT NULL,
        `name` varchar(200) NOT NULL,
        `email_id` varchar(200) NOT NULL,
        `address` varchar(200) NOT NULL,
        `city` varchar(200) NOT NULL,
        `pincode` varchar(10) NOT NULL,
        `mobile` varchar(10) NOT NULL,
        `amount` int(11) NOT NULL,
        `q_id` varchar(100) NOT NULL,
        `hash` varchar(100) NOT NULL,
        `checksum` varchar(200) NOT NULL,
        `order_status` varchar(100) NOT NULL,
        PRIMARY KEY (`order_no`)
      ) ENGINE=MyISAM DEFAULT CHARACTER SET utf8  COLLATE utf8_general_ci ;