function RoutesIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService, JQGridService,CatalogService,RoutesDataService,routes){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {selRow:{index:null,id:null,name:null}};

   
    function _init(){        
        JQGridService.resize('grdRoutes');
    }
    
    $scope.status=CatalogService.getPointStatus()[0].status;
    
    $scope.grid.home= grdHome;
    $scope.grid.delete = function(){return grdDelete($scope.grid.selRow)};
    $scope.grid.refresh = grdRefresh;
    $scope.grid.edit=function(){return grdEdit($scope.grid.selRow)};
    $scope.grid.add=grdAdd;

    $scope.grid.config = JQGridService.config({
    datatype:'json',
    url: PATH.routes + 'getRoutes',
    colNames: ["id", "Clave", "Nombre","Franquisiatario", "Estatus", "Capacidad", "Factor", "Estado"],
    colModel: [
        {name: "id", width: 0, align: "right", hidden: true},
        {name: "code", index: "code", width: 80},
        {name: "name", index: "name", width: 150},
        {name: "franchisee", index: "franchisee", width: 80},
        {name: "estatus", index: "estatus", width: 50},
        {name: "capacity", index: "capacity", width: 50},
        {name: "factor", index: "factor", width: 50, formatter: 'number', formatoptions: {decimalPlaces: 0, separator:",", suffix: ""}},
        {name: "estado", index: "estado", width: 80}                
    ],
    sortname: "code",
    sortorder: "asc",
    caption: "Rutas",
    onSelectRow: function(id){
        $scope.$apply(function(){            
            $scope.grid.selRow.index = id;
            var row = $scope.grid.apicall('getRowData', id);
            $scope.grid.selRow.id = row.id;
            $scope.grid.selRow.name = row.name;            
        });
    },
    beforeRequest: function(){
        $timeout(function(){$scope.loading = true;},0);
    },
    loadComplete: function (data) {
        $timeout(function(){
            $scope.grid.selRow.index =null;
            $scope.grid.selRow.id = null;
            $scope.grid.selRow.name = null;
            $scope.loading = false;
            RoutesDataService.setData(data[0]);
        },0);
        _init();
    }
    },{id:"id"});


    /*Public & private functions*/
    function grdRefresh(){
        $scope.grid.api.refresh();
    }

    function grdDelete(row){
        if(row.id){
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar esta Ruta? ' + row.name
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                $scope.loading = true;
                config.alertOnSuccess=true;
                RoutesDataService.delete({id: row.id},config)
                .success(function(data, status, headers, config) {
                    $scope.loading = false;                    
                    if(!data.error) $scope.grid.api.refresh();
                })
                .error(function(data, status, headers, config) {
                    $scope.loading = false;
                })
            });
        }else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder eliminar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }

    function grdEdit(row){
        if(row.id){
            $state.go('routes.edit',{routeId:row.id})
        }else{
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder editar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        
        }
    }

    function grdHome(){
        $state.go('routes',$stateParams);
    }

    function grdAdd(){
        $state.go('routes.add');
    }
}
