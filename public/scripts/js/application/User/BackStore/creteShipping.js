/* global sessvars */

sumFactors = 0;
var packagesLoaded=[];
var promotionsLoaded=[];
var packagesSelected=[];
var selectedPackageid=null;
var selectedPromo={};
var costingConfig = {};
var ratesByRoutePoint= [];
var hasDeliveryDate=false;
var lTableRate=[];
var lRoutesFromPoints = [];
var lRoutes = [];
var lRoutePoints = [];

var opcion = 0;
var baseUrl = "";
var basePagos = "";
$(document).ready(function(){
    changeTotalPrice();
    totalTime();
    addPackage();
    deletePackage();
    beforehSubmit();
    changePointOrigin();
    postForm();
    $('#pointSeller').trigger('change');
    baseUrl = document.getElementById("urlBase").value;
    basePagos = baseUrl + '/OperationController/CompraCreditos/';

    loadPackageCatalog();  
    loadProducts();
    
    openModalPromotion();
});

function deletePackage()
{
    var id = $('#idClient').val();
    $('.deletePackage').live('click',function()
    {        
        var currentRow = $(this).parent()/*td*/.parent()/*tr*/;
        var currentRowTd = currentRow.children(0)[0]/*td*/

        var packageId = parseInt($(currentRowTd).find('.pkgid').val());       
        for(var i=0;i<packagesSelected.length;i++){            
            if(packagesSelected[i].id==packageId){
                packagesSelected.splice(i,1);
                break;
            }
        }    
        
        
        $(currentRow).remove();       

        changeUnitarys();
        calculaTotal();
        updateShippingDetailCost();

        return false; 
    })
}

function addPackage()
{ 
    var id = $('#idClient').val();
    $('#addPackage').live('click',function()
    {
        $('#divAdd').dialog('open');

        $.post(urlConsultPackage,{
            'id': id
        },function(data){
            $('#divAdd').html(data);
            inputPackageCatalog();
        });

        return false;

    });
    
        
    $('#divAdd').dialog({
        autoOpen: false,
        modal:true,
        resizable:false,
        buttons: {
            Aceptar:
            {
                text:'Accept',
                click: function()
                {
                    peso = $('#peso').val();
                    alto = $('#alto').val();
                    ancho = $('#ancho').val();
                    profundidad = $('#profundidad').val();
                    nombre = $('#nombre').val();
                    $.post(urlSavePackage,{
                        'id':id,
                        'peso':peso,
                        'alto':alto,
                        'ancho':ancho,
                        'profundidad':profundidad,
                        'nombre':nombre
                    },
                    function(data){
                        $('#tablePackage1').html(data);
                        $('#divAdd').dialog('close');
                        changeUnitarys();
                    })
                }                    
            }
        },
        closeText: 'hide',
        width:450
    });
     
    
}

function totalTime()
{
    $('#boxSchedules, #pointBuyer').change(function(){
        var idRoute = $('#boxSchedules').val();
        pointOrigin = $('#pointSeller').val();
        pointDestiny  = $('#pointBuyer').val();
        tvol = $('#tvol').val();
        tweight = $('#tweight').val();

        if($('#tablePackageBody > tr').length == 0){
            Masdist.createMessagebox('Atenci?n','No se puede calcular la ruta, seleccione los paquetes a enviar.',null,null);
            $('#boxSchedules > option[value="0"]').attr('selected', 'selected');
	        return false;
	    }
        
        var selectedScheduledDate = $("#boxSchedules option:selected").text().substring(0,19);
        
        if(idRoute > 0)
        {
            $.post(urlCalculateShippingTime, {
                idRoute : idRoute,
                buyerId : buyerId,
                orderId : orderId,
                pointSeller:pointOrigin,
                pointBuyer :pointDestiny,
                selectedScheduledDate: selectedScheduledDate,
                tvol: tvol,
                tweight: tweight
            }, function(data){
                if(data.res)
                {
                    $('#shippingDate').val(data.date);
                    costingConfig = data.costingConfig;
                    ratesByRoutePoint = data.ratesByRoutePoint;

                    hasDeliveryDate = costingConfig.hasDeliveryDate;
                    lTableRate = data.tableRateActivities;
                }
                else
                {
                    $('#shippingDate').val('');

                    hasDeliveryDate=false;
                    updateTotalList();
                    lTableRate = [];
                }                
                /* Se suma el factor de las rutas*/
                sumFactors = 0;
                $.each(data.factors, function(key, value) { 
                  sumFactors += value.factor; 
                });
                
                createShippingDetail(lTableRate,data.res,data.message);                
                changeUnitarys();
            },'json');
        }
        else{            
            clearShippingInfo(false,'Seleccione la fecha de recolecci&oacute;n');
            updateTotalList();
        }
    });
}

function clearShippingInfo(lhasDelivery,lMessage){
    $('#shippingActivities').html('');
    $('#shippingTable').hide();
    $('#infoDate').html('');    
    $('#infoDate').html(lMessage);   
}

