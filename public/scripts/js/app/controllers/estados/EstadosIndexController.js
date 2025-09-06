function EstadosIndexController($rootScope, $scope, $state, $stateParams, ModalService, EstadosDataService, PATH, PARTIALPATH, $injector, CONFIG) {
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
        grdDelete($scope.id, $scope.estado)
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

            EstadosDataService.getEstados(postParams)
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
    
    $scope.changeSelection = function(estado){
        var data = $scope.tableParams.data;
        for(var i = 0; i < data.length; i += 1){
            if(data[i].id != estado.id){
                data[i].$selected = false;
            }
        }
        $scope.id = estado.id;
        $scope.estado = estado;
    }
    
    $scope.add = function(){
        $state.go('estados.add');
    }
    
    
    function grdEdit(id){
        if(id){
            $state.go('estados.edit', {estadoId:id});
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para editar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
            });
        }
    }
    
    function grdDelete(id, estado) {
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar el registro?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                var estadoId = estado.id;
                if (id == estadoId) {
                    EstadosDataService.delete(estado);
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
            {name: 'name', type: 'text', label: 'Estado'},
            {name: 'abbreviation', type: 'text', label: 'Abreviación'},
            {name: 'country', type: 'text', label: 'País'}
    ]
    
    $scope.filterOpen = false;
    $scope.openFilter = function(){
        $scope.filterOpen = true;
    }
    
    $scope.appFilter = function(filter){
        $scope.tableParams.settings().filterDelay = 0;
        $scope.tableParams.$params.filter = filter;
    }

}