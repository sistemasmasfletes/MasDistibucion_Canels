function PaisesIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,PaisesDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.paisId)};
    $scope.grid.edit=function(){grdEdit($scope.paisId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;
    $scope.fecha = new Date();
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdPaises');
    };

    $scope.grid.config = JQGridService.config({
        url: PATH.paises + '/getPaises',
        colNames: ["id", "Nombre","Estatus"],
        colModel: [
            {name: "id", width: 40, align: "right", hidden: true},
            {name: "nombre", index: "nombre", width: 120},
            {name: "estado", index: "estado", width: 120},
        ],
        sortname: "nombre",
        sortorder: "asc",
        caption: "Países",
        autowidth: true,
        postData: { filtro : null},
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.paisId = $scope.grid.apicall('getRowData', id).id
            },0);
        },
        loadComplete: function (data) {        
            $timeout(function(){
                PaisesDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.paisId = null;
            },0);
            $scope.grid.rezise();
        },
        loadError: function () 
        {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Ocurrio un error al cargar los datos'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions).then(function (result) {
                $scope.grid.rezise();
            });
        }
    },{id:"id"});
    

    function grdEdit(id){
        if(id)
           $state.go('paises.edit',{paisId:id})
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
                PaisesDataService.delete({id: id})
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
        $state.go('paises.add');
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'buscar',type:'text',label:'País'}
    ];

    $scope.filterOpen = false;
    
    $scope.openFilter = function()
    {
        $scope.filterOpen = true;  
    };

    $scope.appFilter = function(filter)
    {
        var buscar = (filter.buscar)? filter.buscar: null;        
        $("#grdPaises").setGridParam({datatype: 'json', postData: { filtro : buscar, page : 1}}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdPaises').getGridParam("postData");
        $scope.nombreArchivo = "Paises "+$scope.fecha.toDateString();
        
        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.paises + '/fncExportar',
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
}