function filterArrayByProp(lArray,lProp,lValue){
    resultArray = [];
    resultArray = lArray.filter(function(item,index){
        if(parseInt(item[lProp])==lValue)
            return item;
    });

    return resultArray;
}

function createShippingDetail(lTableRate,lhasDelivery,lMessage){
    var markup='';
    // element_id,element_type,pv,activityType,ptype,shippingDate,client_rate,elementName
    clearShippingInfo(lhasDelivery,lMessage)
    if(!lhasDelivery) return;
    $('#shippingTable').show();
    lRoutePoints = filterArrayByProp(lTableRate,"element_type",2);

    lRoutesFromPoints = filterArrayByProp(lTableRate,"element_type",1);

    var lenPoints = lRoutePoints.length;
    var prevRouteId = -1;
    var prevPointType = -1
    var prevActivityType = -1
    var currPoint = {};
    var currRoute = {};
    var activityType = 0;
    var activity = ""
    var pointType = "";
    var activityDetail = ";"
    var esCambioRuta = false
    var esCentroIntercambio = false
    var esRecoleccion=1
    var esEntrega=2

    for(var i=0;i<lenPoints;i++){
        currPoint = lRoutePoints[i];
        currRoute = lRoutesFromPoints.filter(function(item,index){
            return item.routeId == currPoint.routeId;
        })[0];


        activityType = parseInt(currPoint.activityType);
        activity = activityType == 1 ? "Recolección" : (activityType == 2 ? "Entrega " : "");
        pointType = currPoint.ptype == 1 ? "Punto de venta" : "Centro de Intercambio" ;

        // Encabezado de Rutas
        esCentroIntercambio = (currPoint.ptype==2)
        esCambioRuta = (esCentroIntercambio && prevPointType==currPoint.ptype && prevActivityType==esEntrega && activityType==esRecoleccion)
        if( esCambioRuta || i==0){
            markup += '<tr style="border-bottom:1px dotted #ccc">\n\
            <td colSpan="2" style="font-weight:bold; padding:5px 0px 0px 0px">'+currRoute.elementName+'</td>\n\
            <td class="activityCost" style="text-align:right;padding-right:10px"></td>\n\
            <td style="text-align:right">\n\
                <input type="hidden" class="routeId" value="'+currRoute.element_id+'">\n\
                <span class="activityCost"></span>\n\
            </td>\n\
            </tr>';
        }

        // Primer y ?ltimo punto (Recolecci?n inicial y Entrega final)
        if(i==0||i+1==lenPoints) {
            markup += '<tr style="border-bottom:1px dotted #ccc">\n\
            <td style="padding:1px 10px 1px 5px">'+ currPoint.shippingDate +'</td>\n\
            <td>'+ activity + ' ' +  currPoint.elementName +'</td>';
        }else{
        //Puntos intermedios
            activityDetail = activity + ' ' + pointType + ' '  +  currPoint.elementName;
            markup += '<tr style="border-bottom:1px dotted #ccc">\n\
            <td style="padding:1px 10px 1px 5px">'+currPoint.shippingDate +'</td>\n\
            <td>'+ activityDetail + '</td>';                   
        }
        //Columna de costos
        markup+='<td class="activityCost" style="text-align:right;padding-right:10px"></td>\n\
                 <td style="text-align:right">\n\
                    <input type="hidden" class="rpId" value="'+currPoint.pv+'">\n\
                    <span class="activityCost"></span>\n\
                 </td>\n\
                 </tr>';

        prevPointType = currPoint.ptype;
        prevActivityType = activityType;
    }
	$('#shippingActivities').html(markup); 
    updateShippingDetailCost();
}

function setCostActivity(arrRouteCosts,arrPointCosts){
    var spanRoute = null;
    var spanPv = null;
    var currRouteId = null;
    var currPointId = null;
    var inputRoute = null;
    var inputPoint = null;
    var currentTotalRate = null;
    var currenRate =  null;

    if ($.isArray(arrRouteCosts) && arrRouteCosts.length>0 && $.isArray(arrPointCosts) && arrPointCosts.length>0  ){        
        for(var i=0;i<arrRouteCosts.length;i++){
            currRouteId = arrRouteCosts[i].element_id;
            currenRate = arrRouteCosts[i].client_rate ? arrRouteCosts[i].client_rate : '-';
            currentTotalRate = arrRouteCosts[i].totalRate ? arrRouteCosts[i].totalRate : '-';
            inputRoute = $("input.routeId[value='" + currRouteId + "']");
            $(inputRoute).each(function(){
                spanRoute = $(this).parent().children(0)[1];
                $(spanRoute).html(currentTotalRate);
                $(this).parent().prev().html(currenRate);
            })            
        }

        for(var i=0;i<arrPointCosts.length;i++){
            currPointId = arrPointCosts[i].pv;
            currenRate = arrPointCosts[i].client_rate ? arrPointCosts[i].client_rate : '-';
            currentTotalRate = arrPointCosts[i].totalRate ? arrPointCosts[i].totalRate : '-';
            inputPoint = $("input.rpId[value='" + currPointId + "']");
            spanPv = $(inputPoint.parent().children(0)[1]);
            spanPv.html(currentTotalRate);
            $(inputPoint.parent()).prev().html(currenRate);
        }
    }else{
        $('.routeId').each(function(){
            spanRoute = $(this).parent().children(0)[1];
            $(spanRoute).html(' - ');
        });
        $('.rpId').each(function(){            
            spanPv=$(this).parent().children(0)[1];
            $(spanPv).html(' - ');            
        });
    }
    $('.activityCost').number(true,2);
}

