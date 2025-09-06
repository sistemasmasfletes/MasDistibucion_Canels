$(document).ready( function(){
    initCalendar();
    initDialogMetting();
    initValidate();
    initMeeting();
});
function initCalendar()
{
    $('#fullCallendar').fullCalendar({
        defaultView:'agendaWeek',
        height: 350,
        eventSources:[
            $('#urlSelfCalendarEvents').val(),
            $('#urlInvitedCalendarEvents').val()
        ],
        minTime : $('#min_time').val() ,
        maxTime : $('#max_time').val() ,
        theme:false,
        header: {
            left: 'prev,today,next',
            center: '   title   ',
            right: 'month,agendaWeek,agendaDay'
        },
        dayClick:function( date, allDay, jsEvent, view )
        {
            if(view.name == 'month')
            {
                $('#fullCallendar').fullCalendar( 'changeView', 'agendaDay' );
                $('#fullCallendar').fullCalendar( 'gotoDate', date );
            }
        },
        eventClick: function(calEvent, jsEvent, view) {
            showDetailsEvent(calEvent);
        }
    });
    //recorremos el calendario a la fecha deseada
    $('#fullCallendar').fullCalendar('gotoDate',$('#year_init').val(),$('#month_init').val(),$('#day_init').val());    
}
function showDetailsEvent(calEvent)
{

//    $.post($('#urlCalendarEvents').val(),{id:calEvent.id},function(data){
//        $('#detailEvent').html(data);
//    });
    if(calEvent.id != 0)
    {
        var html='<div>';
        html+='<label>Start: </label>'+calEvent.start+'<br/>';
        html+='<label>Guest: </label>'+calEvent.guest+'<br/>';
        html+='<label>Company: </label>'+calEvent.company+'<br/>';
        html+='<label>Description: </label>'+calEvent.description+'<br/>';
        html+='<label>Website: </label>'+calEvent.website+'<br/>';
        html+='<label>Interests: </label>'+calEvent.interests+'<br/>';
        html+='</div>';
        $('#detailEvent').html(html);

        $('#detailEvent').dialog({
            modal:true,
            resizable:false,
            buttons: {
                Aceptar:
                {
                    text:'Accept',
                    click:function() {
                    $(this).dialog('close');
                    }
                }
            },
            open:initDialogButtons,
            closeText: 'hide',
            width:450
        });
    }

}
function initDialogButtons()
{
    $('.buttonDialog').button();
}
function initDialogMetting()
{
    $('.meeting').click(function()
    {
        var id=$(this).attr('value');
        $('#dialog').dialog({
            modal:true,
            resizable:false,
            buttons: {
                Aceptar:
                {//
                    text:'Accept',
                    click:function() {
                        $('#formUpdate').attr('action',$('#urlStatus_'+id).val()); 
                        $(this).dialog('close');
                        $('#formUpdate').submit(); 
                    }
                }
            },
            title:'Compose Message',
            closeText: 'hide',
            width:450
        });
    });
}

function initValidate()
{
    $('#sendInvite').validate(
    {
        rules:{
            message:{
                required:true
            }
        },
        messages:{
            message:{
                required:'This field is required'
            }
        }
    });
}
 function initMeeting()
 {
        $( "#tabs" ).tabs();
   
     
 }