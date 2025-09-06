$(document).ready(function()
{
    $('#startDate,#endDate').datepicker({
        dateFormat: 'dd-mm-yy',
        minDate: 0
    });
    $('#startDate,#endDate').click(function(){
        $('#ui-datepicker-div').css('z-index', 2);
    });
  
});