function updateShippingDetailCost(){

    if(!($.isArray(lTableRate) && lTableRate.length>0)) return;

    var lenPoints = lRoutePoints.length;

    var currPoint = {};    
    var sumRoute = null;
    var prevRouteId = -1;   
    var lRoutes = [];
   
    var currRate = 0;
    var lenRoutes = lRoutesFromPoints.length;
    var lenPackages = packagesSelected.length;
    var currPackage = {};

    var pWidth = 0;
    var pHeight = 0;
    var pDepth = 0;
    var pSize = 0;
    var pUnity = 0;
    var curRatePackage = 0;
    var sumRatePackage = 0;
    var subTotalRate = 0;
    var powerFactor = 0;

    /*==================  Costeo x Ruta y Punto de venta   ==================       
        Se hace uso del Array de paquetes, cargado en memoria.
        Asimismo, de la configuración de paquetes.
    */

    //Cruza entre rutas y paquetes
    for(var i=0;i<lenRoutes;i++){
        currRate = lRoutesFromPoints[i].client_rate;
        sumRatePackage = 0;
        if(!(currRate && currRate>0)){
            lRoutePoints[i]["totalRate"]=null;
            continue;  
        }
        for(var j=0;j<lenPackages;j++){
            currPackage = packagesSelected[j];
            pWidth = currPackage.width;
            pHeight = currPackage.height;
            pDepth = currPackage.depth;
            pUnity = currPackage.unity
            pSize = pWidth * pHeight * pDepth;
            subTotalRate = 0;

            if(pSize<=costingConfig.basePackageSize)
                subTotalRate+=currRate
            else
                subTotalRate+=currRate + calculateCostingFormula(pSize,costingConfig.basePackageSize,5,costingConfig.powerFactor);
            
            subTotalRate = subTotalRate * pUnity;
            sumRatePackage += subTotalRate;
        }
        lRoutesFromPoints[i]["totalRate"] = roundTo(sumRatePackage,2);
    }

    //Cruza entre puntos y paquetes
    for(var i=0;i<lenPoints;i++){
        currRate = lRoutePoints[i].client_rate;
        sumRatePackage = 0;
        if(!(currRate && currRate>0)){
            lRoutePoints[i]["totalRate"]=null;
            continue;  
        } 
        for(var j=0;j<lenPackages;j++){            
            currPackage = packagesSelected[j];
            pWidth = currPackage.width;
            pHeight = currPackage.height;
            pDepth = currPackage.depth;
            pUnity = currPackage.unity
            pSize = pWidth * pHeight * pDepth;
            subTotalRate = 0;

            if(pSize<=costingConfig.basePackageSize)
                subTotalRate+=currRate
            else{
                powerFactor = calculateCostingFormula(pSize,costingConfig.basePackageSize,5,costingConfig.powerFactor);
                subTotalRate+=currRate + powerFactor;
            }
            subTotalRate = subTotalRate * pUnity;
            sumRatePackage += subTotalRate;
        }
        lRoutePoints[i]["totalRate"]=roundTo(sumRatePackage,2);
    }   
    setCostActivity(lRoutesFromPoints,lRoutePoints);
}

function calculateCostingFormula(lpackageSize,lbasePackageSize,ldivide,lpower){
    if(lbasePackageSize && lbasePackageSize>0 && lpower && ldivide>0)
        return roundTo(Math.pow((roundTo((lpackageSize/lbasePackageSize)/ldivide,2)),lpower),2)
    else
        return 0.00;
}

function changeTotalPrice(){

    $('.boxunity').live('change',function(){
        var unidades = $(this).val();
        var total;
        var unitaryPrice = 0;
        
        var currentRow = $(this).parent()/*td*/.parent()/*tr*/;        
        var currentRowTd = currentRow.children(0)[0]/*td*/

        var packageId = parseInt($(currentRowTd).find('.pkgid').val());
        var currentPackage = {};

        for(var i=0;i<packagesSelected.length;i++){
            if(packagesSelected[i].id==packageId){
                currentPackage = packagesSelected[i];
                break;
            }
        }

        if(currentPackage){
            var vol = currentPackage.width*currentPackage.height*currentPackage.depth;
            var costingPerPackage = {};
            if(unidades>0){
                costingPerPackage = calculateCostingPackage(costingConfig,vol,unidades);
                currentPromoCost = (costingConfig.promotionCosting ? costingConfig.promotionCosting : 0) * (currentPackage.promotionid ? currentPackage.promotionNumResources : 0);
                total = costingPerPackage + currentPromoCost;
                unitaryPrice = calculateCostingPackage(costingConfig,vol,1) + currentPromoCost;
            }else
                total = 0;

            $('#total'+currentPackage.id).val(total);
            $('#totalc'+currentPackage.id).val(total/0.182);
            $('#totalcver'+currentPackage.id).html(parseFloat(total/0.182).toFixed(2));
            $('#packagePrice'+currentPackage.id).val(unitaryPrice);
            $('#packagePricever'+currentPackage.id).html(parseFloat(unitaryPrice).toFixed(2));

            packagesSelected[i].unity=unidades;
            packagesSelected[i].total=total;
            packagesSelected[i].packagePrice=unitaryPrice;
        }

        calculaTotal();
        updateShippingDetailCost();
    });
}

