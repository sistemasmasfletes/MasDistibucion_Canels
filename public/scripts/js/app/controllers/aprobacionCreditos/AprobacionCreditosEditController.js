function AprobacionCreditosEditController($scope, $timeout, $state, $stateParams, PARTIALPATH, ModalService, CatalogService, CompraCreditosDataService, compraCreditos) {
    $scope.aprobacion = compraCreditos;
//    $scope.compraCreditosStatus = CatalogService.getCompraCreditosStatus(); //definir en CatalogService
//    $scope.compraCreditosTypes = CatalogService.getCompraCreditosType();
    //AprobacionCreditosEditController
    $scope.save = save;
    $scope.back = function () {
        $state.go('^', $stateParams)
    };
    /* DatePicker*/
    $scope.datePicker = {
        format: 'dd-MM-yyyy',
        toggleMin: function () {
            $scope.datePicker.minDate = null//$scope.datePicker.minDate ? null : new Date();
        },
        open: function ($event) {
            $event.preventDefault();
            $event.stopPropagation();
            $scope.datePicker.opened = true;
        },
        dateOptions: {
            formatYear: 'yy',
            startingDay: 1
        }
    }
    $scope.datePicker.toggleMin();


    function save() {

        $scope.compra.cuenta = $("#cuenta").val();
        // $scope.compra.referencia = $("#referencia").val();
        $scope.compra.path = 'MAS_FLETES\\public\\Documents\\PDF\\' + $("#path").val();

        if ($scope.compra) {
            $scope.loading = true;
            CompraCreditosDataService.save($scope.compra, {})
                    .success(function (data, status, headers, config) {
                        $scope.loading = false;
                        if (data.error) {
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: data.error
                            };
                            ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
                        } else {
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: '¡Registro guardado con éxito!'

                            };
                            
                            var file_data = $('#path').prop('files')[0];
                            var form_data = new FormData();
                            form_data.append('file', file_data);
                            
                            $.ajax({
                                url: '/MasDistribucion/public/OperationController/CompraCreditos/subir', // point to server-side PHP script 
                                dataType: 'text', // what to expect back from the PHP script, if anything
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: form_data,
                                type: 'post',
                                success: function (php_script_response) {
                                    alert(php_script_response); // display response from the PHP script, if any
                                }
                            });
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                $scope.back();
                                $scope.grid.refresh();
                            });
                        }
                    })
                    .error(function (data, status, headers, config) {
                        $scope.loading = false;
                    });
        }
    }

    $(document).ready(function () {
        monedas();
    });
    function monedas() {
        $.ajax({
            type: 'GET',
            data: {metodo: 'monedas'},
            url: '/MasDistribucion/public/OperationController/CompraCreditos/monedas',
            dataType: "json",
            success: Listamonedas
        });
    }
    function Listamonedas(data) {
        $('#moneda option').remove();
        var list = data == null ? [] : (data.monedas instanceof Array ? data.monedas : [data.monedas]);

        if (list.length < 1) {
            alert("SIN NINGÚN RESULTADO EN LA BD");
        } else {

            $('#moneda').append('<option value="0">Seleccionar...</option>');
            $.each(list, function (monedas, mon) {
                $('#moneda').append('<option value="' + mon.moneda + '">' + mon.moneda + '</option>');
            });
            $('#moneda').val($scope.compra.moneda);
            $('#moneda').focus();
        }
    }
    //---------------------
    $(document).ready(function () {
        bancos();
    });
    function bancos() {
        $.ajax({
            type: 'GET',
            data: {metodo: 'bancos'},
            url: '/MasDistribucion/public/OperationController/CompraCreditos/bancos',
            dataType: "json",
            success: Listabancos
        });
    }
    function Listabancos(data) {
        $('#idbanco option').remove();
        var list = data == null ? [] : (data.bancos instanceof Array ? data.bancos : [data.bancos]);

        if (list.length < 1) {
            alert("SIN NINGÚN RESULTADO EN LA BD");
        } else {

            $('#idbanco').append('<option value="0">Seleccionar...</option>');
            $.each(list, function (bancos, ban) {
                $('#idbanco').append('<option value="' + ban.id + '">' + ban.name + '</option>');
            });
            $('#idbanco').val($scope.compra.name);

            $('#idbanco').focus();
        }
    }
    ////////categorias///////////////
    $(document).ready(function () {
        categorias();
    });
    function categorias() {
        $.ajax({
            type: 'GET',
            data: {metodo: 'categorias'},
            url: '/MasDistribucion/public/OperationController/CompraCreditos/categorias',
            dataType: "json",
            success: ListaCategorias
        });
    }
    function ListaCategorias(data) {
        $('#categorias option').remove();
        var list = data == null ? [] : (data.categorias instanceof Array ? data.categorias : [data.categorias]);

        if (list.length < 1) {
            alert("SIN NINGÚN RESULTADO EN LA BD");
        } else {

            $('#categorias').append('<option value="0">Seleccionar...</option>');
            $.each(list, function (categorias, cat) {
                $('#categorias').append('<option value="' + cat.id + '">' + cat.name + '</option>');
            });
            $('#categorias').val($scope.compra.name);
            $('#categorias').focus();
        }
    }

    ///////fin categorias/////////////7

    $(document).ready(function () {
        usuarios();
    });
    function usuarios() {
        $.ajax({
            type: 'GET',
            data: {metodo: 'usuarios'},
            url: '/MasDistribucion/public/OperationController/CompraCreditos/usuarios',
            dataType: "json",
            success: Listausuarios
        });
    }
    function Listausuarios(data) {
        $('#usuario option').remove();
        var list = data == null ? [] : (data.usuarios instanceof Array ? data.usuarios : [data.usuarios]);

        if (list.length < 1) {
            alert("SIN NINGÚN RESULTADO EN LA BD");
        } else {

            $('#usuario').append('<option value="0">Seleccionar...</option>');
            $.each(list, function (usuarios, us) {
                $('#usuario').append('<option value="' + us.commercialName + '" >' + us.commercialName + '</option>');
            });
//                        $('#usuario').val($scope.compra.commercialName);
            $('#usuario').focus();
        }
    }




    /////////////

    //////////////777
    $scope.datosBancos = function () {

        var idBanco = $("#idbanco").val();
         $.ajax({
            method: 'POST',
            data: {
                metodo: 'cuentaBanco',
                idBanco: idBanco,
            },
            url: '/MasDistribucion/public/OperationController/CompraCreditos/cuentaBanco',
            dataType: "json",
            success: $scope.ListaDatosBancos
        });
    }

    $scope.ListaDatosBancos = function (data) {
        if (data.id != 0) {
            $("#cuenta").val(data.numeroCuenta);
            // $("#referencia").val(data.clabeInterbancaria);
            $("#bancoMoneda").val(data.moneda);
        } else {
            alert("no data");
        }

    }
    ////////////////////
    $(document).ready(function () {
        tipoPagos();
    });

    function tipoPagos() {
        $.ajax({
            type: 'GET',
            data: {metodo: 'tipoPagos'},
            url: '/MasDistribucion/public/OperationController/CompraCreditos/tipoPagos',
            dataType: "json",
            success: ListatipoPagos
        });
    }
    function ListatipoPagos(data) {
        $('#tipoPago option').remove();
        var list = data == null ? [] : (data.tipoPagos instanceof Array ? data.tipoPagos : [data.tipoPagos]);

        if (list.length < 1) {
            alert("SIN NINGÚN RESULTADO EN LA BD");
        } else {

            $('#tipoPago').append('<option value="0">Seleccionar...</option>');
            $.each(list, function (tipoPagos, tp) {
                $('#tipoPago').append('<option value="' + tp.tipoPago + '" >' + tp.tipoPago + '</option>');
            });
            $('#tipoPago').val($scope.compra.tipoPago);
            $('#tipoPago').focus();
        }
    }
    $(document).ready(function () {
        estatus();
    });
    function estatus() {
        $.ajax({
            type: 'GET',
            data: {metodo: 'estatus'},
            url: '/MasDistribucion/public/OperationController/CompraCreditos/estatus',
            dataType: "json",
            success: ListaEstatus
        });
    }
    function ListaEstatus(data) {
        $('#estatus option').remove();
        var list = data == null ? [] : (data.estatus instanceof Array ? data.estatus : [data.estatus]);

        if (list.length < 1) {
            alert("SIN NINGÚN RESULTADO EN LA BD");
        } else {

            $('#estatus').append('<option value="0">Seleccionar...</option>');
            $.each(list, function (estatus, est) {
                $('#estatus').append('<option value="' + est.estatu + '">' + est.estatu + '</option>');
            });
            $('#estatus').val($scope.compra.estatu);
          
            $('#estatus').focus();
        }
    }
    



    $scope.getFormFieldCssClass = function (ngModelController) {
//        if (ngModelController.$pristine)
//            return "";
//        return ngModelController.$valid ? "has-success" : "has-error";
    };
}