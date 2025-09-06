function AprobacionCreditoControladorIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,AprobacionCreditoControladorDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.compraId)};
    $scope.grid.edit=function(){grdEdit($scope.compraId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;

    $scope.grid.config = JQGridService.config({
    url: PATH.aprobacionCreditoControlador + '/getAprobacionCreditoControladorController',
    colNames: ["id", "Fecha", "Referencia", "Banco", "Concepto",  "Creditos","Metodo de Pago",  "Monto"],
    colModel: [
        {name: "id", width: 40, align: "right"},
       
        {name: "fecha", index: "fecha", width: 120},
        {name: "referencia", index: "referencia", width: 120},
        {name: "name", index: "name", width: 120},
        {name: "concepto", index: "concepto", width: 180},
        {name: "creditos", index: "creditos", width: 76},
        {name: "tipoPago", index: "tipoPago", width: 180},
        {name: "montoCompra", index: "montoCompra", width: 120},
       
        
//        {name: "nombreImg", index: "nombreImg", width: 120},
//        {name: "moneda", index: "moneda", width: 60},
//        
//       
//        {name: "cuenta", index: "cuenta", width: 120},
//        {name: "estado", index: "Estado", width: 70},
        
        
    ],
    sortname: "name",
    sortorder: "asc",
    caption: "Aprobacion de Creditos",
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.compraId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                AprobacionCreditoControladorDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.compraId = null;
            },0);
            JQGridService.resize('grdAprobacionCreditoControlador');
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('aprobacionCreditoControlador.edit',{compraId:id})
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
                AprobacionCreditoControladorDataService.delete({id: id})
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
        $state.go('aprobacionCreditoControlador.add');
    }
}