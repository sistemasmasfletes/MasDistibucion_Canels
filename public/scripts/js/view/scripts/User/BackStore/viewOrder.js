var baseUrl = "";
var basePagos = "";
$(document).ready(function()
{
    baseUrl = document.getElementById("txtBaseUrl").value;
    basePagos = baseUrl + '/OperationController/Pagos/';
    $('span').addClass('span2');
    $('#statusPaid').change(function(){
        var newStatus = $(this).val();
        $.post(urlUpdateStatusPaid, {
            status : newStatus
        }, function(data){

            },'json');
    });
    $('#statusShipped').change(function(){
        var newStatus = $(this).val();
        $.post(urlUpdateStatusShipped, {
            status : newStatus
        }, function(data){

            },'json');
    });
    fncExistePago();
});

function fncExistePago()
{
    var data = 
    {
        metodo: 'existePago',
        orden: document.getElementById("txtIdOrden").value
    };

    $.ajax({
        type: 'POST',
        data: data,
        url: basePagos + 'fncExistePago',
        dataType: "json",
        success: fncExistePagoSuccess,
        error: fncError
    });
}

function fncExistePagoSuccess(data)
{
    var div = document.getElementById("divTipoPago");
    var divCuerpo = "";
    if(data["id"] !== null)
    {
        divCuerpo += "<input type='text' id='txtEstatusTipoPago' value='"+data["tipoPago"]+"' disabled='true' >";
        div.innerHTML = divCuerpo;
    } 
}
function fncError()
{
    alert("¡Ocurrio un error al cargar los registros!");
}