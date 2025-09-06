$(document).ready(function () {
    $('.remove').click(function () {
        removeProduct($(this).attr('id'), this);
    });
    $('.item-qty').change(function () {
        var qty = $(this).val();
        var idProduct = $(this).attr('id').split('-')[2];
        if (qty <= 0)
            removeProduct(idProduct, $(this).parent());
        else
            changeQty(idProduct, qty);
    });
    $('#empty-cart').click(function () {
        emptyCart();
    });
    changePointOrigin();
    changeScheduleSelect();
    $('#pointSeller').trigger('change');
});

function changeQty(idProduct, qty)
{
    $.post(urlUpdateToCart, {item_id: idProduct, item_qty: qty}, function (data) {
        data = $.parseJSON(data);
        if (data.result)
        {
            $('.subtotalQty').html('<span style="float:right; margin:8px;"><strong>' + data.subtotal + '</strong></span>')
            $('#total_payment').html(data.subtotal);
            $('#item-precio-' + idProduct).html(data.subtotalProduct);
            var creditos = $('#costoCreditosProducto-' + idProduct).val();
            $('#item-creditos-' + idProduct).text((creditos * qty).toFixed(2));

            var subtotalCreditos = 0;
            $('.item-qty').each(function () {
                var qty = $(this).val();
                var idProduct = $(this).attr('id').split('-')[2];

                var creditos = $('#item-creditos-' + idProduct).text();
                subtotalCreditos = subtotalCreditos + (creditos);

            });
            $('#total_paymentCreditos').html(Number.parseFloat(subtotalCreditos).toFixed(2));
        }
    });



}
function removeProduct(idProduct, element)
{
    $.post(urlRemoveFromCart, {item_id: idProduct}, function (data) {
        data = $.parseJSON(data);
        if (data.result)
        {
            $(element).parent('td').parent('tr').remove();
            $('.subtotalQty').html('<span><strong>' + data.subtotal + '</strong></span>');
            $('#total_payment').html(data.subtotal);

        }
    });
}
function emptyCart()
{
    if (confirm('Deseas vaciar el carrito de compra')) {
        $.post(urlEmptyCart, {}, function (data) {
            $('#shopping_details').html('<div class="ui-widget"><div style="padding:1em;" class="ui-widget-content ui-corner-all">El pedido se realizará sin productos</div><div class="clear">&nbsp;</div>');
            $('#shopping_details1').html('<div class="ui-widget"><div class="clear">&nbsp;</div>');
            $('#shopping_details2').html('<div class="ui-widget"><div class="clear">&nbsp;</div>');
            $('#shopping_details3').html('<div class="ui-widget"><div class="clear">&nbsp;</div>');
        });
    }
}
function changePointOrigin()
{
    $('#pointSeller').change(function () {
        var originPoint = $(this).val();
        $.post(urlGetSchedules, {originPoint: originPoint}, function (data) {
            $('#boxSchedules').html(data);
            $('#shippingDateInformation').html('<div class="alert">Seleccione la fecha de recolecci&oacute;n</div>');
        });
    });
}

function changeScheduleSelect()
{
    $('#boxSchedules, #pointBuyer').change(function () {
        var idSchedule = $('#boxSchedules').val();
        var pointSeller = $('#pointSeller').val();
        var pointBuyer = $('#pointBuyer').val();
        var selectedScheduledDate = $("#boxSchedules option:selected").text();
        if (idSchedule > 0)
        {
            $.post(urlSelectSchedule, {'idRoute': idSchedule, 'idSeller': idSeller, 'pointBuyer': pointBuyer, pointSeller: pointSeller, selectedScheduledDate: selectedScheduledDate},
                    function (data) {
                        if (data.res)
                        {
                            $('#shippingDate').val(data.date);
                        } else
                        {
                            $('#shippingDate').val('');
                        }
                        $('#shippingDateInformation').html('<div class="alert">' + data.message + '</div>');
                    }, 'json');
        } else
            $('#shippingDateInformation').html('<div class="alert">Seleccione la fecha de recolecci&oacute;n</div>');
    });

}