function calculateCostingPackage(config,packageSize,unity){
    var resultCosting = 0;
    if(config && config.hasDeliveryDate && config.basePackageSize && config.powerFactor){
        if(config.totalRoutePoint>0){
            var basePackageSize = roundTo((packageSize/config.basePackageSize)/5,2);
            var basePower = roundTo(Math.pow(basePackageSize,config.powerFactor),2);

            var powerByCountRates = basePower * config.totalRoutePoint;
            if(packageSize<=config.basePackageSize)
                resultCosting = (config.totalAmount)*unity;
            else
                resultCosting = (powerByCountRates + config.totalAmount)*unity;
            
        }
    }
    return resultCosting;
}


function formatMoney(number, places, symbol, thousand, decimal) {
    number = number || 0;
    places = !isNaN(places = Math.abs(places)) ? places : 2;
    symbol = symbol !== undefined ? symbol : "$";
    thousand = thousand || ",";
    decimal = decimal || ".";
    var negative = number < 0 ? "-" : "",
        i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
}


function calculaTotal()
{
    var sum = 0;
    var all = $('.totalBoxUnity').each(function(){
        sum += Number($(this).val());
    });
    //$('#sumTotal').html(formatMoney(sum,2));
    $('#sumTotal').html(sum.toPrecision(4));
    $('#sumcTotal').html(formatMoney((sum/0.182),2,'$'));
    //$('#sumTotal').html(sum);
    $('#sumTotal').css('color','green')
    total=sum;
}
/**
 * Antes de hacer el sumbit debe verificar que se haya seleccionado fecha de recoleccion.
 */
function beforehSubmit()
{
   
    $('#submitButton').live('click',function(event)
    {
        var total=$('#sumTotal').text().replace('$','');
        var creditos=$('#ClientCreditos').val().replace(',','');
        var fecha=parseInt($('#boxSchedules').val());
        var replace = parseInt(total);
        var saldo=parseFloat(creditos)-parseFloat(total);
        
        if(fecha === 0 || replace === 0 || $('#shippingDate').val().toString()==='' || parseFloat(creditos)===0.00 || parseFloat(creditos)<parseFloat(total) || saldo<=1)
        {
            event.preventDefault();
            verificar();
        }
    });
}
 
function verificar()
{
        var total=$('#sumTotal').text().replace('$','');
        var creditos=$('#ClientCreditos').val().replace(',','');
        var fecha=parseInt($('#boxSchedules').val());
        var replace = parseInt(total);
        var validacion = false;
        var saldo=parseFloat(creditos)-parseFloat(total);
        var shippingDate =  $('#shippingDate').val().toString();
        $('#submitButton').attr('disabled','disabled');
        if(fecha===0 || replace === 0 || shippingDate == '' || $('#contentdesc').val() == '' || $('#contactsend').val() == '' || $('#contactres').val() == '')
        {
           // event.preventDefault();
            if(fecha===0 || $('#contentdesc').val() == '' || $('#contactsend').val() == '' || $('#contactres').val() == '')
            {                
                Masdist.createMessagebox('Atención','Los datos: Fecha de recolección, Descripcion de contenido, Contacto para recoleccion y Contacto para entrega son obligatorios, por favor ingrese información valida.',null,null);
                $('#submitButton').removeAttr('disabled');
            }
            else 
            {       
                if($('#shippingDate').val().toString()==='')
                {
                    Masdist.createMessagebox('Atenci?n','No se puede generar el pedido, ya que no se ha calculado correctamente la hora de recoleccion y/o entrega.',null,null);
                    $('#submitButton').removeAttr('disabled');
                }
                else
                {
                    Masdist.createMessagebox('Atenci?n','No se puede generar el pedido, ya que no se ha seleccionado ningun paquete.',null,null);
                    $('#submitButton').removeAttr('disabled');
                }
            }
            validacion=false;
        }
        else
        {
            if(parseFloat(creditos)===0.00)
            {
                Masdist.createMessagebox('Atenci?n','?Usted no cuenta con saldo en cr?ditos! Es necesario que proceda a comprar cr?ditos.',null,null);
                $('#submitButton').removeAttr('disabled');
                setSession(total,creditos);
            }
            else if(parseFloat(creditos)<parseFloat(total))
            {
                var btns = {
                        close: {
                            text:'Aceptar',
                            click: function(){                            
                                $(this).dialog('close');
                                setSession(total,creditos);
                            }
                        },
                        cancelar: {
                            text:'Cancelar',
                            click: function(){                            
                                $(this).dialog('close');
                                anularSession();
                            }
                        }
                    }
                Masdist.createMessagebox('Atenci?n','?Usted no cuenta con los cr?ditos suficientes!\n\n Seleccione Aceptar si desea comprar cr?ditos o Cancelar si desea posponer el envio',btns,null);
                $('#submitButton').removeAttr('disabled');
            } 
            else if(saldo<=1)
            {                
                Masdist.createMessagebox('Atenci?n','?No se puede realizar el proceso, debido a que el saldo para operar es insuficiente!.',null,null);
                $('#submitButton').removeAttr('disabled');
            }
            validacion=true;
        } 
        return validacion;
}

