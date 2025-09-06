function TransactionTypeIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,TransactionTypeDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.transactionId)};
    $scope.grid.edit=function(){grdEdit($scope.transactionId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;

    $scope.grid.config = JQGridService.config({
    url: PATH.transactionType + 'getTransactionType',
    colNames: ["id", "Nombre"],
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
        {name: "name", index: "name", width: 120},
    ],
    sortname: "name",
    sortorder: "asc",
    caption: "Tipo de transaccion",
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.transactionId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                TransactionTypeDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.transactionId = null;
            },0);
            JQGridService.resize('grdTransactionType');
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('transactionType.edit',{transactionId:id})
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
                TransactionTypeDataService.delete({id: id})
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
        $state.go('transactionType.add');
    }
}