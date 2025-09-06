function CompraCreditosEditController($scope, $timeout, $state, $stateParams, CONFIG, PATH, PARTIALPATH, ModalService, CatalogService, CompraCreditosDataService, compraCreditos, $http) {

    $scope.ROL_CLIENTE = 3;
    $scope.ROL_CONTROLADOR = 7;
    $scope.ROL_CHOFER = 2;
    $scope.ESTATUS_PENDIENTE = 1;

    $scope.compra = compraCreditos;
    $scope.cliente = $scope.compra.cliente;
    $scope.categorias = [];
    $scope.bancos = [];
    $scope.monedas = [];
    $scope.tiposPago = [];
    $scope.clientes = [];
    $scope.estatus = [];
    $scope.cuentas = [];

    $scope.esCliente = false;
    $scope.esControlador = false;
    $scope.esChofer = false;

    $scope.editaEstatus = true;
    $scope.hide = true;
    $scope.hideTerminal = true;

    $scope.imagenPaypal = PATH.paypalImg;
    $scope.imagenTerminal = PATH.terminalImg;

    $scope.paypalSuccess = PATH.paypalSuccess;
    $scope.paypalCancel = PATH.paypalCancel;
    $scope.urlCompra = PATH.compraCreditos;
    $scope.urlPath = CONFIG.PATH;
    $scope.compra.comentario = ($scope.compra.comentario && $scope.compra.comentario != "---") ? $scope.compra.comentario : "";

    $scope.save = save;
    this.init = function () {
        $scope.obtieneConfigracion();
        $scope.required = "";
    };


    $scope.obtieneConfigracion = function ()
    {
        var data = {};
        var url = PATH.compraCreditos + '/obtenerConfiguracion';
        $scope.loading = true;
        var request = $http({
            method: "post",
            url: url,
            data: data
        });
        request.success($scope.onObtieneConfiguracion);
    };

    $scope.onObtieneConfiguracion = function (data)
    {
        if (data.error)
        {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: data.error
            };
            ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
        } else
        {
            //verifica si el que ingreso al sistema es cliente, chofer o controlador
            if (data.role === $scope.ROL_CLIENTE)
            {
                $scope.compra.categoriaId = data.idCategoria;
                $scope.compra.clienteId = data.idUser;
                if ($scope.compra.estatusId === undefined)
                {
                    $scope.compra.estatusId = $scope.ESTATUS_PENDIENTE;
                }
                $scope.esCliente = true;
            } else if (data.role === $scope.ROL_CONTROLADOR)
            {
                $scope.esControlador = true;
            } else if (data.role === $scope.ROL_CHOFER)
            {
                $scope.esChofer = true;

                if ($scope.compra.estatusId === undefined) {
                    //$scope.compra.estatusId = $scope.ESTATUS_PENDIENTE;
                }
            }
            if ($scope.compra.estatusId !== $scope.ESTATUS_PENDIENTE && $scope.compra.estatusId !== undefined)
            {
                $scope.editaEstatus = false;
            }
            $scope.listaCategorias();
            $scope.initCombos();
        }

    };

    $scope.initCombos = function ()
    {
        $scope.disable = false;

        $scope.listaTiposPago();
        $scope.listaCuentas();
        $scope.listaBancos();
        $scope.listaEstatus();
        $scope.listaMonedas();

        $scope.listaBancoMasDistribucion();
    };

    function save()
    {
        var PAGO_EFECTIVO = 1;
        if ($scope.compra)
        {
            if ($('#path').val() === "" && $scope.compra.tipoPago.id === PAGO_EFECTIVO && $scope.compra.id === undefined)
            {
                var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: "¡Es necesario subir un comprobante de pago!"
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                });
            } else
            {
                $scope.compra.path = '';

                $scope.loading = true;

                CompraCreditosDataService.save($scope.compra, {})
                        .success(guardaCompra)
                        .error(function (data, status, headers, config) {
                            $scope.loading = false;
                        });
            }
        }
    }

    function guardaCompra(data, status, headers, config) {
        $scope.loading = false;
        if (data.error) {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: data.error
            };
            ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
        } else {

            var idCompra = data[1].idCompra;
            guardaArchivo(idCompra);

            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Registro guardado con éxito!'

            };

            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                $scope.back();
                $scope.grid.refresh();
            });
        }
    }

    function guardaArchivo(idCompra) {
        if ($('#path').val() !== "") {

            var form_data = new FormData();
            form_data.append('file', $('#path').prop('files')[0]);
            form_data.append('idCompra', idCompra);

            $http.post(
                    PATH.compraCreditos + '/subir',
                    form_data,
                    {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}
                    }
            ).success(function (response) {
                if (response.respuesta === "0") {
                    alert("ocurrió un error al subir el archivo");
                }
            });
        }
    }

    $scope.fncGuardaPagoTerminal = function ()
    {
        $scope.loading = true;
        var data =
                {
                    metodo: 'fncTerminal',
                    compra: $scope.compra,
                    terminal: '000'
                };

        $.ajax({
            type: 'POST',
            data: data,
            url: PATH.compraCreditos + '/fncTerminal',
            dataType: "json",
            success: $scope.onfncGuardaPagoTerminal
        });
    };

    $scope.onfncGuardaPagoTerminal = function (data)
    {
        $scope.loading = false;
        if (data.error)
        {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: data.error
            };
            ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
        } else
        {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Registro guardado con éxito!'

            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                $scope.back();
                $scope.grid.refresh();
            });
        }
    };

    /*
     * inicia la carga de combos de compra de creditos
     */

    /*
     * combo categorias 
     */
    $scope.listaCategorias = function ()
    {
        $scope.loading = true;
        var data = {
            metodo: 'categorias'
        };
        var request = $http({
            method: "post",
            url: PATH.compraCreditos + '/categorias',
            data: data
        });
        request.success($scope.onListaCategorias);
    };

    $scope.onListaCategorias = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.categorias = list;
            $scope.compra.categoria = "";
        } else
        {
            list = data === null ? [] : (data.categorias instanceof Array ? data.categorias : [data.categorias]);
            $scope.categorias = list;
            $scope.compra.categoria = $scope.getSelectedCategoria();
            $scope.listaClientes();
        }
        $scope.loading = false;
    };

    $scope.getSelectedCategoria = function ()
    {
        var selected = "";
        for (var i = 0; i < $scope.categorias.length; i++)
        {
            if ($scope.compra.categoriaId === $scope.categorias[i].id)
            {
                return $scope.categorias[i];
            }
        }
        return selected;
    };

    /*
     * combo bancos 
     */
    $scope.listaBancos = function ()
    {
        $scope.loading = true;
        var data = {
            metodo: 'bancosEnCuenta'
        };
        var request = $http({
            method: "post",
            url: PATH.compraCreditos + '/bancosEnCuenta',
            data: data
        });
        request.success($scope.onListabancos);
    };

    $scope.onListabancos = function (data)
    {
        $scope.loading = false;
        var list = [];
        if (data.error)
        {
            $scope.bancos = list;
            $scope.compra.banco = "";
        } else
        {
            list = data === null ? [] : (data.bancos instanceof Array ? data.bancos : [data.bancos]);
            $scope.bancos = list;
            $scope.compra.banco = $scope.getSelectedBanco();
            $scope.listaMonedas();
        }

    };

    $scope.getSelectedBanco = function ( )
    {
        var selected = "";
        for (var i = 0; i < $scope.bancos.length; i++)
        {
            if ($scope.compra.bancoId == $scope.bancos[i].id)
            {
                return $scope.bancos[i];
            }
        }
        return selected;
    };

    /*
     * combo monedas 
     */
    $scope.listaMonedas = function ()
    {
        $scope.loading = true;
        var data = {
            metodo: 'monedas',
            banco: 0
        };
        var request = $http({
            method: "post",
            url: PATH.compraCreditos + '/monedas',
            data: data
        });
        request.success($scope.onListamonedas);
    };

    $scope.onListamonedas = function (data)
    {
        $scope.loading = false;
        var list = [];
        if (data.error)
        {
            $scope.monedas = list;
            $scope.compra.moneda = "";
        } else
        {
            list = data === null ? [] : (data.monedas instanceof Array ? data.monedas : [data.monedas]);
            $scope.monedas = list;
            $scope.compra.moneda = $scope.getSelectedMoneda();
            $scope.listaCuentas();
        }
    };

    $scope.getSelectedMoneda = function ()
    {
        var selected = "";
        for (var i = 0; i < $scope.monedas.length; i++)
        {
            if ($scope.compra.monedaId == $scope.monedas[i].id)
            {
                return $scope.monedas[i];
            }
        }
        return selected;
    };

    $scope.getSelectedCurrency = function ()
    {
        var selected = "MXN";
        for (var i = 0; i < $scope.monedas.length; i++)
        {
            if ($scope.compra.monedaId === $scope.monedas[i].id)
            {
                return $scope.monedas[i].currencyCode;
            }
        }
        return selected;
    };

    /*
     * combo metodo de pago 
     */
    $scope.listaTiposPago = function ()
    {
        $scope.loading = true;
        var data = {
            metodo: 'tipoPagos'
        };
        var request = $http({
            method: "post",
            url: PATH.compraCreditos + '/tipoPagos',
            data: data
        });
        request.success($scope.onListaTiposPago);
    };

    $scope.onListaTiposPago = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.tiposPago = list;
            $scope.compra.tipoPago = "";
        } else
        {
            list = data === null ? [] : (data.tipoPagos instanceof Array ? data.tipoPagos : [data.tipoPagos]);
            $scope.tiposPago = list;
            $scope.compra.tipoPago = $scope.getSelectedTipoPago();
        }

        $scope.loading = false;
    };

    $scope.getSelectedTipoPago = function ( )
    {
        var selected = "";
        for (var i = 0; i < $scope.tiposPago.length; i++)
        {
            if ($scope.compra.tipoPagoId === $scope.tiposPago[i].id)
            {
                return $scope.tiposPago[i];
            }
        }
        return selected;
    };

    $scope.getSelectedTipoPagoTipo = function ()
    {
        var PAGO_EFECTIVO = 1;
        var PAGO_SITIO = 2;
        var PAGO_INTERNET = 3;
        var PAGO_TERMINAL = 4;
        $scope.listaCuentas();
        if ($scope.compra.tipoPago.id === PAGO_EFECTIVO)
        {
            $scope.required = "required";
            $scope.hide = true;
            $scope.hideTerminal = true;
            $scope.disable = false;
//            if($scope.bancoMasDistribucion === $scope.compra.banco.id)
//            {
//                $scope.compra.banco = "";
//            }
            for (var i = 0; i < $scope.bancos.length; i++)
            {
                if ($scope.compra.bancoId === $scope.bancos[i].id) {
                    $scope.compra.banco = $scope.bancos[i];
                }
            }
        } else
        {
            $scope.required = "";
            if ($scope.compra.tipoPago.id === PAGO_TERMINAL)
            {
                $scope.hideTerminal = false;
                $scope.hide = true;
                $scope.disable = true;
            }

            if ($scope.compra.tipoPago.id === PAGO_SITIO || $scope.compra.tipoPago.id === PAGO_INTERNET)
            {
                $scope.hideTerminal = true;
                $scope.hide = false;
                $scope.disable = true;
            }
            for (var i = 0; i < $scope.bancos.length; i++)
            {
                if ($scope.bancoMasDistribucion == $scope.bancos[i].id) {
                    $scope.compra.banco = $scope.bancos[i];

                }
            }
            $scope.compra.referencia = "00000";
        }
        
        
        
    };

    //obtiene el id del banco Mas distrubución, 
    //debido a que no aplica el banco cuando es pago con tarjeta o paypal
    $scope.listaBancoMasDistribucion = function ()
    {
        var data = {
            metodo: 'fncBancoMasDistribucion'
        };
        var request = $http({
            method: "post",
            url: PATH.compraCreditos + '/fncBancoMasDistribucion',
            data: data
        });
        request.success($scope.listaBancoMasDistribucionSuccess);
    };

    $scope.listaBancoMasDistribucionSuccess = function (data)
    {
        if (data.error)
        {
            $scope.bancoMasDistribucion = "";
        } else
        {
            $scope.bancoMasDistribucion = data.bancos[0].id;
        }

    };

    //obtiene el id de la cuenta Mas distrubución, 
    //debido a que no aplica el banco cuando es pago con tarjeta o paypal
    $scope.listaCuentaMasDistribucion = function ()
    {
        var data = {
            metodo: 'fncBancoMasDistribucion',
            idMoneda: $scope.compra.moneda.id,
            idBanco: $scope.compra.banco.id
        };
        var request = $http({
            method: "post",
            url: PATH.compraCreditos + '/fncCuentaMasDistribucion',
            data: data
        });
        request.success($scope.listaCuentaMasDistribucionSuccess);
    };

    $scope.listaCuentaMasDistribucionSuccess = function (data)
    {
        var PAGO_EFECTIVO = 1;
        var list = [];
        if (data.error)
        {
            $scope.bancoMasDistribucion = "";
        } else
        {
            if ($scope.compra.tipoPago.id !== PAGO_EFECTIVO)
            {
                if (data.cuentas[0] === (null || undefined)) {
                    $scope.listaCuentas();
                }
                list = data === null ? [] : data.cuentas;
                $scope.cuentas = list;
                $scope.cuentaMasDistribucion = data.cuentas[0].id;
                $scope.listaCuentas();
            } else
            {
                $scope.listaCuentas();
            }
        }
    };

    /*
     * combo clientes 
     */
    $scope.listaClientes = function ()
    {
        if ($scope.compra.categoria !== "")
        {
            $scope.loading = true;
            var data = {
                idCategoria: $scope.compra.categoria.id
            };
            var url = PATH.compraCreditos + '/usuarios';

            var request = $http({
                method: "post",
                url: url,
                data: data
            });
            request.success($scope.onListaClientes);
        } else
        {
            $scope.clientes = [];
            $scope.compra.cliente = "";
        }
    };

    $scope.onListaClientes = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.clientes = list;
            $scope.compra.cliente = "";
        } else
        {
            list = data === null ? [] : (data.usuarios instanceof Array ? data.usuarios : [data.usuarios]);

            for (var i = 0; i < list.length; i++) {
                if (list[i].commercialName === null || list[i].commercialName === '') {
                    list[i].commercialName = (list[i].firstName + " " + list[i].lastName);

                }
            }

            $scope.clientes = list;
            $scope.compra.cliente = $scope.getSelectedCliente();
        }
        $scope.loading = false;
    };

    $scope.getSelectedCliente = function (  )
    {
        var selected = "";
        for (var i = 0; i < $scope.clientes.length; i++)
        {
            if ($scope.compra.clienteId === $scope.clientes[i].id)
            {
                return $scope.clientes[i];
            }
        }
        return selected;
    };

    $scope.listaEstatus = function ()
    {
        $.ajax({
            type: 'GET',
            data: {metodo: 'estatus'},
            url: PATH.compraCreditos + '/estatus',
            dataType: "json",
            success: $scope.onListaEstatus
        });
    };

    $scope.onListaEstatus = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.estatus = list;
            $scope.compra.estatus = "";
        } else
        {
            list = data === null ? [] : (data.estatus instanceof Array ? data.estatus : [data.estatus]);
            $scope.estatus = list;
            $scope.compra.estatus = $scope.getSelectedEstatus();
        }
        $scope.loading = false;
    };

    $scope.getSelectedEstatus = function (  )
    {
        var selected = "";
        for (var i = 0; i < $scope.estatus.length; i++)
        {
            if ($scope.compra.estatusId === $scope.estatus[i].id)
            {
                return $scope.estatus[i];
            }
        }
        return selected;
    };

    $scope.onChangeMoneda = function () {
       // $scope.listaCuentaMasDistribucion();
        $scope.obtenerCreditos();
    };


    $scope.obtenerCreditos = function () {
        if ($scope.compra.moneda.compra) {
            var precioCompra = parseFloat($scope.compra.moneda.compra);
            var montoCompra = parseFloat($scope.compra.montoCompra);
            var cantCreditos = parseFloat($scope.compra.moneda.creditos);
            var credito = (montoCompra / precioCompra) * cantCreditos;
            $scope.compra.creditos = "";
            $scope.compra.creditos = isNaN(credito) ? "0.0" : credito.toLocaleString('en');
        }
    };

    $scope.listaCuentas = function ( )
    {
        $scope.loading = true;


        var PAGO_EFECTIVO = 1;
        if ($scope.compra.tipoPago !== undefined) {

//            if ($scope.compra.tipoPago.id != PAGO_EFECTIVO && ($scope.compra.id === "" || $scope.compra.id === undefined))
//            {
//                for (var i = 0; i < $scope.cuentas.length; i++)
//                {
//                    if ($scope.cuentaMasDistribucion == $scope.cuentas[i].id) {
//                        $scope.compra.cuenta = $scope.cuentas[i];
//                    }
//                }
//                $scope.loading = false;
//            } else
//            {

                var data = {
                    idMoneda: 0,
                    idBanco: 0,
                    idTipoPago: $scope.compra.tipoPago.id
                };
                var url = PATH.compraCreditos + '/cuentas';
                var request = $http({
                    method: "post",
                    url: url,
                    data: data
                });
                request.success($scope.onListaCuentas);
//              }
        } else {
            var data = {
                idMoneda: 0,
                idBanco: 0,
                idTipoPago: 0
            };
            var url = PATH.compraCreditos + '/cuentas';
            var request = $http({
                method: "post",
                url: url,
                data: data
            });
            request.success($scope.onListaCuentas);
        }


    };

    $scope.onListaCuentas = function (data)
    {
        $scope.loading = false;
        var list = [];
        if (data.error)
        {
            $scope.cuentas = list;
            $scope.compra.cuenta = "";
        } else
        {
            list = data === null ? [] : data.cuentas;
            $scope.cuentas = list;
            $scope.compra.cuenta = $scope.getSelectedCuenta();
        }
    };

    $scope.getSelectedCuenta = function (  )
    {
        var selected = "";
        for (var i = 0; i < $scope.cuentas.length; i++)
        {
            if ($scope.compra.cuentaId == $scope.cuentas[i].id)
            {
                return $scope.cuentas[i];
            }
        }
        return selected;
    };

    $scope.onChangeCuenta = function () {
        
//        for (var i = 0; i < $scope.tiposPago.length; i++)
//        {
//            var cuentaIDPago = parseInt($scope.compra.cuenta.intIDTipoPago);
//            var pagoIdPago = parseInt($scope.tiposPago[i].id);
//            if (cuentaIDPago === pagoIdPago)
//            {
//                $scope.compra.tipoPago = $scope.tiposPago[i];
//
//            }
//        }

        for (var i = 0; i < $scope.bancos.length; i++)
        {
            var cuentaIDBanco = parseInt($scope.compra.cuenta.intIDBanco);
            var bancoIdBanco = parseInt($scope.bancos[i].id);
            if (cuentaIDBanco === bancoIdBanco)
            {
                $scope.compra.banco = $scope.bancos[i];

            }
        }
        for (var i = 0; i < $scope.monedas.length; i++)
        {
            if (parseInt($scope.compra.cuenta.intIdTipoMoneda) === parseInt($scope.monedas[i].id))
            {
                $scope.compra.moneda = $scope.monedas[i];
            }
        }
        
    };

    $scope.back = function () {
        $state.go('^', $stateParams);
    };

    $scope.getFormFieldCssClass = function (ngModelController) {
        if (ngModelController.$pristine)
            return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    };


    this.init();
}