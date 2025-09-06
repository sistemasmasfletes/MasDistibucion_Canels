function ActivityTypeIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,ActivityTypeDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.activityId)};
    $scope.grid.edit=function(){grdEdit($scope.activityId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;

    $scope.grid.config = JQGridService.config({
    url: PATH.activityType + 'getActivityType',
    colNames: ["id", "Nombre", "Descripción"],
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
        {name: "name", index: "name", width: 120},
        {name: "description", index: "description", width: 80},
    ],
    sortname: "name",
    sortorder: "asc",
    caption: "Tipo de actividad",
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.activityId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                ActivityTypeDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.activityId = null;
            },0);
            JQGridService.resize('grdActivityType');
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('activityType.edit',{activityId:id})
        else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder editar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }

    }

    function grdDelete(id){
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar el registro?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                $scope.loading = true;
                ActivityTypeDataService.delete({id: id})
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
                             $scope.grid.api.refresh();
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

    function grdRefresh(){
        $scope.grid.api.refresh();
    }

    function grdAdd(){
        $state.go('activityType.add');
    }
}