<?php

chdir($dir . '../../../');

require_once('config.inc.php');
require_once('includes/Loader.php');
require_once('vtlib/Vtiger/Module.php');
require_once('include/utils/VtlibUtils.php');
require_once('includes/http/Request.php');
require_once('include/database/PearDatabase.php');
require_once('includes/runtime/Viewer.php');

# ini_set('display_errors',1);
# ini_set('display_startup_errors',1);
# error_reporting(-1);

class RBK_money_connector {
    function process($request) {
        global $invoice_payment_options;
        global $invoice_payment_rbk_money;

        $orderId = $this->getOrderId($request);
        $paymentStatus = $this->getPaymentStatus($request);
        $eshopId = $this->getEshopId($request);

        if ($eshopId != strval($invoice_payment_rbk_money['eshopId'])) {
            $this->send_incorrect_data();
        }

        if ($paymentStatus != strval($invoice_payment_rbk_money['payment_success_status'])) {
            $this->send_incorrect_data();
        }

        $invoice_data = $this->invoice_data($orderId);

        if (!$invoice_data) {
            $this->send_not_found();
        }

        if ($invoice_data['invoice_status'] == $invoice_payment_options['status_paid']) {
            $this->send_success();
        }

        if (!in_array($invoice_data['invoice_status'], $invoice_payment_options['status_to_pay'])) {
            $this->send_not_found();
        }

        $this->set_invoice_paid($orderId);
        $this->send_success();
    }

    function getPaymentStatus($request) {
        $paymentStatus = $request->get('paymentStatus');
        $paymentStatus = $this->purgeNumber($paymentStatus);
        return $paymentStatus;
    }

    function getEshopId($request) {
        $eshopId = $request->get('eshopId');
        $eshopId = $this->purgeNumber($eshopId);
        return $eshopId;
    }

    function getOrderId($request) {
        $orderId = $request->get('orderId');
        $orderId = $this->purgeNumber($orderId);
        return $orderId;
    }

    function invoice_data($number)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery($this->invoice_data_query(), array($number));
        $data = $db->fetch_array($result);
        return $data;
    }

    function set_invoice_paid($number)
    {
        global $invoice_payment_options;
        $db = PearDatabase::getInstance();
        $db->pquery($this->invoice_update_query(), array($invoice_payment_options['status_paid'], $number));
    }

    function invoice_data_query()
    {
        return <<<SQL
SELECT
i.invoice_no as invoice_number,
i.invoicestatus as invoice_status
FROM
vtiger_invoice i
WHERE
i.invoice_no = ?
SQL;
    }

    function invoice_update_query()
    {
        return <<<SQL
UPDATE
vtiger_invoice i
SET
i.invoicestatus = ?
WHERE
i.invoice_no = ?
SQL;
    }

    function send_incorrect_data()
    {
        header('HTTP/1.0 400 Incorrect data');
        echo "Incorrect data!\r\n";
        exit(1);
    }

    function send_not_found()
    {
        header('HTTP/1.0 404 Invoice not found');
        echo "Invoice not found!\r\n";
        exit(1);
    }

    function send_success()
    {
        header('HTTP/1.0 200 Success');
        echo "Success!\r\n";
        exit(0);
    }

    function purgeNumber($number)
    {
        return preg_replace("/\D/", '', $number);
    }

    function exitIfDisabled()
    {
        if (!vtlib_isModuleActive('InvoicePayment')) {
            header('HTTP/1.0 403 Forbidden');
            printf("Module '%s' is disabled!", 'InvoicePayment');
            exit(1);
        }
    }
}

$connector = new RBK_money_connector();
$connector->exitIfDisabled();
$request = new Vtiger_Request($_REQUEST);
$connector->process($request);