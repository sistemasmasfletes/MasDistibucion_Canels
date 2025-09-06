function BancosEditController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,CatalogService,BancosDataService,banco,$http){
    $scope.banco = banco;
    $scope.bancoStatus = CatalogService.getBancoStatus(); //definir en CatalogService
    $scope.bancoTypes = CatalogService.getBancoType();

    $scope.save=save;
    
    $scope.back=function(){
        $state.go('^',$stateParams);
    };
    
    $scope.init = function() 
    {
        
        if($scope.banco.estado === "Activo")
        {
            $scope.chek = true;
            $scope.banco.estado = "Activo";
        }
        else
        {
            $scope.chek = false;
            $scope.banco.estado = "Inactivo";
        }
        
    };
    
    function save()
    {
        if($scope.banco)
        {
            $scope.loading=true;
            $scope.verificarExistencia();
        }
    }
    
    $scope.verificarExistencia = function() {
        
        var data = {banco: $scope.banco};
        var url = PATH.bancos + 'fncVerificar';

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
                BancosDataService.save($scope.banco, {})
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

    $scope.fncCambia = function() 
    {
        $scope.banco.estado = $scope.banco.estado.valueOf();
    };

    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    };
    $scope.init();
}