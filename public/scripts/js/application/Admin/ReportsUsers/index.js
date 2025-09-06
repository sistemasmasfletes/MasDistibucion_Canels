
$(document).ready(function(){

    // funcionalidad para el dialogo Modal
    $('.openModal').live('click',function(){
        var idOrder = $(this).attr('pid');
        $.post(getOrderDetails, {
            idOrder : idOrder
        }, function(data){
            $('#modalDiv .modal-body').html(data);
            $('#modalDiv .modal-body').html(data);
            $('#modalDiv').modal('show');
        });

    });

    $('.table ul    li > a').click(function () {
        $('.table ul li').removeClass();
        $(this).parent().addClass('active');
    });
    // funcionalidad para el menu
    $('.table ul li a').click(function(){
        var idSelected = $(this).attr('id');

        switch (idSelected) {
            case 'details':
                document.getElementById("tabla").style.border = "1px solid #998c73";
                getDataUser(urlGetDetails);
                break;
            case 'history':
                document.getElementById("tabla").style.border = "0px";
                getDataUser(urlGetHistorical);
                break;
            case 'catalogs':
                document.getElementById("tabla").style.border = "1px solid #998c73";
                getDataUser(urlGetCatalogs);
                
                break;
            case 'invoices':
                document.getElementById("tabla").style.border = "1px solid #998c73";
                getDataUser(urlGetInvoices);
                
                break;
            case 'block':
                document.getElementById("tabla").style.border = "1px solid #998c73";
                getDataUser(urlGetBlock);
                break;
            case 'branches':
                document.getElementById("tabla").style.border = "1px solid #998c73";
                getDataUser(urlBranchesUser);
                break;
        }
    });

    // funcionalidad para buscar el usuario
    $('#searchUser').ajaxForm({
        dataType: 'json',
        success:function(data){

            var div = '';
            if(data.res)
            {
                $('#currentUser').val(data.userId);
                getDataUser(urlGetDetails);
            }
            else
            {
                div = createMessage(data.message, 'alert-error');
                $('#reportArea').html(div);
                $('#currentUser').val('');
            }
        }
    });

    // funcionalidad para activar/desactivar un producto
    changeStatusProduct();
    changeStatusInvoicesUser();
    addPointOfBranches();    
    unsetPoint();		createPointOfBranches();
})

function getDataUser(url)
{
    $('#reportLoader').show();

    $.post(url,{
        currentUser : $('#currentUser').val()
    }, function(data){
        $('#reportLoader').hide();
        $('#reportArea').html(data);
    });
}

function addPointOfBranches()
{
    $('.addPoint').live('click',function(){
        var idBranche = $(this).attr('id');  
        saveChangePointOption(idBranche);
        $('#myModal').modal('toggle');
    });    
}

function unsetPoint(){
    $('.unsetPoint').live('click',function(){
        var idBranch = $(this).attr('id');

        var btns = {
                        close: {
                            text:'Aceptar',
                            click: function(){
                                $.post(urlSaveChange,
                                {
                                    'idPoint' : null,
                                    'idBranche': idBranch,
                                    'currentUser' : $('#currentUser').val()
                                },
                                function(data){
                                    $('#reportArea').html(data); 
                                });
                                  
                                $(this).dialog('close');
                                
                            }
                        },
                        cancelar: {
                            text:'Cancelar',
                            click: function(){                            
                                $(this).dialog('close');                                
                            }
                        }
                    }
                Masdist.createMessagebox('Atención','¿Desea desvincular este punto de venta de la sucursal?',btns,null);        
    });
}
function createPointOfBranches(){    $('.createPoint').live('click',function(){        var idBranche = $(this).attr('id');  var onlyaddr =  $('#onlyaddr').val();                $('<form method="post" action="https://masdistribucion.com/public/App/#!/points/add" target="_blank"><input type="hidden" name="idbranche" value="'+idBranche+'"></form>').appendTo('body').submit().remove();  window.open('https://www.google.com.mx/maps/place/'+onlyaddr)          });    }
function saveChangePointOption(idBranche)
{
    $('#saveChange').click(function(){
        //var idPoint = $('input[name="points"]:checked').val();
        var idPoint = $('#hiddenBranch').val()
                
        $.post(urlSaveChange,
        {
            'idPoint' : idPoint,
            'idBranche': idBranche,
            'currentUser' : $('#currentUser').val()
        },
        function(data){
            $('#reportArea').html(data); 
        });
        $('#myModal').modal('hide');
    });
}
function createMessage(message, clase)
{
    return '<div class="alert '+clase+'"><button class="close" data-dismiss="alert">×</button>'+message+'</div>';
}

function changeStatusProduct()
{
    $('.btn-group#productStatus .btn').live('click',function(){
        var pid = $(this).parent().attr('pid');
        var value = $(this).val();

        $.post(urlChangeStatus, {
            pId : pid,
            status : value
        }, function(data){
            });
    });

    $('.btn-group#userStatus .btn').live('click',function(){
        var idUser = $(this).parent().attr('idUser');
        var value = $(this).val();
        var comment = $('#comment').val();

        $.post(urlChangeStatusUser, {
            idUser : idUser,
            status : value,
            comment : comment
        }, function(data){
            $('#tableComments').html(data);
        //getDataUser(urlGetBlock);
        });

    });
}

function changeStatusInvoicesUser()
{
    $('select.orderId').live('change',function(){
        var oId = $(this).attr('id');
        var value = $(this).val();

        $.post(urlChangeInvoices, {
            'oId' : oId,
            'status' : value
        }, function(data){
            });
    });
}

function initTypeHead()
{
    $('#form-search input').typeahead({
        source: function (typeahead, query) {
            return $.post(urlTypeHead, {
                query: query
            }, function (data) {
                return typeahead.process(data);
            });
        }
    });

}