function postForm()
{
    successPostForm = function(data,statusText,form){
        var message = '';

        var btns = {
                close: {
                    text:'Cerrar',
                    click: function(){                            
                        $(this).dialog('close');
                        if(data.redirect)
                            window.location.href = data.redirect;
                    }
                }
            }

        if(data && data.numTypeResponse>=0){
            switch(data.numTypeResponse){
                case 0:                
                    if(data.success){
                        Masdist.createMessagebox('Aviso','Orden creada con éxito.',btns,{});
                    }else{
                        Masdist.createMessagebox('Aviso','No se pudo crear la orden. Favor de contactar al Administrador del Sistema.',null,{});
                    }

               
                break;
                
                //Cambio en parámetros de cálculo de flete
                case 1: 
                    if(data.message) message = data.message;
                    else message ("Ocurrió un error en el servidor. Por favor actualice la página y si el problema persiste contacte al Administrador del Sistema.");
                    Masdist.createMessagebox('Aviso',message,null,{});
                    costingConfig = data.costingConfig;
                    changeUnitarys();
                break;
                
                //Se generó más de 1 orden
                case 2:
                    var markup = '<p>Se creó una nueva orden de compra a partir del segundo paquete seleccionado.</p>';
                    markup +='<table class="table">\n\
                                <thead>\n\
                                    <tr><th># Orden</th><th style="text-align:center">Cantidad</th><th style="text-align:center">Paquete</th><th>P. Unitario</th><th style="text-align:center">Importe</th></tr>\n\
                                </thead>\n\
                                <tbody>';

                    var arrNewOrders = data.newOrders;
                    var currOrder = {};
                    for(var i=0;i<arrNewOrders.length;i++){
                        currOrder = arrNewOrders[i];
                        markup+='<tr>\n\
                                    <td>\n\
                                        <a href="'+currOrder.viewOrder+'" target="_blank" style="font-weight:bold; padding:2px">\n\
                                        ' + currOrder.orden + '<span class="ui-icon ui-icon-extlink" style="float:right; "></span></a>\n\
                                    </td>\n\
                                <td style="text-align:right">' + currOrder.cantidad + '</td>\n\
                                <td style="text-align:left">' + currOrder.paquete + '</td>\n\
                                <td style="text-align:right">' + currOrder.punitario + '</td>\n\
                                <td style="text-align:right">' + currOrder.importe + '</td></tr>';
                    }
                    markup+= '</tbody>\n\
                                <tfoot>\n\
                                </tfoot>\n\
                            </table>';

                    Masdist.createMessagebox('Atención. Nuevas órdenes de compra',markup,btns,{width:600, height:300});

                break;
            }
        }        
    }

    var opciones= {
        beforeSerialize: function($form, options) {
                $('.boxunity').prop('disabled',false);
        },      
        beforeSubmit: function(arr, $form, options){
            //Validar datos
            if(!verificar()) return false;
            //Agregar al POST, nuevas Variables obligatorias
            arr.push({condesc:$('#contentdesc').val(), contactS:$('#contactsend').val(), contactR:$('#contactres').val()})

            var ob = {}
            //Quitar formateo de valores numéricos.
            for(var i=0;i<arr.length;i++){
                ob = arr[i];
                if(ob.name=='packagePrice[]'||ob.name=='total[]')
                    ob.value=ob.value.replace(',','');
            }

            //Agregar al POST, las variables utilizadas en el cálculo del costeo, para comprobación posterior de integridad
            for(var prop in costingConfig){
                if(!costingConfig.hasOwnProperty(prop)) continue;

                if(prop!='hasDeliveryDate' && prop!='hasFullRatesCaptured')
                    arr.push({name:prop, value:costingConfig[prop]})
            }
            
                      
        },        
        success: successPostForm,
        dataType:'json'
    };


    $('#formPackages').ajaxForm(opciones,costingConfig);
}

/**
 * Cambia el precio de los unitarios
 */
function changeUnitarys()
{
    $('.totalBoxUnity').number(true,2);
    $('.totalcBoxUnity').number(true,2);
    $('.packagePrice').number(true,2);    
    $('.boxunity').trigger('change', ['change', 'Event']);
}

