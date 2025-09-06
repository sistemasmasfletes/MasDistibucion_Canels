function InventoryIndexController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,ModalService,InventoryDataService,UtilsService,CONFIG){
    
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                shipping_date:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){     
                var postParams = {page:params.page(), rowsPerPage:params.count()};
                var filter=params.filter();
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                if(filter) angular.extend(postParams,{filter:filter});

                InventoryDataService.getInventory(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    //params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                });       
            }
        }

    );

    $scope.changeSelection = function(inventory) {
        var data = $scope.tableParams.data;
        var $id = inventory.vehicleId;
        for(var i=0;i<data.length;i++){
            if(data[i].id!=inventory.id)
                data[i].$selected=false;
        }
        //$state.go('inventory.view',{id:$id}); //ir a paquetes de carro
    }
}