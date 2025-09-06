function WarehousemanRejectedController($scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,WarehousemanDataService,UtilsService,CONFIG){
    
    var modalPath = PARTIALPATH.modal
    $scope.partials = PARTIALPATH.base;
    $scope.grid = {};
    $scope.isLoading=false;
    $scope.grid.edit = function(){return grdEdit($scope.id)};
    $scope.back=function(){$state.go('^',$stateParams)};
    /*$scope.grid.delete=function(){grdDelete($scope.id, $scope.schedule)};*/
    $scope.partials = CONFIG.PARTIALS;
    $scope.currentData = [];
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                0:'asc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = UtilsService.createNgTablePostParams(params,{orderId:$stateParams.orderId});
                /*var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});*/
                
                
                WarehousemanDataService.getPackageRejected(postParams)
                .then(function(response){
                    $scope.currentData=response.data;
                    $scope.isLoading=false;
                    params.total($scope.currentData.meta.totalRecords);
                    $defer.resolve($scope.currentData.data);
                });       
            }
        }

    );
        
    $scope.changeSelection = function(schedule) {       
        var data = $scope.tableParams.data;
        for(var i=0;i<data.length;i++){
            if(data[i].id!=schedule.id)
                data[i].$selected=false;
        }
        $scope.id = schedule[0];
        $scope.schedule = schedule;
        
    }
    
    /*$scope.add = function(){
        $state.go('points.add');
        
    }*/
    
    function grdEdit(id){
        if(id){
        	//alert($state.toSource())
            $state.go('warehouseman.edit',{Oid:id})
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para editar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
        }
    }

    /*function grdDelete(id,schedule){
        if(id){
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar este Punto de Venta?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                var pointId = schedule.id;
                if(id==pointId){
                    PointsDataService.delete(schedule);
                }
                $scope.tableParams.reload();
            });
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para eliminar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
        }
    }*/    
    
    /*$scope.getDateFromString = UtilsService.getDateFromString;
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.customFilter = [
    	{name:'orderId',type:'text',label:'OC'},
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function(){
        $scope.filterOpen = true;  
    }

    $scope.appFilter = function(filter){
        $scope.tableParams.settings().filterDelay=0
        $scope.tableParams.$params.filter=filter;
        
    }*/

}