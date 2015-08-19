<div class="titl1">
    <h1>Состав заказа:</h1>
</div>

<table class="ord" width="100%">
    <tbody>
    <tr class="head" height="27">
        <td class="col1" colspan="1">Товары (работы, услуги)</td>
        <td class="col2">Цена</td>
        <td class="col3">Кол-во</td>
        <td class="col4">Сумма</td>
    </tr>
    {foreach from=$invoice_items item=item}
        <tr height="70">
            <td class="col15 ">{$item.name}</td>
            <td class="col2">{$item.price|string_format:"%.2f"} руб.</td>
            <td class="col3">{$item.quantity|string_format:"%d"} шт.</td>
            <td class="col4">{$item.total|string_format:"%.2f"} руб.</td>
        </tr>
    {/foreach}
    <tr height="27">
        <td class="summ" colspan="6"><h2>Итого: {$invoice_data.invoice_total|string_format:"%.2f"} руб.</h2></td>
    </tr>
    </tbody>
</table>