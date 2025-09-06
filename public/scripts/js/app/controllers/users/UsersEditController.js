function UsersEditController($scope, $timeout, $state, $stateParams, PARTIALPATH, ModalService, CatalogService, UsersDataService, user) {
    $scope.user = user;
    
    $scope.userType = $scope.user.type;
    $scope.userStatus = CatalogService.getUserStatus();
    $scope.getCategories = [];
    $scope.getMonedas = [];
    $scope.getZonas = [];
    $scope.role;

    CatalogService.getUserType()
            .then(function (response) {
            	$scope.userTypes = response.data;
            });
    
    CatalogService.getCatalogMoneda()
            .then(function (response) {
                $scope.getMonedas = response.data;
            });

    CatalogService.getUserRole()
            .then(function (response) {
                $scope.role = response.data;
                if ($scope.role == 1) {
                    CatalogService.getCatalogZona()
                            .then(function (response) {
                                $scope.getZonas = response.data;
                            });
                } else {
                    CatalogService.getZonaByUser()
                            .then(function (response) {
                                $scope.getZonas = response.data;
                            });
                }
            });





    CatalogService.getBranchCategories()
            .then(function (response) {
                $scope.getCategories = response.data;
            });
    CatalogService.getCatalogPoints()
            .then(function (response) {
                $scope.getPoint = response.data;
            });

    $scope.categoriesId = function () {
        if($scope.user.type != 3){
            $scope.user.category_id = undefined;
        }
        
        if($scope.user.type != 6){
            $scope.user.point_id = undefined;
        }
        
        if($scope.user.type != 2 || $scope.user.type != 7){
            $scope.user.zonas_id = undefined;
        }
    };

    $scope.back = function () {
        $state.go('^', $stateParams)
    };
    $scope.save = save;

    function save() {
        if ($scope.user) {
            $scope.loading = true;
            //$scope.user.point_id=null;
            if ($scope.user.category_id === '')
                $scope.user.category_id = null;
            UsersDataService.save($scope.user, {})
                    .success(function (data, status, headers, config) {
                        $scope.loading = false;
                        if (data.error) {
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: data.error
                            };
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions)
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
                    })
                    .error(function (data, status, headers, config) {
                        $scope.loading = false;
                    });
        }
    }

    $scope.getFormFieldCssClass = function (ngModelController) {
        if (ngModelController.$pristine)
            return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    }

}