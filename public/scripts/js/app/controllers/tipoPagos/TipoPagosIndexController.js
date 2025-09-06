function TipoPagosIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,TipoPagosDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.tipoId)};
    $scope.grid.edit=function(){grdEdit($scope.tipoId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;
    $scope.exportar = null;
    $scope.fecha = new Date();
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdTipoPagos');
    };
    $scope.init = function() 
    {
        $scope.loading = true;
    };

    $scope.grid.config = JQGridService.config(
    {
        url: PATH.tipoPagos + 'getTipoPagos',
        colNames: ["id", "Tipos de Pagos"],
        colModel: [
            {name: "id", width: 40, align: "right", hidden: true},
            {name: "tipoPago", index: "tipoPago", width: 120}
        ],
        sortname: "tipoPago",
        sortorder: "asc",
        caption: "Tipos de Pagos",
        autowidth: true,
        postData: { filtro : null},
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.tipoId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                TipoPagosDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.tipoId = null;
            },0);
            $scope.loading = false;
            $scope.grid.rezise();
        },
        loadError: function () 
        {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Ocurrio un error al cargar los datos'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions).then(function (result) {
                $scope.loading = false;
                $scope.grid.rezise();
            });
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('tipoPagos.edit',{tipoId:id})
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
                TipoPagosDataService.delete({id: id})
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
        $state.go('tipoPagos.add');
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'buscar',type:'text',label:'Tipo de Pago'}
    ];

    $scope.filterOpen = false;
    
    $scope.openFilter = function()
    {
        $scope.filterOpen = true;  
    };

    $scope.appFilter = function(filter)
    {
        var buscar = (filter.buscar)? filter.buscar: null;   
        $("#grdTipoPagos").setGridParam({datatype: 'json', postData: { filtro : buscar}, page : 1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdTipoPagos').getGridParam("postData");
        $scope.nombreArchivo = "tipoPago "+$scope.fecha.toDateString();
        
        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.tipoPagos + 'fncExportar',
            dataType: "json",
            error: function () 
            {
                var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: '¡Ocurrio un error al exportar los registros!'
                };
                ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
            }
        });
        
    };
 $scope.init();
}

