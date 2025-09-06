function RoutesEditController($scope,$cookieStore,$timeout,$state,$stateParams,PARTIALPATH,ModalService, JQGridService,CatalogService,RoutesDataService,route){
	$scope.route=route;
    $scope.progressbar={loading:false}

    $scope.regresar=function(){
        $state.go('^', $stateParams);
    }

    $scope.points=function(){
        $state.go('routes.edit.points');
    }

    $scope.routeStatus=CatalogService.getRouteStatus();
    $scope.getZonas = [];
    CatalogService.getCatalogZonaByController()
            .then(function(response){
                $scope.getZonas = response.data[0];
    });

    //$scope.franchisee_id=CatalogService.getFranchisee();
    $scope.getFranchisee = [];
    CatalogService.getFranchisee()
            .then(function(response){
                $scope.getFranchisee = response.data[0];
    });

    $scope.save = function(){
        if($scope.route){
            $scope.progressbar.loading=true;
            config.alertOnSuccess=true;
            RoutesDataService.save($scope.route, config)
                .success(function(data, status, headers, config){
                    $scope.progressbar.loading=false;                    
                    if (!data.error) {
                        $state.go('^', $stateParams);
                        $scope.grid.refresh();                            
                    }                        
                })
                .error(function(data, status, headers, config){
                    $scope.progressbar.loading = false;                    
                });
        }
    }

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    }
}