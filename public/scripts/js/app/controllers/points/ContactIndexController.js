function ContactIndexController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,PointsDataService,UtilsService,CONFIG){
    
    //console.log($stateParams);
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo
    $scope.partials = PARTIALPATH.base;
    $scope.grid = {};
    $scope.isLoading=false;
    $scope.grid.editContact = function(){return grdEdit($scope.id)};
    $scope.grid.deleteContact=function(){grdDelete($scope.id, $scope.schedule)};
    $scope.partials = CONFIG.PARTIALS;
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                start_date:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var $pointId = $stateParams.pointId;
                var postParams = {page:params.page(), rowsPerPage:params.count(), pointId:$pointId};
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});

                PointsDataService.getContact(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    $defer.resolve(data.data);
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
        $scope.id = schedule.id;
        $scope.schedule = schedule;
        
    }
    
    $scope.back = function(){
        $state.go('points');
    }
    
    $scope.addContact = function(){
        var idContact = $stateParams.id;
        $state.go('points.contact.add',{id:idContact});
    }
    
    function grdEdit(id){
        var data = $scope.tableParams.data;
        //console.log(data[0]);
        if(id){
            $state.go('points.contact.edit',{contactId:id})
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para editar es necesario primero seleccionar un registro'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
        }
    }
    
    function grdDelete(id){
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar este Punto de Venta?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                $scope.loading = true;
                PointsDataService.deleteContact({id: id})
                .success(function(data, status, headers, config) {
                    $scope.loading = false;
                    if(data.error){
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
                    }else{
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Registro eliminado con éxito!'
                        };
                        ModalService.showModal({templateUrl: modalInfoPath}, modalOptions).then(function (result){
                             $scope.tableParams.reload();
                        });
                    }                   
                    })
                .error(function(data, status, headers, config) {
                    $scope.loading = false;
                    var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: 'Ocurrió un error al eliminar el registro.'
                        };
                        ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
                });
            });
        }else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder eliminar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }
}