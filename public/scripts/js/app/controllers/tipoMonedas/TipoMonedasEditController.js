function TipoMonedasEditController($scope,$timeout,$state,$stateParams,PATH, PARTIALPATH,ModalService,CatalogService,TipoMonedasDataService,tipoMonedas, $http){
    $scope.monedas=tipoMonedas;
    $scope.tipoMonedasStatus=CatalogService.getTipoMonedasStatus(); //definir en CatalogService
    $scope.tipoMonedasTypes=CatalogService.getTipoMonedasType();

    $scope.save=save;
	$scope.back=function(){$state.go('^',$stateParams)};
    
    function save()
    {
        if($scope.monedas)
        {
            $scope.loading=true;
            $scope.verificarExistencia();
        }
    }
    
    $scope.verificarExistencia = function() {
        
        var data = {tipoMonedas: $scope.monedas};
        var url = PATH.tipoMonedas + '/fncVerificar';

        var request = $http({ 
            method: "post",
            url: url,
            data: data
        });

        request.success( $scope.successVerificar );
    };
    
    $scope.successVerificar = function(data) {
        
        $scope.si_existe = 1;
        if (data.error) 
        {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: data.error
            };
            ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
        }
        else
        {
            $scope.existe = data["existe"];

            if( $scope.existe === $scope.si_existe) 
            {
                var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: "El registro ya existe"
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                    $scope.loading = false; 
                }); 
            }
            else
            {
                TipoMonedasDataService.save($scope.monedas, {})
                .success( onSave )
                .error(function(data, status, headers, config){
                    $scope.loading = false;                    
                });
            }
        }
    };
    
    function onSave(data, status, headers, config){
        $scope.loading=false;
        if (data.error) {
             var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: data.error
            };
            ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
        } else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Registro guardado con éxito!'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                    $scope.back();
                    $scope.grid.refresh();
                });
            }
    };

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    }
}