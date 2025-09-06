$(document).ready(function()
{
    $('#date , #dateSecond').datepicker({
        beforeShow: function(input){
            return { minDate: (input.id == "dateSecond" ? ( $("#date").datepicker("getDate") ? $("#date").datepicker("getDate"): 0 ): '0')}
        },
        dateFormat: 'yy-mm-dd'
    });
});