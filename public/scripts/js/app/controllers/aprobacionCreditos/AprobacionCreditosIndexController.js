function AprobacionCreditosIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,CompraCreditosDataService){
    var modalPath = PARTIALPATH.modal;
    var modalInfoPath = PARTIALPATH.modalInfo;

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.compraId);};
    $scope.grid.edit=function(){grdEdit($scope.compraId);};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;


    $scope.grid.config = JQGridService.config({
    url: PATH.compraCreditos + '/getCompraCreditos',
    colNames: ["id", "Fecha", "Cliente","Tipo de Pago", "Monto de Compra",  "Moneda", "Banco", "Cuenta", "Creditos",  "Referencia", "Comprobante", "Estado"],
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
        {name: "fecha", index: "fecha", width: 100,},
        {name: "usuario", index: "usuario", width: 100},
        {name: "tipoPago", index: "tipoPago", width: 100},
        {name: "montoCompra", index: "montoCompra", width: 120},
        {name: "moneda", index: "moneda", width: 100},
        {name: "name", index: "name", width: 100},
        {name: "cuenta", index: "cuenta", width: 100},
        {name: "creditos", index: "creditos", width: 100},
        {name: "referencia", index: "referencia", width: 110},
        {name: "nombreImg", index: "nombreImg", width: 100},
        {name: "estado", index: "Estado", width: 100}
        
        
    ],
    sortname: "name",
    sortorder: "asc",
    cout:10,
    page:1,
    caption: "Aprobacion de Creditos",
        onSelectRow: function(id){ 
            $timeout(function(){
                $scope.selRow = id;
                $scope.compraId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                CompraCreditosDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.compraId = null;
            },0);
            JQGridService.resize('grdAprobacionCreditos');
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('aprobacionCreditos.edit',{compraId:id})
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
                CompraCreditosDataService.delete({id: id})
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
        $state.go('aprobacionCreditos.add');
    }
}