function changePointOrigin()
{
    $('#pointSeller').change(function(){
       var originPoint = $(this).val();
        $.post(urlGetSchedules, {originPoint:originPoint,orderId:orderId}, function(data){
            $('#boxSchedules').html(data);            
            clearShippingInfo(false,'Seleccione la fecha de recolecci&oacute;n');
            updateTotalList();
        });
    });
}

function setSession(total, creditos)
{
   $.ajax({
        type: 'POST',
        data: {
            metodo: 'enviarParametros',
            creditos: creditos,
            total: total  
        },
        url: basePagos + '/recibiParametros',
        dataType: "json",
        success: getSession
    }); 
}
function anularSession()
{
   $.ajax({
        type: 'POST',
        data: {
            metodo: 'anular'
        },
        url: basePagos + '/anular',
        dataType: "json",
    }); 
}
function getSession()
{  
    window.location= baseUrl + "/App/#!/compraCreditos";
}

function loadPackageCatalog(){
   /* $('#packageText').live('keyup',function()
    {*/
       /* delaySearch(function(){
            var vpackage = $('#packageText').val();

            if(vpackage.length == 0){
                $('#suggestedUsers').hide();
            }else{*/
	
				var vpackage = "";
                $.post(urlSearchPackage, 
                    {package:vpackage, userId:currentUser, sellerId:sellerId, buyerId:buyerId/*variable al inicio del documento*/}, 
                    function(data){                
                        if(data && $.isArray(data) && data.length > 0)
                        {
                            packagesLoaded = data;

                            $('#suggestedUsers').show();
                            $('#suggestedUsersList').html('');
                            
                            var markup = '';
                            var markup1 = '<option  style="background-color:#FFEFD5; color:#CD853F">Seleccione...</option>';
                            for(i=0;i<data.length;i++){
                                markup+=
                                '<div class="userFinalSuggestList" > \n\
                                    <div>'+ data[i].name + '(' + data[i].width + 'x' + data[i].height + 'x' + data[i].depth + ' )' + '</div> \n\
                                    <input class="packageId" type="hidden" value="'+ data[i].id +'" /> \n\
                                </div>';
                                
                                markup1+='<option style="background-color:#FFEFD5; color:#CD853F" value="'+ data[i].id +'">'+ data[i].name + '(' + data[i].width + 'x' + data[i].height + 'x' + data[i].depth + ' )' + '</option>';
                                        //<input class="packageId" type="hidden" value="'+ data[i].id +'" /> \n\

                                /*markup+=
                                    '<div class="userFinalSuggestList" > \n\
                                        <div>'+ data[i].name + '(' + data[i].width + 'x' + data[i].height + 'x' + data[i].depth +', $'+ data[i].price + ' )' + '</div> \n\
                                        <input class="packageId" type="hidden" value="'+ data[i].id +'" /> \n\
                                    </div>';
                                $()*/
                            }
                            //alert(markup1);
                            //$('#suggestedUsersList').html(markup);
                            $('#suggestedUsersListS').html(markup1);
                            selectPackage();
                        }
                        else
                        {
                            $('#suggestedUsers').hide('slow');
                        }
                    },
                    'json'
                );
        /*    }
        },500);
        
    //});*/
}

function selectPackage(){
    $('#suggestedUsersListS').change(function(){

        var elpkgId = $(this).val();
        var pkgId = parseInt(elpkgId);
        var oPackage = {}

    	if($('#selgroup').val() == 1){
    		packagesSelected.length = 0;	
    	}
        
        for(var i=0;i<packagesLoaded.length;i++){
        	
            if(packagesLoaded[i].id == pkgId){
                oPackage = packagesLoaded[i];
                break;
            }
        }
        
        addPackageToCollection(oPackage);
        $('#suggestedUsers').hide();
        $('#packageText').val('');
    });
	
	/*$('.userFinalSuggestList').click(function(){

        var elpkgId = $(this).find('.packageId');
        var pkgId = parseInt(elpkgId.val());
        var oPackage = {}

        for(var i=0;i<packagesLoaded.length;i++){
            if(packagesLoaded[i].id == pkgId){
                oPackage = packagesLoaded[i];
                break;
            }
        }

        addPackageToCollection(oPackage);
        
        $('#suggestedUsers').hide();
        $('#packageText').val('');
        
    });*/
}

function addPackageToCollection(opackage){
    var found = false;
    
    if(!opackage || !opackage.id) return;

	for(var i=0;i<packagesSelected.length;i++){
        if(packagesSelected[i].id==opackage.id){
           found = true;
           break;
        }
    }

    if(!found){
    	packagesSelected.push(opackage);
    	addSelectedPackagesToTable();
    }
}

