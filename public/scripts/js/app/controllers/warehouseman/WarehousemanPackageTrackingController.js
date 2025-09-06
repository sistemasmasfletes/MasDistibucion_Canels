function WarehousemanPackageTrackingController($scope,$timeout,$state,$stateParams,ngTableParams,ModalService,WarehousemanDataService,UtilsService,CONFIG){
    
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.currentData = [];
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                routeName:'asc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = UtilsService.createNgTablePostParams(params,{orderId:$stateParams.orderId});
                
                WarehousemanDataService.getPackageTrackingWarehouseman(postParams)
                .then(function(response){
                    $scope.currentData=response.data;
                    $scope.isLoading=false;
                    params.total($scope.currentData.meta.totalRecords);
                    $defer.resolve($scope.currentData.data[0]);
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
    
    $scope.getDateFromString = UtilsService.getDateFromString;
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    

    $scope.customFilter = [
    	{name:'orderId',type:'text',label:'Id Paquete'},
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function(){
        $scope.filterOpen = true;  
    }

    $scope.appFilter = function(filter){
        $scope.tableParams.settings().filterDelay=0
        $scope.tableParams.$params.filter=filter;
        
    }
}