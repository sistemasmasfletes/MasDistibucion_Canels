$(document).ready(function(){

    

	$('#formStoreFilter').submit(function(e){

        var city = $('#city').val();

        

        if(city == null){

        	alert("Debe Seleccionar una ciudad");

        	e.preventDefault();

        	return false;

        }

		

	})

	

    $('#date').datepicker({ dateFormat: 'yy-mm-dd' }).val();

    

    $('select[name="client_id"]').live('change',function()

    {

       getData($(this).val());

    });

    $('select[name="client_favorites_id"]').live('change',function()

    {

       getData($(this).val());

    });

    

    changeRadioOptions();

    autocomplete();

    if($('#radio_favorites').length > 0)

    {

        $('#radio_favorites').trigger('click');

        $('#div_selected').val('1');

        //getData($('select[name="client_favorites_id"] option:first').val());

    }

    else

    {

        if ($('#radio_point_fletes').length >0 )

        {

            $('#radio_point_fletes').trigger('click');

            $('#div_selected').val('2');

        }

        else

        {

            $('#div_selected').val('3');

        }

    }

    

    $('#select_category').change(function()

    {

        getPoints($(this).val());

    });

    $('#select_category').trigger('change');

    $('#submitCreateOrder').click(function()

    {

        if($('#suggestedUsers').is(':visible'))

        {

            return false;

        }

    });

    

});



function getData(userId){

    $.post(urlGetDataUser,{userId:userId}, function(data)

    {

        data=$.parseJSON(data);

        $('#nombre').val(data.nombre);

        $('#apellido').val(data.apellido);

        $('#phone').val(data.phone);

        $('#movil').val(data.movil);

    });

}

function changeRadioOptions()

{

    $('.radio').click(function()

    {

        var id = $(this).attr('id');

        switch(id)

        {

            case 'radio_favorites':

                $('#div_radio_favorites').show();

                $('#div_radio_point_fletes').hide();

                $('#div_radio_clients').hide();

                $('#div_selected').val('1');

                disabledInputs();

                getData($('select[name="client_favorites_id"] option:first').val());

                break;

            case 'radio_clients':

                $('#div_radio_favorites').hide();

                $('#div_radio_point_fletes').show();

                $('#div_radio_clients').hide();

                $('#div_selected').val('3');

                disabledInputs();

                getPoints($('#select_category').val());

                break;

            case 'radio_point_fletes':

                $('#div_radio_favorites').hide();

                $('#div_radio_point_fletes').hide();

                $('#div_radio_clients').show();

                $('#div_selected').val('2');

                enableInputs();

                break;

        }

    });

}



function autocomplete()

{

    $('#usernameTxt').live('keyup',function()

    {

        var values = $('#usernameTxt').val();

        var state = $('#select_state').val();

        var chkTiendasSucursal = false;

        var city = '';
        var isbuy = ''
        var isbranche = "";
		
		if(typeof $('#isBranch').val() !== 'undefined'){
        	isbranche = $('#isBranch').val();
        }
		
        if(typeof $('#isbuy').val() !== 'undefined'){
        	isbuy = $('#isbuy').val();
        }
        

        if (document.getElementById("chkTiendasSucursal")){

            chkTiendasSucursal = $('#chkTiendasSucursal').is(":checked");

        }

        

        if (document.getElementById("city")){

            city = $('#city').val();

            if(city == null){

            	alert("Debe Seleccionar una ciudad");

                $('#suggestedUsers').show();

                $('#suggestedUsersList').html('');

            	return false;

            }

        }



        if(values.length == 0)

        {

            $('#suggestedUsers').hide();

            $('#usernameTxt').val("");

            $('#hiddenBranch').val("");

        }

        else

        {
            $.post(urlSearch, {paramString:values, state:state, conSucursal:chkTiendasSucursal, city:city,isbuy:isbuy, isbranche:isbranche}, function(data)
            {

                if(data.length > 0)

                {

                    $('#suggestedUsers').show();

                    $('#suggestedUsersList').html('');

                    $('#suggestedUsersList').html(data);

                    setUserName();

                }

                else

                {

                    $('#suggestedUsers').hide('slow');

                }

            });

        }

    });

    $('#select_state').live('change',function()

    {

        var values = $('#usernameTxt').val();

        var state = $('#select_state').val();

        if(values.length == 0)

        {

            $('#suggestedUsers').hide();

        }

        else

        {

            $.post(urlSearch, {paramString:values, state:state}, function(data)

            {

                if(data.length > 0)

                {

                    $('#suggestedUsers').show();

                    $('#suggestedUsersList').html('');

                    $('#suggestedUsersList').html(data);

                    setUserName();

                }

                else

                {

                    $('#suggestedUsers').hide('slow');

                }

            });

        }

    });

}

function getPoints(idCategory)

{

    $.post(urlGetPointsByCategory, {idCategory:idCategory},function (data)

    {

        $('#div_points').html(data);

        getData($('select[name="client_id"] option:first').val());

    });

}



function setUserName()

{

    $('.userFinalSuggestList').click(function()

    {

        var inpUser = $(this).find('.point_address');

        var address = inpUser.val();

        var inpIdBranch = $(this).find('.point_id');

        var IdBranch = inpIdBranch.val();

        $('#suggestedUsers').hide();

        $('#usernameTxt').val(address);

        $('#hiddenBranch').val(IdBranch);

    });

}



function disabledInputs()

{

//    console.log('desabilita');

    $('#nombre').prev('span').show();

    $('#nombre').show();

    $('#apellido').prev('span').show();

    $('#apellido').show();

    $('#phone').prev('span').show();

    $('#phone').show();

    $('#movil').prev('span').show();

    $('#movil').show();

    $('#nombre').val('');

    $('#apellido').val('');

    $('#phone').val('');

    $('#movil').val('');

   

//    $('#email').attr('disabled','disabled');

//    $('#phone').attr('disabled','disabled');

//    $('#movil').attr('disabled','disabled');

//    $('#email').removeAttr('placeholder');

//    $('#phone').removeAttr('placeholder');

//    $('#movil').removeAttr('placeholder');

}



function enableInputs()

{

     $('#nombre').prev('span').hide();

    $('#nombre').hide();

     $('#apellido').prev('span').hide();

    $('#apellido').hide();

    $('#phone').prev('span').hide();

    $('#phone').hide();

    $('#movil').prev('span').hide();

    $('#movil').hide();

//    $('#email').removeAttr('disabled');

//    $('#phone').removeAttr('disabled');

//    $('#movil').removeAttr('disabled');

//    $('#email').attr('placeholder','Ingrese Email');

//    $('#phone').attr('placeholder','Ingrese telefono');

//    $('#movil').attr('placeholder','Ingrese movil');

//    $('#email').val('');

//    $('#phone').val('');

//    $('#movil').val('');

    

    

}



