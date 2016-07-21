<?php

chdir($dir . '../../');

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

class InvoicePayment
{

    function getName()
    {
        return 'InvoicePayment';
    }

    function getNumber($request)
    {
        $number = $request->get('number');
        return $number;
    }

    function getId($request)
    {
        $id = $request->get('id');
        $id = $this->purgeNumber($id);
        return $id;
    }

    function purgeNumber($number)
    {
        return preg_replace("/\D/", '', $number);
    }

    function invoiceData($number, $id)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery($this->invoice_data_query(), array($number, $id));
        $data = $db->fetch_array($result);
        return $data;
    }

    function invoiceItems($number, $id)
    {
        $db = PearDatabase::getInstance();
        $result = $db->pquery($this->invoice_items_query(), array($number, $id));
        $items = array();
        while ($row = $db->fetch_array($result)) {
            array_push($items, $row);
        }
        return $items;
    }

    function modifyData(&$data)
    {
        $data['payer_name'] = $data['contact_name'] ? $data['contact_name'] : $data['account_name'];
        $data['payer_phone'] = $data['contact_phone'] ? $data['contact_phone'] : $data['account_phone'];
        $data['payer_email'] = $data['contact_email'] ? $data['contact_email'] : $data['account_email'];
    }

    function send_already_paid($invoice_data)
    {
        global $invoice_payment_options;
        $smarty = new Vtiger_Viewer();
        $smarty->assign('invoice_data', $invoice_data);
        $smarty->assign('options', $invoice_payment_options);
        $smarty->view('AlreadyPaid.tpl', $this->getName());
        exit(0);
    }

    function send_not_found()
    {
        global $invoice_payment_options;
        $smarty = new Vtiger_Viewer();
        $smarty->assign('options', $invoice_payment_options);
        $smarty->view('NotFound.tpl', $this->getName());
        exit(1);
    }

    function send_show_invoice($invoice_data, $invoice_items)
    {
        global $invoice_payment_options;
        global $invoice_payment_rbk_money;
        $smarty = new Vtiger_Viewer();
        $smarty->assign('invoice_data', $invoice_data);
        $smarty->assign('invoice_items', $invoice_items);
        $smarty->assign('options', $invoice_payment_options);
        $smarty->assign('rbk_money',$invoice_payment_rbk_money);
        $smarty->view('ShowInvoice.tpl', $this->getName());
        exit(0);
    }

    function send_payment_success($invoice_data)
    {
        global $invoice_payment_options;
        $smarty = new Vtiger_Viewer();
        $smarty->assign('invoice_data', $invoice_data);
        $smarty->assign('options', $invoice_payment_options);
        $smarty->view('PaymentSuccess.tpl', $this->getName());
        exit(0);
    }

    function send_payment_fail($invoice_data)
    {
        global $invoice_payment_options;
        $smarty = new Vtiger_Viewer();
        $smarty->assign('invoice_data', $invoice_data);
        $smarty->assign('options', $invoice_payment_options);
        $smarty->view('PaymentFail.tpl', $this->getName());
        exit(0);
    }

    function process($request)
    {
        $number = $this->getNumber($request);
        $id = $this->getId($request);

        $invoice_data = $this->invoiceData($number, $id);

        if (!$invoice_data) {
            $this->send_not_found();
        }

        $payment_result = $request->get('result');

        if ($payment_result == 'success') {
            $this->send_payment_success($invoice_data);
        } elseif ($payment_result == 'fail') {
            $this->send_payment_fail($invoice_data);
        }

        global $invoice_payment_options;

        if ($invoice_data['invoice_status'] == $invoice_payment_options['status_paid']) {
            $this->send_already_paid($invoice_data);
        }

        if (!in_array($invoice_data['invoice_status'], $invoice_payment_options['status_to_pay'])) {
            $this->send_not_found();
        }

        $this->modifyData($invoice_data);
        $invoice_items = $this->invoiceItems($number, $id);
        $this->send_show_invoice($invoice_data, $invoice_items);
    }

    function exitIfDisabled()
    {
        if (!vtlib_isModuleActive($this->getName())) {
            header('HTTP/1.0 403 Forbidden');
            printf("Module '%s' is disabled!", $this->getName());
            exit(1);
        }
    }

    function invoice_data_query()
    {
        return <<<SQL
SELECT

i.invoice_no as invoice_number,
i.total as invoice_total,
i.subject as invoice_subject,
i.invoicestatus as invoice_status,

concat(ifnull(concat(cd.firstname, " "), ""), cd.lastname) as contact_name,
coalesce(cd.phone, cd.mobile, cd.fax) as contact_phone,
coalesce(cd.email, cd.secondaryemail, cd.otheremail) as contact_email,

a.accountname as account_name,
coalesce(a.phone, a.otherphone, a.fax) as account_phone,
coalesce(a.email1, a.email2) as account_email

FROM

vtiger_invoice i
left outer join vtiger_contactdetails cd on i.contactid = cd.contactid
left outer join vtiger_account a on i.accountid = a.accountid

WHERE

i.invoice_no = ?

AND

i.invoiceid = ?
SQL;
    }

    function invoice_items_query()
    {
        return <<<SQL
SELECT

ipr.sequence_no as number,
p.productname as name,
ipr.listprice as price,
ipr.quantity as quantity,
ipr.listprice * ipr.quantity as total

FROM

vtiger_inventoryproductrel ipr
left outer join vtiger_products p on p.productid = ipr.productid
left outer join vtiger_invoice i on ipr.id = i.invoiceid

WHERE

i.invoice_no = ?

AND

i.invoiceid = ?

ORDER BY

ipr.sequence_no
SQL;
    }
}

########################################################

$payment = new InvoicePayment();
$payment->exitIfDisabled();
$request = new Vtiger_Request($_REQUEST);
$payment->process($request);
