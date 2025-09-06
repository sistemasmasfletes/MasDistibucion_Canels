function CiudadesEditController($scope, $timeout, $state, $stateParams, $filter, PARTIALPATH, ModalService, CatalogService, CiudadesDataService, UtilsService, ciudad, CONFIG) {
    $scope.ciudad = ciudad;
    if($scope.ciudad.chrEstatus != '1'){
       $scope.ciudad.chrEstatus = false; 
    }else{
        $scope.ciudad.chrEstatus = true;
    }
    $scope.save = save;
    $scope.back = function () {
        $state.go('^', $stateParams)
    };

    CiudadesDataService.getCountry($scope.ciudad, {})
            .then(function (response) {
                $scope.onListaPaises(response);
            });


    $scope.onListaPaises = function (data) {
        var list = [];
        if (data.error) {
            $scope.getCountry = list;
            $scope.ciudad.countryId = "";
        } else {
            list = data === null ? [] : (data.data instanceof Array ? data.data : [data.data]);
            $scope.getCountry = list;
            $scope.ciudad.countryId = $scope.getSelectedCountry();
            CiudadesDataService.getStatesByCountryId({countryId: $scope.ciudad.countryId.id})
                    .then(function (response) {
                        $scope.onListaEstados(response);
                    });
        }

    };

    $scope.populateStates = function(){
        CiudadesDataService.getStatesByCountryId({countryId: $scope.ciudad.countryId.id})
                    .then(function (response) {
                        $scope.onListaEstados(response);
                    });
    };

    $scope.getSelectedCountry = function () {
        var selected = "";
        for (var i = 0; i < $scope.getCountry.length; i++) {
            if ($scope.ciudad.countryId == $scope.getCountry[i].id) {
                return $scope.getCountry[i];
            }
        }
        return selected;
    };

    $scope.onListaEstados = function (data) {
        var list = [];
        if (data.error) {
            $scope.getState = list;
            $scope.ciudad.stateId = "";
        } else {
            list = data === null ? [] : (data.data.data instanceof Array ? data.data.data : [data.data.data]);
            $scope.getState = list;
            $scope.ciudad.stateId = $scope.getSelectedState();
        }
    };

    $scope.getSelectedState = function () {
        var selected = "";
        for (var i = 0; i < $scope.getState.length; i++) {
            if ($scope.ciudad.stateId == $scope.getState[i].id) {
                return $scope.getState[i];
            }
        }
        return selected;
    };

    function save() {
        if ($scope.ciudad) {
            $scope.loading = true;
            CiudadesDataService.save({id:$scope.ciudad.id, countryId: $scope.ciudad.countryId.id, stateId: $scope.ciudad.stateId.id, name:$scope.ciudad.name, chrEstatus:$scope.ciudad.chrEstatus }, {})
                    .success(function (data, status, headers, config) {
                        $scope.loading = false;
                        if (data.error) {
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: data.error
                            };
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions);
                        } else {
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: '¡Registro guardado con éxito!'
                            };
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                $scope.back();
                                $scope.tableParams.reload();
                            });
                        }
                    }).error(function (data, status, headers, config) {
                $scope.loading = false;
            });
        }
    }

}