$(document).ready(function(){
   var day;
   day = new Date(); 
   day = day.getDay();
   $.post(urlMantenimiento,{'day' : day},function(data){
       $('#message').html(data);
   });
   
});
