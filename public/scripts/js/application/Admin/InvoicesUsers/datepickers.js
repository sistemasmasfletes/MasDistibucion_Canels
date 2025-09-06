$(document).ready(function()
    {
        datePickersAdmin();
        datePickersUser();
    });
    
function datePickersAdmin()
{
    $('#endDate').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $('#endDate').click(function(){
        $('#ui-datepicker-div').css('z-index', 2);
    });
    $('#find').click(function(){
        lapsoDeFacturacion();
    });
    
}
function lapsoDeFacturacion()
{
    var endDate = $('#endDate').val();    
    if(endDate)
    {
        $.post(urlFindInvocesUser,{
            'endDate': endDate
        },
        function(data){
            $('#respuesta').html(data);
        });
    }
    else 
        alert('Por favor defina la fecha final de busqueda');
}

function datePickersUser()
{
    $('#endDateUser, #startDateUser').datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $('#endDateUser, #startDateUser').live('click',function(){
        $('#ui-datepicker-div').css('z-index', 2);
    });
        
    $('#findUser').live('click',function(){
        lapsoDeFacturacionUser();
    });
}

function lapsoDeFacturacionUser()
{
    var endDate = $('#endDateUser').val();    
    var startDate = $('#startDateUser').val();
    if(endDate && startDate)
    {
        $.post(urlFindInvocesUser,{
            'startDate':startDate,
            'endDate': endDate
        },
        function(data){
            $('#respuesta').html(data);
        });
    }
    else if(startDate)
        alert('Por favor defina la fecha final de busqueda');
    else
        alert('Por favor defina la fecha inicial de busqueda');
}