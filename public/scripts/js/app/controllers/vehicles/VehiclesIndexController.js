function VehiclesIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,VehiclesDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.vehicleId)};
    $scope.grid.edit=function(){grdEdit($scope.vehicleId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;

    $scope.grid.config = JQGridService.config({
    url: PATH.vehicles + 'getVehicles',
    colNames: ["id", "Nombre", "Tipo", "type", "status", "Volúmen", "Número Económico", "Capacidad", "Marca", "Placas", "Color", "GPS", "Modelo", "Ancho", "Altura", "Profunidad"],
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
        {name: "name", index: "name", width: 120},
        {name: "tipo", index: "tipo", width: 80},
        {name: "type", index: "type", width: 80, hidden: true},
        {name: "status", index: "status", width: 80, hidden: true},
        {name: "volume", index: "volume", width: 50, formatter: 'number', formatoptions: {decimalPlaces: 0, suffix: "ft³"}},
        {name: "economic_number", index: "economic_number", width: 30, hidden: true},
        {name: "capacity", index: "capacity", width: 80},
        {name: "trade_mark", index: "trade_mark", width: 80},
        {name: "plate", index: "plate", width: 80, hidden: true},
        {name: "color", index: "color", width: 80, hidden: true},
        {name: "gps", index: "gps", width: 80, hidden: true},
        {name: "model", index: "model", width: 50},
        {name: "width", index: "width", width: 50},
        {name: "height", index: "height", width: 50},
        {name: "deep", index: "deep", width: 50}
    ],
    sortname: "name",
    sortorder: "asc",
    caption: "Vehículos",
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.vehicleId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                VehiclesDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.vehicleId = null;
            },0);
            JQGridService.resize('grdVehicles');
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('vehicles.edit',{vehicleId:id})
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
                bodyText: '¿Estás seguro de eliminar este vehículo?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                $scope.loading = true;
                VehiclesDataService.delete({id: id})
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
        $state.go('vehicles.add');
    }
}