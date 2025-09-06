function ActividadesChoferIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,ActividadesChoferDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.actividadesId)};
    $scope.grid.edit=function(){grdEdit($scope.actividadesId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;

    $scope.grid.config = JQGridService.config({
    url: PATH.actividadesChofer + '/getActividadchofer',
    colNames: ["id", "Fecha", "Tipo de Movimiento","Tipo de Pago", "Referencia","Concepto", "Estado", "Valor", "Saldo en Caja", "Compre Credito"],
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
        {name: "fecha", index: "fecha", width: 120},
        {name: "tipoMovimiento", index: "tipoMovimiento", width: 150},
        {name: "tipoPago", index: "tipoPago", width: 120},
        {name: "referencia", index: "referencia", width: 130},
        {name: "concepto", index: "concepto", width: 130},
        {name: "estado", index: "estado", width: 120},
        {name: "valor", index: "valor", width: 120},
        {name: "saldoCaja", index: "saldoCaja", width: 100},
        {name: "Compra Creditos", width: 120},
        
        
    ],
    sortname: "name",
    sortorder: "asc",
    caption: "Reporte de Actividades",
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.actividadesId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                ActividadesChoferDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.actividadesId = null;
            },0);
            JQGridService.resize('grdActividadesChofer');
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('actividadesChofer.edit',{actividadesId:id})
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
                ActividadesChoferDataService.delete({id: id})
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
        $state.go('actividadesChofer.add');
    }
}