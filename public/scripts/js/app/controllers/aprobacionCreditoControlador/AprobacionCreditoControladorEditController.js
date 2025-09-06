function AprobacionCreditoControladorEditController($scope, $timeout, $state, $stateParams, PARTIALPATH, ModalService, CatalogService, AprobacionCreditoControladorDataService, AprobacionCreditoControlador) {
    $scope.aprobacionCredito = AprobacionCreditoControlador;
    $scope.aprobacionCreditoControladorStatus = CatalogService.getAprobacionCreditoControladorStatus(); //definir en CatalogService
    $scope.aprobacionCreditoControladorTypes = CatalogService.getAprobacionCreditoControladorType();

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
        
        $scope.aprobacionCredito.cuenta = $("#cuenta").val();
        $scope.aprobacionCredito.referencia = $("#referencia").val();

        if ($scope.aprobacionCredito) {
            $scope.loading = true;
            AprobacionCreditoControladorDataService.save($scope.aprobacionCredito, {})
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
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
//                                $scope.back();
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
            url: '/MasDistribucion/public/OperationController/AprobacionCreditoControlador/monedas',
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
            url: '/MasDistribucion/public/OperationController/AprobacionCreditoControlador/bancos',
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
            url: '/MasDistribucion/public/OperationController/AprobacionCreditoControlador/categorias',
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
            url: '/MasDistribucion/public/OperationController/AprobacionCreditoControlador/usuarios',
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
    

    
    
    $scope.datosBancos = function(){
        
        var idBanco = $("#idbanco").val();
        

        
        $.ajax({
            method: 'POST',
            data: {
                metodo: 'cuentaBanco',
                idBanco:idBanco,
            },
            url: '/MasDistribucion/public/OperationController/AprobacionCreditoControlador/cuentaBanco',
            dataType: "json",
            success: $scope.ListaDatosBancos
        });
    }
     
    $scope.ListaDatosBancos = function(data){
        if ( data.id != 0) {
            $scope.compra.cuenta = data.numeroCuenta;
            $scope.compra.referencia = data.clabeInterbancaria;
            $("#cuenta").val( data.numeroCuenta );
            $("#referencia").val( data.clabeInterbancaria );
        } else {
            alert("no data");
        }
         
     }
    /////////////
    
    //////////////777
    $scope.datosBancos = function(){
        
        var idBanco = $("#idbanco").val();
        

        
        $.ajax({
            method: 'POST',
            data: {
                metodo: 'cuentaBanco',
                idBanco:idBanco,
            },
            url: '/MasDistribucion/public/OperationController/AprobacionCreditoControlador/cuentaBanco',
            dataType: "json",
            success: $scope.ListaDatosBancos
        });
    }
     
    $scope.ListaDatosBancos = function(data){
        if ( data.id != 0) {
            $("#cuenta").val( data.numeroCuenta );
            $("#referencia").val( data.clabeInterbancaria );
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
            url: '/MasDistribucion/public/OperationController/AprobacionCreditoControlador/tipoPagos',
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
    
    
   
    $scope.getFormFieldCssClass = function (ngModelController) {
        /*if (ngModelController.$pristine)
            return "";
        return ngModelController.$valid ? "has-success" : "has-error";*/
    }
}