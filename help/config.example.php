<?php

$invoice_payment_options = array(
    'resources_dir' => '/layouts/vlayout/modules/InvoicePayment/resources',
    'invoice_url' => '/modules/InvoicePayment/InvoicePayment.php',
    'logo' => 'my-logo.png',
    'title' => 'Invoice payment',
    'status_to_pay' => array('Sent', 'Created'),
    'status_paid' => 'Paid',
);

$invoice_payment_rbk_money = array(
    'logo' => 'rbk_money.gif',
    'eshopId' => 1,
    'payment_success_status' => 5,
    'recipientCurrency' => 'RUR',
    'apiUrl' => 'https://rbkmoney.ru/acceptpurchase.aspx',
    'returnUrl' => 'http://my-site.ru/modules/InvoicePayment/systems/RBKMoney.php',
);
