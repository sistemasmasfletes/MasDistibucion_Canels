$(document).ready(function(){

    $('#sessionExit').click(function(){

        if(confirm(String.fromCharCode(191)+'Estas seguro que deseas finalizar la sesi'+String.fromCharCode(243)+'n actual?'))
        {
            document.location = exitLink;
        }

    });
});