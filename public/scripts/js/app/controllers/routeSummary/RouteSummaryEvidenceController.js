//PANTALLA DE INFORMACIÓN DE PAQUETE
function RouteSummaryEvidenceController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,ModalService,RouteSummaryDataService,UtilsService,CONFIG){
    
    var $scheduleId = $stateParams.scheId;
    var $routePointId = $stateParams.point;
    var $id = $stateParams.id;
    var $rpaId = $stateParams.rpaId;
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
                console.log($stateParams);
                var $id=$stateParams.id;
                var postParams = {page:params.page(), rowsPerPage:params.count(),ocId:$id};
                var filter=params.filter();
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                if(filter) angular.extend(postParams,{filter:filter});

                RouteSummaryDataService.getEvidence(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    //params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                });       
            }
        }

    );
        
	$scope.changeSelection = function(info) {
        var data = $scope.tableParams.data;        
        for(var i=0;i<data.length;i++){
            if(data[i].id!=info.id)
                data[i].$selected=false;
            }
        }
        
        $scope.regresar=function(){
            //$state.go('routeSummary.view') //NAVEGAR A INFORMACIÓN DEL PAQUETE
            $state.go('routeSummary.view',{scheId:$scheduleId,point:$routePointId,id:$id,rpaId:$rpaId}); //NAVEGA A PANTALLA DE SALVAR EVIDENCIA
        }
        
}