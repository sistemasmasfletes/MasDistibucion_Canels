function CiudadesIndexController($rootScope, $scope, $state, $stateParams, ModalService, CiudadesDataService, PATH, PARTIALPATH, $injector, CONFIG) {
    ngTableParams = $injector.get('ngTableParams');
    UtilsService = $injector.get('UtilsService');
    var modalPath = PARTIALPATH.modal;
    $scope.isLoading = false;
    $scope.partials = PARTIALPATH.base;
    $scope.grid = {};
    
    $scope.grid.edit = function(){
        return grdEdit($scope.id);
    };
    
    $scope.grid.delete = function () {
        grdDelete($scope.id, $scope.ciudad);
    };
    
    $scope.tableParams = new ngTableParams(
            {page: 1,
                count: 10,
                sorting: {
                    name: ' asc '
                }
            },
    {
        total: 0,
        getData: function ($defer, params) {
            var postParams = UtilsService.createNgTablePostParams(params);

            CiudadesDataService.getCiudades(postParams)
                    .then(function (response) {
                        var data = response.data;
                        $scope.isLoading = false;
                        params.total(data.meta.totalRecords);
                        $defer.resolve(data.data);
                        $scope.adata = data.data;
                    });
        }
    }
    );
    
    $scope.changeSelection = function(ciudad){
        var data = $scope.tableParams.data;
        for(var i = 0; i < data.length; i += 1){
            if(data[i].id != ciudad.id){
                data[i].$selected = false;
            }
        }
        $scope.id = ciudad.id;
        $scope.ciudad = ciudad;
    }
    
    $scope.add = function(){
        $state.go('ciudades.add');
    }
    
    
    function grdEdit(id){
        if(id){
            $state.go('ciudades.edit', {ciudadId:id});
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para editar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
            });
        }
    }
    
    function grdDelete(id, ciudad) {
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar el registro?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                var ciudadId = ciudad.id;
                if (id == ciudadId) {
                    CiudadesDataService.delete(ciudad);
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
    
    $scope.toggleFilter = function(params){
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }
    
    $scope.customFilter = [
            {name: 'name', type: 'text', label: 'Ciudad'},
            {name: 'state', type: 'text', label: 'Estado'},
            {name: 'country', type: 'text', label: 'País'}
    ];
    
    $scope.filterOpen = false;
    $scope.openFilter = function(){
        $scope.filterOpen = true;
    }
    
    $scope.appFilter = function(filter){
        $scope.tableParams.settings().filterDelay = 0;
        $scope.tableParams.$params.filter = filter;
    }

}