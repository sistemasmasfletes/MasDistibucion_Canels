function InventoryWarehousemanController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,InventoryWarehousemanDataService,UtilsService,CONFIG)
{
    $scope.partials = PARTIALPATH.base;
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                start_date:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = {page:params.page(), rowsPerPage:params.count()};
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});

                InventoryWarehousemanDataService.getInventoryWarehouseman(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    //params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
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
    }
}