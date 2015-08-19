<div class="titl2">
    <h1>Счёт:</h1>
</div>

<table class="bill">
    <tbody>
    <tr height="27" class="head">
        <td class="col6" colspan="2"><h2>Номер: {$invoice_data.invoice_number}</h2></td>
    </tr>
    <tr height="27">
        <td class="col10">Плательщик:</td>
        <td class="col20">{$invoice_data.payer_name}</td>
    </tr>
    <tr height="27">
        <td class="col10">Email:</td>
        <td class="col20">{$invoice_data.payer_email}</td>
    </tr>
    <tr height="27">
        <td class="col10">Телефон:</td>
        <td class="col20">{$invoice_data.payer_phone}</td>
    </tr>
    </tbody>
</table>