function addSelectedPackagesToTable(){
    $('#tablePackageBody').html('');
    var markup='';
    var hasPromo = false;
    var tdPromoMarkup = '';
    var promoname = '';
    var tvol=0;
    var tweight=0;

    for(var i=0;i<packagesSelected.length;i++){
        var pkg = packagesSelected[i];
        //Preseleccionar 1 unidad de paquete
        pkg.unity = pkg.unity ? pkg.unity : 1;        
        hasPromo = pkg.promotionid ? true : false;

        if(hasPromo){
            promoname = pkg.promotionName
            tdPromoMarkup = '<span title="'+promoname+'">'+ (promoname.length>17 ? promoname.substring(0,16) +'...' : promoname) + '</span><br><a class="btn removepromo" title="Quitar promoción"><img src="'+baseUrl+'/images/iconos/icono-promocion-quitar.png"/></a>';
        }else{
            tdPromoMarkup = '<a class="btn promo" title="Agregar promoción"><img src="'+baseUrl+'/images/iconos/icono-promocion-agregar.png"/></a>';
        }

        pkg.weight = (pkg.weight == null)? 0 : pkg.weight;
        
        tvol = tvol + (parseFloat(pkg.width)*parseFloat(pkg.height)*parseFloat(pkg.depth))*pkg.unity;
        tweight = tweight + (parseFloat(pkg.weight))*pkg.unity;
        
        markup+=
        '<tr style="padding:0; vertical-align:middle">\n\
                <td>'+pkg.name+'\n\
                    <input type="hidden" class="pkgid" name="idPackage[]" value="'+pkg.id+'">\n\
                    <input type="hidden" name="promotionid[]" value="'+ (hasPromo ? pkg.promotionid : -1) +'">\n\
                </td>\n\
                <td style="text-align:center">'+pkg.unity+'</td>\n\
                <td style="text-align:center">'+pkg.width +' x '+ pkg.height +' x '+ pkg.depth+'</td>\n\
                <td style="text-align:center">'+pkg.weight+createComboUnity(pkg,30)+'</td>\n\
                <!-- td style="text-align:center">'+/*createComboUnity(pkg,30)+*/'</td-->\n\
                <!-- td style="text-align:center"><input type="text" name="packagePrice[]" id="packagePrice'+pkg.id+'" class="packagePrice" value="'+(pkg.packagePrice ? pkg.packagePrice : '0.00')+'" readonly="readonly" style="width:60px; color:green;" /></td-->\n\
                <td style="text-align:center; color:green;">'
                +'<span id="packagePricever'+pkg.id+'">'+(pkg.total ? pkg.total:'0.00')+'</span>'
                +'<input type="hidden" name="packagePrice[]" id="packagePrice'+pkg.id+'" value="'+(pkg.packagePrice ? pkg.packagePrice : '0.00')+'" class="packagePrice"/>'
                +'<input type="hidden" name="total[]" id="total'+pkg.id+'" class="totalBoxUnity span1" value="'+(pkg.total ? pkg.total:'0.00')+'" />'
                +'</td>\n\
				<td style="text-align:center">'
                +'<span id="totalcver'+pkg.id+'">'+(pkg.total ? (parseFloat(pkg.total)/0.182):'0.00')+'</span>'
                +'<input type="hidden" name="totalc[]" id="totalc'+pkg.id+'" class="totalcBoxUnity span1" value="'+(pkg.total ? (parseFloat(pkg.total)/0.182):'0.00')+'" />'
                +'</td>\n\
                <td style="text-align:center">'+ tdPromoMarkup  +' </td>\n\
                <td style="text-align:center"><a href="#" class="deletePackage btn" style="width:20px; height:20px" title="Quitar paquete" id="'+pkg.id+'"><img src="'+baseUrl+'/images/ui/close.png" alt="Quitar paquete"></a></td>\n\
        </tr>';
    }

    $('#tweight').val(tweight);
    $('#tvol').val(tvol);
    $('#tablePackageBody').html(markup);
    changeUnitarys();
}

var delaySearch = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();

function createComboUnity(obPkg,optionCount){
    /*var markup = '<select name="unity[]" id="unity'+obPkg.id+'" class="boxunity span1" style="width:50px" >';
    if(optionCount>0 && optionCount<=30){
        for(i=0;i<=optionCount;i++){
            markup+='<option value="'+i+'" '+(obPkg.unity && obPkg.unity==i? 'selected' : "" )+'>'+i+'</option>';            
        }
    }
    
    markup+='</select>'*/
    	
   var markup = '<input type="hidden" name="unity[]" id="unity'+obPkg.id+'" value="'+obPkg.unity+'" class="boxunity span1" style="width:50px" readonly />';
    	
    	
    return markup;
}

function inputPackageCatalog(){
    $('#alto').number(true,2);
    $('#ancho').number(true,2);
    $('#profundidad').number(true,2);
    $('#peso').number(true,2);
}

function roundTo(number, decimalPlaces){
    result = 0;
    if(number && decimalPlaces>0){
        var base = Math.pow(10,decimalPlaces);
        result = Math.round(number * base)/base
    }
    return result;
}

function updateTotalList(){
    costingConfig = {};            
    changeUnitarys();
}

