var moneda;
var monedaUsr;
var totalCreditos = 0;
var baseUrl = "";
var basePagos = "";

$(document).ready(function ()
{
    baseUrl = document.getElementById("txtBaseUrl").value;
    basePagos = baseUrl + '/OperationController/Pagos/';
    totalCreditos = 0;
    getUserCurrency();
//    fncActualizaTabla();


});

function fncGetCreditos()
{
    var data =
            {
                metodo: 'creditos',
                orden:  (document.getElementById("txtIdOrden"))?document.getElementById("txtIdOrden").value:'',
                tipo:  (document.getElementById("txtTipo"))?document.getElementById("txtTipo").value:'',
                routePointId: (document.getElementById("routePointId"))? document.getElementById("routePointId").value:0
            };

    $.ajax({
        type: 'POST',
        data: data,
        url: basePagos + 'getCreditos',
        dataType: "json",
        success: fncDesabilitarChk,
        error: fncError
    });
}

function fncDesabilitarChk(data)
{
    if(document.getElementById("txtCreditos")){
    	$("#txtCreditosver").html(parseFloat(data.creditos).toFixed(2));
        document.getElementById("txtCreditos").value = parseFloat(data.creditos).toFixed(2);
    }
    if(document.getElementById("txtSaldo")){
    	var saldotext = parseFloat(data.creditos).toFixed(2);
    	$("#txtSaldover").html("$ "+parseFloat(data.creditos/0.181818).toFixed(2));
        document.getElementById("txtSaldo").value = saldotext;
    }
    
    if (document.getElementById("txtIdTipoPago")) {
        if (document.getElementById("txtIdTipoPago").value != 2)
        {
            if(document.getElementById("chkDebitar")){
                document.getElementById("chkDebitar").disabled = true;
            }
            
        }
    }
    
}

function fncValidar()
{
    var result = false;
    var numCreditos = parseFloat(document.getElementById("txtCreditos").value);
    var numTotalCreditos = parseFloat(document.getElementById("txtTotalCreditos").value);
    var saldo = parseFloat(numCreditos - numTotalCreditos).toFixed(2);
    
    document.getElementById("txtSaldo").value = saldo;
	$("#txtSaldover").html(parseFloat(saldo/0.181818).toFixed(2));

    if (numTotalCreditos > numCreditos)
    {
        document.getElementById("txtSaldo").value = numCreditos;
    	$("#txtSaldover").html(parseFloat(numCreditos/0.181818).toFixed(2));
        result = true;
    }
    return result;
}

function fncActualizaTabla(m_data)
{
    var moneda = 1;
    var select = m_data;
    if (select != "")
    {
        moneda = select;
    }
    var data =
            {
                metodo: 'obtenerProductos',
                orden: (document.getElementById("txtIdOrden"))?document.getElementById("txtIdOrden").value:'',
                moneda: moneda
            };

    $.ajax({
        type: 'POST',
        data: data,
        url: basePagos + 'getProductos',
        dataType: "json",
        success: fncCrearTabla,
        error: fncError
    });

}

function getUserCurrency() {

    var data =
            {
                metodo: 'getUserCurrency'
            };

    $.ajax({
        type: 'POST',
        data: data,
        url: basePagos + 'getUserCurrency',
        dataType: "json",
        error: fncError
    }).done(function (data) {
        monedaUsr = data;
        if(document.getElementById("tbdCuerpo")){
            if(document.getElementById("tbfPie")){
                fncActualizaTabla(data);
            }
        }
        fncGetCreditos();
        fncObtenerTipoMoneda();
        fncDeshabilitar();
    });
}

function fncCrearTabla(data)
{
    var tablaBody = document.getElementById("tbdCuerpo");

    var tablaCuerpo = "";

    i = 0;
    for (i in data)
    {
        tablaCuerpo += "<tr>";
        tablaCuerpo += "<td>" + data[i].cantidad + "</td>";
        tablaCuerpo += "<td>" + data[i].producto + "</td>";
        tablaCuerpo += "<td>" + data[i].sku + "</td>";
        tablaCuerpo += "<td>" + parseFloat(data[i].precioUnitario).toFixed(2) + "</td>";
        tablaCuerpo += "<td>" + parseFloat(data[i].precioSubtotal).toFixed(2) + "</td>";
        tablaCuerpo += "</tr>";
        i++;
    }
    tablaBody.innerHTML = tablaCuerpo;
    fncCrearTablaPie(data);
}

