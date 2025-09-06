$(document).ready(function(){
    orderRecurrent();
    datePickerOrderRecurrent();
});

function orderRecurrent()
{
    $('#orderR').live('change',function(){
        var sel = $('#orderR').val();
        if(sel == 1)
        {
            $('#envioR').hide();
            $('#orderR').val(0);
        }
        else if(sel == 0)
        {
            $('#orderR').val(1);
            $('#envioR').show();
            $.post(urlOrderRecurrent,{},function(data){
                $('#envioR').html(data);
            });
        }
    });
}

function datePickerOrderRecurrent()
{   
    $('#date').live('click',function(){
        $('#date').datepicker({ dateFormat:'yy-mm-dd'});
        $('#ui-datepicker-div').css('z-index', 2);
    });
}