//Pantalla de paquetes informativos
function ActivityPackageController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,RouteSummaryDataService,UtilsService,CONFIG){
    console.log($stateParams);
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    var scheduleId= $stateParams.id2;    
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                Paquete:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var idPack= $stateParams.id1; //routePointId
                var schedId = $stateParams.id2; //scheduleRouteId
                var oc = $stateParams.id4; //orden de compra
                var rpaId = $stateParams.id3; //routePointActivityId

                var postParams = {page:params.page(), rowsPerPage:params.count(),stateParams:$stateParams,idrow:idPack,idrow2:schedId,idrow3:oc,idrow4:rpaId};
                var filter=params.filter();
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                if(filter) angular.extend(postParams,{filter:filter});

                RouteSummaryDataService.getActivityPackage(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
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
        $state.go('routeSummary.edit',{id:scheduleId}); //NAVEGA A PANTALLA DE PUNTOS DE LA RUTA
    }
    
        $scope.toggleFilter = function (params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.customFilter = [
        {name: 'Paquete', type: 'text', label: 'Paquete'},
        {name: 'Actividad', type: 'text', label: 'Actividad'} 
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function () {
        $scope.filterOpen = true;
    }

    $scope.appFilter = function(filter) {
        $scope.tableParams.settings().filterDelay = 0;
        $scope.tableParams.$params.filter = filter;
    }
}
