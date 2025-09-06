function AddressesEditController($scope, $timeout, $state, $stateParams, PARTIALPATH, ModalService, CatalogService, AddressesDataService, address, CONFIG) {
    $scope.address = address;
    $scope.save = save;
    $scope.back = function () {
        $state.go('^', $stateParams)
    };
    $scope.userRole;
    $scope.getState = [];
    $scope.getAuthorization = [];
    $scope.getZones = [];

    $scope.getAuthorization = CatalogService.getAddressAuthorization();
    CatalogService.getUserRole()
            .then(function (response) {
                $scope.userRole = response.data;
            });


    CatalogService.getZonaByUser()
            .then(function (response) {
                $scope.onListaZonas(response);
            });

    $scope.onListaZonas = function (data)
    {
        var list = [];
        if (data.error)
        {
            $scope.getZones = list;
            $scope.address.zona = "";
        } else
        {
            list = data === null ? [] : (data.data instanceof Array ? data.data : [data.data]);
            $scope.getZones = list;
            $scope.address.zone_id = $scope.getSelectedZona();
        }
        $scope.loading = false;
    }

    $scope.getSelectedZona = function ()
    {
        var selected = "";
        for (var i = 0; i < $scope.getZones.length; i++)
        {
            if ($scope.address.zone_id == $scope.getZones[i].id)
            {
                return $scope.getZones[i];
            }
        }
        return selected;
    };

    AddressesDataService.getCountry($scope.address, {})
            .then(function (response) {
                $scope.getCountry = response.data;
                if ($scope.address.state_id) {
                    AddressesDataService.getState({id: $scope.address.country_id})
                            .then(function (response) {
                                $scope.getState = response.data;
                                if ($scope.address.city_id) {
                                    AddressesDataService.getCity({id: $scope.address.state_id})
                                            .then(function (response) {
                                                $scope.getCity = response.data;
                                            });
                                }
                            });
                }
            });

    $scope.getSelectedCountry = function () {
        var countryId = $scope.address.country_id;
        if (  countryId !== undefined && countryId !== "") {
            var postParams = {id: countryId};
            AddressesDataService.getState(postParams)
                    .then(function (response) {
                        $scope.getState = response.data;
                        $scope.address.state_id = $scope.getStateData(response.data);
                        $scope.getSelectedState();
                    });
        } else {
            $scope.getState = "";
            $scope.getCity = "";
        }

    };
    
    $scope.getStateData = function (data){
        for(var i = 0;i < data.length ; i++){
            if(data[i].id == $scope.address.state_id){
                return data[i].id;
            }
        }
        $scope.address.city_id = "";
        return "";
    };
    
    $scope.getSelectedState = function () {
        $scope.getState;
        var stateId = $scope.address.state_id;
        if (  stateId !== undefined &&stateId !== "") {
            var postParams = {id: stateId};
            AddressesDataService.getCity(postParams)
                    .then(function (response) {
                        $scope.getCity  = response.data;
                        $scope.address.city_id = $scope.getCityData(response.data);
                    });
        } else {
            $scope.getCity = "";
        }

    };
    
    $scope.getCityData = function(data){
        for(var i = 0; i < data.length ; i++){
            if(data[i].id == $scope.address.city_id){
                return data[i];
            }
        }
        return "";
    };

    function save() {
        if ($scope.address) {
            $scope.loading = true;
            AddressesDataService.save($scope.address, {})
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
                    })
                    .error(function (data, status, headers, config) {
                        $scope.loading = false;
                    });
        }
    }
}