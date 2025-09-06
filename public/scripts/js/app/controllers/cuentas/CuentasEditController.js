function CuentasEditController($scope, $timeout, $state, $stateParams, PATH, PARTIALPATH, ModalService, CatalogService, CuentasDataService, cuentas, $http) {
    $scope.cuenta = cuentas;
    $scope.cuentasStatus = CatalogService.getCuentasStatus( );
    $scope.cuentasTypes = CatalogService.getCuentasType( );
    $scope.existe = false;
    $scope.monedas = [];
    $scope.bancos = [];
    $scope.paises = [];
    $scope.tiposPago = [];
    $scope.operadores = [];
    $scope.regex = '^[0-9]{16}$';

    $scope.save = save;

    this.init = function () {
        $scope.disable = false;
        $scope.listaMonedas();
        $scope.listaBancos();
        $scope.listaPaises();
        $scope.listaOperadores();
        $scope.listaTiposPago();
    };

    $scope.back = function ( ) {
        $state.go('^', $stateParams);
    };

    function save()
    {
        $scope.verificarExistencia();
    }

    $scope.verificarExistencia = function ()
    {

        var data = {cuenta: $scope.cuenta};
        var url = PATH.cuentas + '/fncVerificar';

        var request = $http({
            method: "post",
            url: url,
            data: data
        });

        request.success($scope.successVerificar);
    };

    $scope.successVerificar = function (data)
    {

        $scope.si_existe = 1;
        if (data.error)
        {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: data.error
            };
            ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
        } else
        {
            $scope.existe = data["existe"];

            if ($scope.existe === $scope.si_existe)
            {
                var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: "El registro ya existe"
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                    $scope.loading = false;
                });
            } else
            {
                CuentasDataService.save($scope.cuenta, {})
                        .success(onSave)
                        .error(function (data, status, headers, config) {
                            $scope.loading = false;
                        });
            }
        }
    };

    function onSave(data, status, headers, config) {
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
                $scope.back();
                $scope.grid.refresh();
            });
        }
    }
    ;

    $scope.listaMonedas = function () {

        $scope.loading = true;
        var data = {
            metodo: 'monedas'
        };
        var request = $http({
            method: "post",
            url: PATH.cuentas + '/monedas',
            data: data
        });
        request.success($scope.onListamonedas);
    }

    $scope.onListamonedas = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.monedas = list;
            $scope.cuenta.moneda = "";
        } else
        {
            list = data === null ? [] : (data.monedas instanceof Array ? data.monedas : [data.monedas]);
            $scope.monedas = list;
            $scope.cuenta.idMoneda = $scope.getSelectedMoneda();
        }
        $scope.loading = false;
    }

    $scope.getSelectedMoneda = function ()
    {
        var selected = "";
        for (var i = 0; i < $scope.monedas.length; i++)
        {
            if ($scope.cuenta.idMoneda == $scope.monedas[i].id)
            {
                return $scope.monedas[i];
            }
        }
        return selected;
    };


    $scope.listaBancos = function () {
        $scope.loading = true;
        var data = {
            metodo: 'bancos'
        };
        var request = $http({
            method: "post",
            url: PATH.cuentas + '/bancos',
            data: data
        });
        request.success($scope.onListabancos);
    }

    $scope.onListabancos = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.bancos = list;
            $scope.cuenta.idBanco = "";
        } else
        {
            list = data === null ? [] : (data.bancos instanceof Array ? data.bancos : [data.bancos]);
            $scope.bancos = list;
            $scope.cuenta.idBanco = $scope.getSelectedBanco();
        }
        $scope.loading = false;
    }

    $scope.getSelectedBanco = function ()
    {
        var selected = "";
        for (var i = 0; i < $scope.bancos.length; i++)
        {
            if ($scope.cuenta.idBanco == $scope.bancos[i].id)
            {
                return $scope.bancos[i];
            }
        }
        return selected;
    };

    $scope.listaPaises = function () {
        $scope.loading = true;
        var data = {
            metodo: 'paises'
        };
        var request = $http({
            method: "post",
            url: PATH.cuentas + '/paises',
            data: data
        });
        request.success($scope.onListapaises);
    }

    $scope.onListapaises = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.paises = list;
            $scope.cuenta.idPais = "";
        } else
        {
            list = data === null ? [] : (data.paises instanceof Array ? data.paises : [data.paises]);
            $scope.paises = list;
            $scope.cuenta.idPais = $scope.getSelectedPais();
        }

        $scope.loading = false;
    }

    $scope.getSelectedPais = function ()
    {
        var selected = "";
        for (var i = 0; i < $scope.paises.length; i++)
        {
            if ($scope.cuenta.idPais == $scope.paises[i].id)
            {
                return $scope.paises[i];
            }
        }
        return selected;
    };


    $scope.listaOperadores = function () {
        $scope.loading = true;
        var data = {
            metodo: 'operadores'
        };
        var request = $http({
            method: "post",
            url: PATH.cardOperators + 'getCardOperators',
            data: data
        });
        request.success($scope.onListaOperadores);
    }

    $scope.onListaOperadores = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.operadores = list;
            $scope.cuenta.idOperador = "";
        } else
        {
            list = data === null ? [] : (data.data instanceof Array ? data.data : [data.data]);
            $scope.operadores = list;
            $scope.cuenta.idOperador = $scope.getSelectedOperador();
        }

        $scope.loading = false;
    }

    $scope.getSelectedOperador = function ()
    {
        var selected = "";
        for (var i = 0; i < $scope.operadores.length; i++)
        {
            if (($scope.cuenta.idOperador == $scope.operadores[i].id) ||
                    ($scope.cuenta.tipoOperador.replace(/\s/g, '').toUpperCase() == $scope.operadores[i].chrOperator.replace(/\s/g, '').toUpperCase()))
            {
                return $scope.operadores[i];
            }
        }
        return selected;
    };

    $scope.getFormFieldCssClass = function (ngModelController) {
        if (ngModelController.$pristine)
            return "";
        return ngModelController.$valid ? "has-success" : "has-error";
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
            url: PATH.cuentas + '/tipoPagos',
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
            $scope.cuenta.tipoPago = "";
        } else
        {
            list = data === null ? [] : (data.tipoPagos instanceof Array ? data.tipoPagos : [data.tipoPagos]);
            $scope.tiposPago = list;
            $scope.cuenta.tipoPago = $scope.getSelectedTipoPago();
        }
        $scope.loading = false;
    };

    $scope.getSelectedTipoPago = function ( )
    {
        var selected = "";
        for (var i = 0; i < $scope.tiposPago.length; i++)
        {
            if ($scope.cuenta.idTipoPago === $scope.tiposPago[i].id)
            {
                return $scope.tiposPago[i];
            }
        }

        return selected;
    };


    this.init();
}