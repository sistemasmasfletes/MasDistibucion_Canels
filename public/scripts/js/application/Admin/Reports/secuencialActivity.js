$(document).ready(function(){
    $('.dateInput').datepicker({
        dateFormat: 'yy-mm-dd'
    }).change(function(){
         getHours();
         //getDataReport($('#hourExit').val());
    });
    $('#route').change(function(){
        getHours();
        //getDataReport($('#hourExit').val());//al hacer el cambio en este hacer un post y traer las saliudas de ese dia para el tipo
    });
    $('#hourExit').change(function(){
        getDataReport($(this).val());
    });
    getDataReport($('#hourExit').val());
});

function getDataReport(hourExit)
{
    $('#reportLoader').show();
    var init = $('#start').val();
    var route = $('#route').val();
    var hourRoute = hourExit;

    $('#reportResult').hide();
    $.post(urlGetSecuencialActivity, {
            starDate : init,
            route : route,
            hourRoute:hourRoute
        }, function(data){
            $('#reportLoader').hide();
            $('#reportResult').html(data);
            $('#reportResult').show();
        });
}
function getHours()
{
    var date=$('#start').val();
    var route = $('#route').val();
    $.post(urlGetOptionsHour, {date:date,route:route}, function(data){
        $('#hourExit').html(data);
        getDataReport($('#hourExit').val());
    });
}