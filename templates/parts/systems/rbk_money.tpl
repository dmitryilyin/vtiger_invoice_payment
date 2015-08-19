<tr>
    <td class="w110b"><img src="{$options.resources_dir}/{$rbk_money.logo}" alt="RBK Money"></td>
    <td class="about"><h3>RBK Money</h3><br>

        <p>Платежная система RBKmoney</p>

        <ul>
            <li>Оплата картой<strong> Visa/MasterCard</strong> российского или зарубежного банка (в рублях)</li>
            <li><strong>Платежные терминалы</strong>&nbsp;Элекснет и другие</li>
            <li>Оплата в салонах связи <strong>Евросеть</strong> или <strong>Связной</strong> (зачисление до 2-х
                часов, комиссия 0%)
            </li>
            <li>Интернет-банкинг (УралСиб, Ocean, ВТБ, otpBank, Екатеринбургский муниципальный банк,
                УралТрансБанк, Банк24)
            </li>
            <li>Электронные деньги RBKmoney</li>
            <li>Платежная система Contact (на территории России)</li>
            <li>Почтовый перевод</li>
            <li>и другие способы оплаты</li>
        </ul>
    </td>
    <td class="w110b">

        <form action="{$rbk_money.apiUrl}" name="pay" method="POST">
            <input type="hidden" name="eshopId" value="{$rbk_money.eshopId}">
            <input type="hidden" name="orderId" value="{$invoice_data.invoice_number}">
            <input type="hidden" name="serviceName" value="Оплата счета номер {$invoice_data.invoice_number}">
            <input type="hidden" name="recipientAmount" value="{$invoice_data.invoice_total|string_format:"%.2f"}">
            <input type="hidden" name="recipientCurrency" value="{$rbk_money.recipientCurrency}">
            <input type="hidden" name="user_email" value="{$invoice_data.payer_email}">
            <input type="hidden" name="successUrl" value="{$rbk_money.returnUrl}?number={$invoice_data.invoice_number}&result=success">
            <input type="hidden" name="failUrl" value="{$rbk_money.returnUrl}?number={$invoice_data.invoice_number}&result=fail">
            <input class="button" type="submit" name="yt0" value="ВЫБРАТЬ">
        </form>

    </td>
</tr>

<html>
<head>
</head>
<body>

</body>
</html>