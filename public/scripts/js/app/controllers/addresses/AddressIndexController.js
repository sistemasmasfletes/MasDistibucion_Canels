function AddressIndexController($rootScope, $scope, $timeout, $state, $stateParams, ngTableParams, PARTIALPATH, ModalService, AddressesDataService, UtilsService, CONFIG) {

    var modalPath = PARTIALPATH.modal
    $scope.partials = PARTIALPATH.base;
    $scope.grid = {};
    $scope.isLoading = false;
    $scope.userRole;
    $scope.postD = "";

    $scope.grid.edit = function () {
        return grdEdit($scope.id)
    };
    $scope.grid.delete = function () {
        grdDelete($scope.id, $scope.schedule)
    };
    $scope.partials = CONFIG.PARTIALS;
    AddressesDataService.getUserRole().then(function (response) {
        $scope.userRole = response.data;
    });
    $scope.tableParams = new ngTableParams(
            {page: 1,
                count: 10,
                sorting: {
                    country: 'desc'
                }
            },
            {
                total: 0,
                getData: function ($defer, params) {
                	
                   /* var postParams = {page:params.page(), rowsPerPage:params.count(), srch:$scope.postD};
                    var sorting = params.sorting();
                    var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                    if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                    PointsDataService.getPoints(postParams)*/
                	
                	
                    var postParams = UtilsService.createNgTablePostParams(params);
//            var sorting = params.sorting();
//            var sortField = UtilsService.getKeysFromJsonOnject(sorting)[0];

//            if (sorting)
//                angular.extend(postParams, {sortField: sortField, sortDir: sorting[sortField]});

                    postParams.srch = $scope.postD;
                    AddressesDataService.getAddInformation(postParams)
                            .then(function (response) {
                                var data = response.data;
                                $scope.isLoading = false;
                                $defer.resolve(data.data);
                            });
                }
            }

    );

    $scope.changeSelection = function (schedule) {
        var data = $scope.tableParams.data;
        for (var i = 0; i < data.length; i++) {
            if (data[i].id != schedule.id)
                data[i].$selected = false;
        }
        $scope.id = schedule.id;
        $scope.schedule = schedule;
    }

    $scope.search = function(data){
    	$scope.postD = data
    	$scope.tableParams.reload();
    }
    
    $scope.add = function () {
        $state.go('addresses.add');
    }

    function grdEdit(id) {
        if (id) {
            $state.go('addresses.edit', {addressId: id})
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para editar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
            });
        }
    }

    function grdDelete(id, schedule) {
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar el registro?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                var addressId = schedule.id;
                if (id == addressId) {
                    AddressesDataService.delete(schedule)
                            .then(function (response) {
                                if (response.data.error) {
                                    var modalOptions3 = {
                                        closeButtonText: 'Aceptar',
                                        bodyText:  response.data.error
                                    };
                                    ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions3);
                                }
                            });
                }
                $scope.tableParams.reload();
            });
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para eliminar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
            });
        }
    }

    $scope.toggleFilter = function (params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.customFilter = [
        {name: 'Pais', type: 'text', label: 'País'},
        {name: 'Estado', type: 'text', label: 'Estado'},
        {name: 'Ciudad', type: 'text', label: 'Ciudad'},
        {name: 'Calle', type: 'text', label: 'Calle'},
        {name: 'CP', type: 'text', label: 'Código Postal'}
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function () {
        $scope.filterOpen = true;
    }

    $scope.appFilter = function (filter) {
        $scope.tableParams.settings().filterDelay = 0;
        $scope.tableParams.$params.filter = filter;
    }
}