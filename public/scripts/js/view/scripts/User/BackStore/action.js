/**
 * action.js archivo para accion comunes en BackStoreController
 */
$(document).ready(function(){
   
   initEvents();
   changeStatusOrder();
});

function initEvents()
{
    $('#changeStatusShippingBtn').live('click',function()
    {
        //status = 2: recolectado
        $.post(urlChangeStatusShipping, {'id':orderId, 'status':newStatus}, function(data)
        {
            if(data.indexOf('error') == -1)
            {
                $('#changeStatusShippingBtn').hide();
                $('#statusShipping').val('En ruta');
            }
            $('#divResultShipping').html(data);
        });
    });
}

function changeStatusOrder()
{
    $('#changeStatusOrder').live('change',function()
    {
        var newStatusOrder = $(this).val();
        $.post(urlChangeStatusOrder, {'id':orderId, 'status':newStatusOrder});
    });
    
    
}