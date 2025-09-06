
$(document).ready( function(){
    $('#newStartDate').datepicker({dateFormat: 'yy-mm-dd'});
    $('#newStartDate').click(function(){
        $('#ui-datepicker-div').css('z-index', 2);
    });
    
    $('#newEndDate').datepicker({dateFormat: 'yy-mm-dd'});
    $('#newEndDate').click(function(){
        $('#ui-datepicker-div').css('z-index', 2);
    });

});


