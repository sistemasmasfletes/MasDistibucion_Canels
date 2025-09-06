function InventoryPacksController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,ModalService,InventoryDataService,UtilsService,CONFIG){
    
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
                var vehicle= $stateParams.id;
                var postParams = {page:params.page(), rowsPerPage:params.count(),idrow:vehicle};
                
                var filter=params.filter();
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                if(filter) angular.extend(postParams,{filter:filter});

                InventoryDataService.getInventoryPacks(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    //params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                });       
            }
        }

    );

    $scope.changeSelection = function(pack) {
        var data = $scope.tableParams.data;        
        for(var i=0;i<data.length;i++){
            if(data[i].id!=pack.id)
                data[i].$selected=false;
        }
    }
    
    $scope.regresar=function(){
        $state.go('inventory'); //NAVEGA A PANTALLA DE inventario principal
    }
    
}