//Carga de productos de la orden actual
function loadProducts(){
        //orderId,urlSearchProducts: Variables al inicio del documento.
	
    $.post(urlSearchProducts,{orderId:orderId},function(data){                
    	$("#ininopacks").val(data.length);
        if(data && $.isArray(data) && data.length > 0){
        	$("#seldiv").show();

	
	        $('#selgroup').live('change',function(event){
	        	$('#selected').show();
	        	$('#seldiv').hide();
	            if($(this).val() == 1){
	            	//packagesSelected = [];
	                $('.col-sm').show();
	                $('#tablePackageBody').html('');
	                $('#tablePackage').show();
	                $('#sel1').show();
	                $('#sel2').hide();
	        	}else{
	        		addSelectedPackagesToTable();
	        		$('#tablePackage').show();
	                $('.col-sm').hide();
	                $('#sel2').show();
	                $('#sel1').hide();
	        	}
	        });
        	
            //packagesLoaded = data;
            for(var i=0;i<data.length;i++)
                addPackageToCollection(data[i]);
            	$('#packageText').prop('disabled',true);
            	$('.boxunity').prop('disabled',true);
            	$('.deletePackage').prop('disabled',true);
        }else{
        	
        	$("#seldiv").hide();
        	$(".col-sm").show();
        	$("#tablePackage").show();
        	
        }
    },'json');
}

// ========== Funciones para asociar promoción a paquete ==========
function openModalPromotion(){
    var btns = {
                    close: {
                        text:'Cerrar',
                        click: function(){                            
                            $(this).dialog('close');
                            resetSearchPromotion();
                        }
                    },
                    add: {
                        text:'Asociar promoción',
                        click: function(){
                            if(selectedPromo && selectedPromo.id){
                                $(this).dialog('close');
                                updatePackageCollection(selectedPromo);
                                resetSearchPromotion();
                            }else{
                                alert("Primero debe seleccionar una promoción del cuadro desplegable.")
                            }
                        }
                    }
                }

    $('.promo').live('click',function(){
        var currentRow = $(this).parent()/*td*/.parent()/*tr*/;
        var currentRowTd = currentRow.children(0)[0]/*td*/

        selectedPackageid = parseInt($(currentRowTd).find('.pkgid').val()); 
        
        Masdist.createMessagebox('Asociar Promoción a un paquete',null,btns,{width:'350', height:'300'},'searchPromotionDialog');
        loadPromotionCatalog();
    });

    removePromotion();
}

function loadPromotionCatalog(){    
    var vpromo = "";
    
    selectedPromo=null;
    
    //Cargar sólo 1 vez el catálogo
    if(promotionsLoaded.length==0){
        $.ajax({
            type: "POST",
            url: urlSearchPromotion,
            data: JSON.stringify({filter:{name:vpromo, userId:currentUser} }),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(data){
                var data=data.data          
                if(data && $.isArray(data) && data.length > 0)
                {
                    promotionsLoaded = data;                            
                    createPromotionsCombo(promotionsLoaded);
                }
                else
                {                            
                    selectedPromo = null;
                }
            },
            failure: function(errMsg) {}
            }
        );
    }else{
        createPromotionsCombo(promotionsLoaded);
    }

    function createPromotionsCombo(arraydata){
        var markup = '<option>Seleccione una promoción...</option>';
        $('#suggestedPromoList').html('');
        for(i=0;i<arraydata.length;i++){
            markup+='<option value="' + arraydata[i].id + '">' + arraydata[i].name + '</option>';
        }
        
        $('#suggestedPromoList').html(markup);
        selectPromotion();
    }
}

function selectPromotion(){
    $('#suggestedPromoList').change(function(){

        var elpromoId = $(this).val();
        var promoId = parseInt(elpromoId);
        
        for(var i=0;i<promotionsLoaded.length;i++){
            if(promotionsLoaded[i].id == promoId){
                selectedPromo = promotionsLoaded[i];
                break;
            }
        }
    });
}

function updatePackageCollection(oPromotion){    
    if(!oPromotion || !oPromotion.id) return;

    for(var i=0;i<packagesSelected.length;i++){
        if(packagesSelected[i].id==selectedPackageid){
           packagesSelected[i].promotionid=oPromotion.id;
           packagesSelected[i].promotionName = oPromotion.name;
           packagesSelected[i].promotionNumResources = oPromotion.numResources;
           addSelectedPackagesToTable();
           break;
        }
    }
}

function removePromotion(){
    $('.removepromo').live('click',function()
    {        
        var currentRow = $(this).parent()/*td*/.parent()/*tr*/;
        var currentRowTd = currentRow.children(0)[0]/*td*/

        var packageId = parseInt($(currentRowTd).find('.pkgid').val());       
        for(var i=0;i<packagesSelected.length;i++){            
            if(packagesSelected[i].id==packageId){
                packagesSelected[i].promotionid=null;
                packagesSelected[i].promotionName = '';
                packagesSelected[i].promotionNumResources = 0;
                addSelectedPackagesToTable();
                break;
            }
        }
    });    
}

function resetSearchPromotion(){
    $('#suggestedPromo').hide();
    $('#suggestedPromoList').html('');
    $('#promotionText').val('');
    selectedPackageid = null;
    selectedPromo = null;
}


