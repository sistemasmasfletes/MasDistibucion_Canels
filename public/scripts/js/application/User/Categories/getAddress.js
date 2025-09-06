function getStates(country_id)
{    
    //var lista = '<option value="">Seleccionar estado</option>';
    var lista = '';
    var country = document.getElementById('country');
    var v_state = $("#hiddenstate").val()
    //$("#city").html('<option value="">Seleccionar ciudad</option>');
    //$("#state").html('<option value="">Seleccionar estado</option>');
    country.value = country_id;

    var v_s = (v_state != "") ? v_state : 24;

    $.post(urlGetStates, {countryId: country_id}, function (data) {
        for(var i = 0; i < data.length; i++){
        	var selected = (data[i].id == v_s) ? "selected = 'selected'" : "";
            //lista += '<option style="display:none;" '+ selected +' value="' + data[i].id.toString() + '">' + data[i].name + '</option>';
            lista += '<option '+ selected +' value="' + data[i].id.toString() + '">' + data[i].name + '</option>';
        }
       $("#state").html(lista);
       document.getElementById("state").onchange();
    });
}

function getCities(state_id){
    //var lista = '<option value="">Seleccionar ciudad</option>';
    var lista = '';
    var state = document.getElementById('state');
    $("#city").html(lista);
    var v_city = $("#hiddencity").val()
    state.value = state_id;

    var v_c = (v_city != "") ? v_city : 1829;

    $.post(urlGetCities, {stateId: state_id}, function (data) {
        for(var i = 0; i < data.length; i++){
        	var selected = (data[i].id == v_c) ? "selected = 'selected'" : "";
            //lista += '<option style="display:none;" '+ selected +' value="' + data[i].id.toString() + '">' + data[i].name + '</option>';
            lista += '<option '+ selected +' value="' + data[i].id.toString() + '">' + data[i].name + '</option>';
        }
       $("#city").html(lista);
       document.getElementById("city").onchange();
    });
}
