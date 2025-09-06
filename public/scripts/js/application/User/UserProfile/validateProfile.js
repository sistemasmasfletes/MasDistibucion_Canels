$(document).ready(function ()
{
    initValidateProfile();
});

function initValidateProfile()
{
    $('#formProfileUser').validate(
    {
        rules:{
            firstName:{
                required:true
                
            },
            lastName:{
                required:true
            },
            title:{
                required:true
            },
            commercialName:{
                required:true
            },
            localNumber:{
                required:true,
                digits:true
            },
            cellPhone:{
                required:true,
                digits:true
            },
            /*country:{
                required:true
            },
            state:{
                required:true
            }*/
        },
        messages:{
            firstName:{
                required:'Este campo es obligatorio '
            },
            lastName:{
                required:'Este campo es obligatorio '
            },
            title:{
                required:'Este campo es obligatorio '
            },
            commercialName:{
                required:'Este campo es obligatorio '
            },
            LocalNumber:{
                required: 'Este campo es obligatorio ',
                digits:'Solo se permiten numeros'
            },
            cellPhone:{
                required:'Este campo es obligatorio ',
                digits:'Solo se permiten numeros'
            },
            /*country:{
                required:'Este campo es obligatorio ',
            },
            state:{
                required:'Este campo es obligatorio ',
            }*/
        }
        ,
        errorPlacement: function(error,element){
            error.appendTo(element.parent().next());
            }
            
    });
}

function getStates(country_id)
{    
    var lista = '<option value="">Seleccionar estado</option>';
    var country = document.getElementById('country');
    $("#city").html('<option value="">Seleccionar ciudad</option>');
    $("#state").html('<option value="">Seleccionar estado</option>');
    country.value = country_id;

    $.post(urlGetStates, {countryId: country_id}, function (data) {
        for(var i = 0; i < data.length; i++){
            lista += '<option value="' + data[i].id.toString() + '">' + data[i].name + '</option>';
        }
       $("#state").html(lista);
    });
}

function getCities(state_id){
    var lista = '<option value="">Seleccionar ciudad</option>';
    var state = document.getElementById('state');
    $("#city").html(lista);
    state.value = state_id;
    $.post(urlGetCities, {stateId: state_id}, function (data) {
        for(var i = 0; i < data.length; i++){
            lista += '<option value="' + data[i].id.toString() + '">' + data[i].name + '</option>';
        }
       $("#city").html(lista);
    });
}