function fncCrearTablaPie(data)
{
    var tablaPie = document.getElementById("tbfPie");

    var tablaFoot = "";

    var x = data.length - 1;

    tablaFoot += "<tr>";
    tablaFoot += "<td colspan='2'></td>";
    tablaFoot += "<td colspan='2'>Total en Créditos </td>";
    tablaFoot += "<td>" + parseFloat(data[x].totalCreditos).toFixed(2) + "</td>";
    tablaFoot += "<input type='hidden' id='txtTotalCreditos' value='" + data[x].totalCreditos + "'>";
    tablaFoot += "</tr>";

    tablaFoot += "<tr>";
    tablaFoot += "<td colspan='2'></td>";
    tablaFoot += "<td colspan='2'>Total en Moneda</td>";
    tablaFoot += "<td>" + parseFloat(data[x].totalMonedas).toFixed(2) + "</td>";
    tablaFoot += "<input type='hidden' id='txtTotalMonedas' value='" + data[x].totalMonedas + "'>";
    tablaFoot += "</tr>";

    tablaPie.innerHTML = tablaFoot;

    totalCreditos = data[x].totalCreditos;
    totalMonedas = data[x].totalMonedas;
}

function fncVerificar()
{
    var tipoPago = document.getElementById("txtIdTipoPago").value;
    var formaDebitar = 0;
    //formaDebitar = 0 = DebitarCreditos
    //formaDebitar = 1 = CongelarCreditos
    //formaDebitar = 2 = CreditosNegativos
    switch (tipoPago)
    {
        case "1":
            formaDebitar = (fncValidar() === false) ? 1 : 2;
            if (formaDebitar === 1)
            {
                alert("¡Será congelada la cantidad de créditos que cubran el total de la adquisición!");
            }
            else
            {
                alert("¡Al no contar con créditos suficientes, el pago generará créditos negativos!");
            }
            fncGuardarPago(formaDebitar);
            break;
        case "2":
            if (fncValidar() === true)
            {
                alert("¡Creditos Insuficientes!");
            }
            else
            {
                fncGuardarPago(formaDebitar);
            }
            break;
    }
}
function fncGuardarPago(formaDebitar)
{
    
    var orden = document.getElementById("txtIdOrden").value;
    var data =
            {
                metodo: 'guardarPago',
                orden: orden,
                tipoPago: document.getElementById("txtIdTipoPago").value,
                monto: totalCreditos,
                montoMoneda: totalMonedas,
                moneda: document.getElementById("slcTipoMoneda").value,
                formaDebito: formaDebitar
            };

    $.ajax({
        type: 'POST',
        data: data,
        url: basePagos + 'fncGuardarPago',
        dataType: "json",
        success: fncGuardarSuccess,
        error: fncError
    });

}
function fncGuardarSuccess(data)
{
    alert("¡El registro se realizó con éxito!");
//    document.getElementById("aRegresar").click();
    document.getElementById("btnGuardar").disabled = true;
}

function fncObtenerTipoMoneda()
{
    var data =
            {
                metodo: 'obtenerMonedas'
            };

    $.ajax({
        type: 'POST',
        data: data,
        url: basePagos + 'fncTipoMonedas',
        dataType: "json",
        success: fncObtenerTipoMonedaSuccess,
        error: fncError
    });

}

function fncObtenerTipoMonedaSuccess(data)
{
    var txtMoneda1 = document.getElementById("monedatxt");
    var txtMoneda = document.getElementById("moneda");
    var hdIdMoneda = document.getElementById("slcTipoMoneda");
    i = 0;
    for (i in data)
    {
        if (data[i].id == monedaUsr) {
            hdIdMoneda.value = data[i].id;
            txtMoneda.value = data[i].moneda;
            txtMoneda1.innerHTML = data[i].moneda;
        } 
        i++;
    } 
}

function fncDeshabilitar()
{
    var tipoPago ;
    if(document.getElementById("txtIdTipoPago")){
        tipoPago = document.getElementById("txtIdTipoPago").value;
    }
    if (tipoPago === "3")
    {
        if(document.getElementById("btnGuardar")){
//        //fncGuardarPagoFuera();
        document.getElementById("btnGuardar").style.display = "none";
        fncGuardarPagoFuera();
        }

    }

}

function fncGuardarPagoFuera()
{
    var data =
            {
                metodo: "guardarPago",
                orden: document.getElementById("txtIdOrden").value,
                tipoPago: document.getElementById("txtIdTipoPago").value,
                formaDebito: 4
            };

    $.ajax({
        type: 'POST',
        data: data,
        url: basePagos + 'fncGuardarPagoFuera',
        dataType: "json",
        success: fncGuardarFueraSuccess,
        error: fncError
    });

}
function fncGuardarFueraSuccess(data)
{
    alert("¡El registro se realizó con éxito!");
}

function fncError()
{
    alert("¡Ha ocurrido un error!");
}

function fncActualizaTipoPago(){
    var e = document.getElementById("slcTipoPago");
    var value = e.options[e.selectedIndex].value;
    document.getElementById("txtIdTipoPago").value = value;
    getUserCurrency();
}
        




















