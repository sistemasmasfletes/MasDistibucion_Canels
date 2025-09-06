function ActividaChoferLogEditController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,ActividaChoferLogDataService, $http,actividaChoferLog){
    
    $scope.actividaChoferLog = actividaChoferLog;
   
    $scope.fecha = new Date();
    $scope.actividades = [];
    
    $scope.save = save;
    
    this.init = function() 
    {
      fncSetComboActividad();
    };
   
    $scope.back = function()
    {
        $state.go('^',$stateParams);
    };

    function fncSetComboActividad()
    {

        if($scope.actividaChoferLog.actividadTipoId === 4)
        {
            $scope.actividades = [{name:'Cobrar', id:'3'}];
        }
        else
        {
            $scope.actividades = [{name:'Entregar', id:'2'}];
        }
        $scope.actividaChoferLog.actividad = ""; // red
    };

    function save()
    {
        
        if($scope.actividaChoferLog)
        {
            $scope.loading=true;
            ActividaChoferLogDataService.save($scope.actividaChoferLog, {})
                .success(function(data)
                {
                    $scope.loading=false;
                    if (data.error) 
                    {
                         var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
                    } else 
                    {
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Registro guardado con éxito!'
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                $scope.back();
                                $scope.grid.refresh();
                            });
                    }
                })
                .error(function(data, status, headers, config){
                    $scope.loading = false;                    
                });
        }
    }
    
    

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    };
    this.init();
}