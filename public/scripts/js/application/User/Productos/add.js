$(document).ready(function(){
    initTabs();
    intiButtonSave();
    initDialogs();
    sheepIt();

    $('#price').number(true,2);
    $('#priceList').number(true,2);
    $('#priceCreditos').number(true,2);
    $('#order').number(true);
    $('#stock').number(true);
    $('#warranty').number(true);
    $('#weight').number(true,2);
    $('#width').number(true,2);
    $('#height').number(true,2);
    $('#depth').number(true,2);
    $('#size').number(true,2);
    $('#size').number(true,2);


    //Keyevents para las medidas del producto
    $('#width').keyup(function(){
        var vwidth = $('#width').val();
        var vheight = $('#height').val();
        var vdepth = $('#depth').val();

        $('#size').val(vwidth*vheight*vdepth);
    });

     $('#height').keyup(function(){
        var vwidth = $('#width').val();
        var vheight = $('#height').val();
        var vdepth = $('#depth').val();

        $('#size').val(vwidth*vheight*vdepth);
    });

    $('#depth').keyup(function(){
        var vwidth = $('#width').val();
        var vheight = $('#height').val();
        var vdepth = $('#depth').val();

        $('#size').val(vwidth*vheight*vdepth);
    });
    
    $('#price').keyup(function(){
        var creditos = $('#creditosXmoneda').val();
        var precio = $('#price').val();
        
        $('#priceCreditos').val(precio*creditos);
    });
    
});

function initDialogs()
{
    $('#myModal').modal('hide');
    $('#myTab a:last').click(function() {
        var idProduct = $('#idProd').val();
        $('#myModal').modal('show');
        if(idProduct != '')
        {
            $('#notImage').hide();
            $('#imageUpdate').show();
        }
        else
        {
             $('#imageUpdate').hide(); 
             $('#notImage').show();
        }
//        e.preventDefault();
//        $(this).tab('show');
    });

}

function initTabs()
{
    $('#productForm span').addClass('span2');
    $('#productForm label').addClass('inline');
    //    $('#productForm input').addClass('block');

    $('#myTab a:last').tab('show');
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    $('#myTab a[href="#area1"]').tab('show');
    initToolTips();

    $('#newStartDate,#newEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
}

function initToolTips()
{
    // inicializamos los tooltips
    $('#name,#price,#priceList,#stock,#order').popover('hide');
    $('#maker,#newStartDate,#newEndDate,#sku,#warranty,#weight,#width,#height,#depth,#color').popover('hide');
}

function intiButtonSave()
{
    var opciones= {
        beforeSubmit: function(){
            if(!validateFormFields()){                
                return false;
            }
            mostrarLoader();
            
        },
        success: mostrarRespuesta //funcion que se ejecuta una vez enviado el formulario
    };


    $('#productForm').ajaxForm(opciones);


}

function mostrarLoader()
{
    // si existen alertas las cerramos
    $('.messages').html('<div class="alert alert-error fade in">Enviando formulario<button type="button" class="close" data-dismiss="alert">×</button></div>');
    $('.messages').show();
}

function mostrarRespuesta(response)
{
    var response = jQuery.parseJSON(response);
    var clase = 'alert alert-error';
    var idProduct = $('#idProd').val();
    $('#idProducto1').val(response.idProduct);
    $('.messages').hide();
    if(response.res)
    {
        clase = 'alert';
    }
    
    var arrMessage = response.message;
    if($.isArray(arrMessage) && arrMessage.length>0){       
        var markup = "";
        for(var i=0;i<arrMessage.length;i++){
            markup+=arrMessage[i]+'<br>';
        }
        Masdist.createMessagebox('Aviso',markup,null,null);
    }else
        Masdist.createMessagebox('Aviso',response.message,null,null);

    if(idProduct == "")
    {
        $('#idProd').val(response.idProduct);
    }
    $('#notImage').hide();
    $('#imageUpdate').show();
    
}    

function sheepIt()
{
    showHideSheepit($('#variantsUse').val());
    $('#variantsUse').change(function(){
        showHideSheepit($(this).val());
    });
    var i=0;
    var pregenerated = new Array();
    $('.pregenerated').each(function (){
        pregenerated[i]='pregenerated_form_'+(i+1);
        i++;
    });
    $('#sheepItForm').sheepIt({
        separator: '',
        allowRemoveLast: false,
        allowRemoveCurrent: true,
        allowRemoveAll: false,
        allowAdd: true,
        allowAddN: false,
        maxFormsCount: 10,
        minFormsCount: 0,
        iniFormsCount: 0,
        pregeneratedForms: pregenerated
    });
    showHideSheepit($('#variantsUse').val());
}
function showHideSheepit(value){
    if(value==1)
    {
        $('#sheepItForm').show();
        $('#stock').addClass('disabled');
        $('#stock').attr('disabled','disabled');
    }
    else
    {
        $('#sheepItForm').hide();
        $('#stock').removeClass('disabled');
        $('#stock').removeAttr('disabled');
    }
}

function validateFormFields(){

    var validated = true;

    var vwidth = $('#width').val();
    var vheight = $('#height').val();
    var vdepth = $('#depth').val();

    if($('#name').val()==''){
        Masdist.createMessagebox('Atención','El nombre del producto no pueden quedar vacío.',null,null);
        $('#myTab a[href="#area1"]').tab('show');

        return false;
    }

    if($('#price').val()=='' || parseFloat($('#price').val())<=0){
        Masdist.createMessagebox('Atención','El precio del producto no pueden quedar vacío o en ceros.',null,null);
        $('#myTab a[href="#area1"]').tab('show');

        return false;
    }

    if($('#stock').val()=='' || parseFloat($('#stock').val())<=0){        Masdist.createMessagebox('Atención','Las existencias del producto no pueden quedar vacías o en ceros.',null,null);
        $('#myTab a[href="#area1"]').tab('show');

        return false;
    }

    

    if(vwidth=='' || parseFloat(vwidth)<=0){
        Masdist.createMessagebox('Atención','Las medidas de ancho del embalaje no pueden quedar vacías o en ceros.',null,null);
        $('#myTab a[href="#embalaje"]').tab('show');
        return false;
    }

    if(vheight=='' || parseFloat(vheight)<=0){
        Masdist.createMessagebox('Atención','Las medidas de alto del embalaje no pueden quedar vacías o en ceros.',null,null);
        $('#myTab a[href="#embalaje"]').tab('show');

        return false;
    }

    if(vdepth=='' || parseFloat(vdepth)<=0){        
        Masdist.createMessagebox('Atención','Las medidas de largo del embalaje no pueden quedar vacías o en ceros.',null,null);
        $('#myTab a[href="#embalaje"]').tab('show');

        return false;
    }

    if($('#size').val()=='' || parseFloat($('#size').val())<=0){      
        Masdist.createMessagebox('Atención','El tamaño del embalaje no pueden quedar vacío o en ceros.',null,null);
        $('#myTab a[href="#embalaje"]').tab('show');

        return false;
    }
   
    return validated;
}
