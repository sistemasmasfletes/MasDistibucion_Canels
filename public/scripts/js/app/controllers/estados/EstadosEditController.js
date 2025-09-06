function EstadosEditController($scope,$timeout,$state,$stateParams,$filter,PARTIALPATH,ModalService,CatalogService,EstadosDataService,UtilsService,estado, CONFIG, $http, PATH){
    $scope.estado = estado;
    $scope.save = save;
    $scope.back = function () {
        $state.go('^', $stateParams)
    };
    
    EstadosDataService.getCountry($scope.estado, {})
            .then(function(response){
                $scope.onListaPaises(response);
    });
    
    
    $scope.onListaPaises = function(data){
        var list = [];
        if(data.error){
            $scope.getCountry = list;
            $scope.estado.countryId = "";
        } else {
            list = data === null ? [] : (data.data instanceof Array ? data.data : [data.data]);
            $scope.getCountry = list;
            $scope.estado.countryId = $scope.getSelectedCountry();
        }
    };
    
    $scope.getSelectedCountry = function(){
        var selected = "";
        for(var i = 0; i < $scope.getCountry.length; i++){
            if($scope.estado.countryId == $scope.getCountry[i].id){
                return $scope.getCountry[i];
            }
        }
        return selected;
    };
    $scope.verificarExistencia = function(){
        var data = {estado: $scope.estado};
        var url = PATH.estados + 'verificar';
        var request = $http({
            method:'POST',
            url: url,
            data: data
        });
        
        request.success($scope.successVerificar);
    };
    
    $scope.successVerificar = function(data){
        $scope.si_existe = 1;
        $scope.existe = data['existe'];
        
        if ($scope.existe != $scope.si_existe  ) {
                EstadosDataService.save($scope.estado, {})
                        .success(function (data, status, headers, config) {
                            $scope.loading = false;
                            if (data.error) {
                                var modalOptions = {
                                    actionButtonText: 'Aceptar',
                                    bodyText: data.error
                                };
                                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions);
                            } else {
                                var modalOptions = {
                                    actionButtonText: 'Aceptar',
                                    bodyText: '¡Registro guardado con éxito!'
                                };
                                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                    $scope.back();
                                    $scope.tableParams.reload();
                                });
                            }
                        }).error(function (data, status, headers, config) {
                    $scope.loading = false;
                });
            } else if($scope.existe == $scope.si_existe){
                var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: "El registro ya existe"
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions); 
                $scope.loading = false;
            }
    };
    
    function save() {
        if ($scope.estado) {
            $scope.loading = true;
            if($scope.estado.id != null){
                $scope.successVerificar({'existe':0});
            } else {
                $scope.verificarExistencia();
            }
            
        }
    }
    
}