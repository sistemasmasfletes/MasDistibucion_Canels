function ContactEditController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,PointsDataService,contact,CONFIG){
    
    //console.log($stateParams.pointId);
    $scope.contact=contact;
    $scope.save=save;
    $scope.back=function(){$state.go('^',$stateParams)};
    $scope.pointStatus = CatalogService.getContactStatus();
    $scope.phoneNumberPattern = (function () {
        var regexp = /^\(?(\d{3})\)?[ .-]?(\d{3})[ .-]?(\d{4})$/;
        return {
            test: function (value) {
                if ($scope.requireTel === false) {
                    return true;
                }
                return regexp.test(value);
            }
        };
    })();
    function save(){
        if($scope.contact){
            //console.log($scope.contact);
            $scope.loading=true;
            $scope.contact.point_id = $stateParams.pointId;
            PointsDataService.saveContact($scope.contact, {})
                .success(function(data, status, headers, config){
                    $scope.loading=false;
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
                })
                .error(function(data, status, headers, config){
                    $scope.loading = false;
                });
        }
    }
}