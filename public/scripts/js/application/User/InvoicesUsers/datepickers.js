$(document).ready(function()
    {
        $('#startDate,#endDate').datepicker({
            dateFormat: 'dd-mm-yy'
        });
        $('#startDate,#endDate').click(function(){
            $('#ui-datepicker-div').css('z-index', 2);
        });
    

        $('#find').click(function(){
            lapsoDeFacturacion();
        });
    });
    
function lapsoDeFacturacion()
{
    var startDate = $('#startDate').val();    
    var endDate = $('#endDate').val();    
    if(startDate && endDate)
    {
       $.post(urlFindInvocesUser,{
           'startDate': startDate,
           'endDate': endDate},
       function(data){
           $('#respuesta').html(data);
       });
    }
    else if(startDate)
        alert('Por favor defina la fecha final de busqueda');
    else 
        alert('Por favor defina la fecha inicial de busqueda');
}