function BancosIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,BancosDataService){
    var modalPath = PARTIALPATH.modal;
    var modalInfoPath = PARTIALPATH.modalInfo;

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.bancoId)};
    $scope.grid.edit=function(){grdEdit($scope.bancoId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;
    $scope.grid.filter=function(){grdFilter();};
    $scope.exportar = null;
    $scope.fecha = new Date();
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdBancos');
    };

    $scope.init = function() 
    {
        $scope.loading = true;
    };
    $scope.grid.config = JQGridService.config({
        url: PATH.bancos + 'getBancos',
        colNames: ["id", "Nombre","Estatus"],
        colModel: [
            {name: "id", width: 40, align: "right", hidden: true},
            {name: "name", index: "name", width: 120},
            {name: "estado", index: "estado", width: 120}
        ],
        sortname: "name",
        sortorder: "asc",
        caption: "Bancos",
        autowidth: true,
        postData: { filtro : null},

        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.bancoId = $scope.grid.apicall('getRowData', id).id
            },0);
        },
        loadComplete: function (data) {        
            $timeout(function(){
                BancosDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.bancoId = null;
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
           $state.go('bancos.edit',{bancoId:id})
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
                BancosDataService.delete({id: id})
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
        $state.go('bancos.add');
    }


    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'banco',type:'text',label:'Banco'},
        {name:'estado',type:'text',label:'Estado'}
    ];

    $scope.filterOpen = false;
    
    $scope.openFilter = function()
    {
        $scope.filterOpen = true;  
    };

    $scope.appFilter = function(filter)
    {
        var buscar={};
        var count = 0;
        
        //asigna los valores al arreglo de los parametros que se van buscar
        for(var i=0;i<$scope.customFilter.length;i++)
        {
            buscar[$scope.customFilter[i].name] = ($scope.customFilter[i].value) ? $scope.customFilter[i].value : null;
        }
        
        //cuenta los campos que son nullos
        for(var i=0;i<$scope.customFilter.length;i++)
        {
            if(buscar[$scope.customFilter[i].name] === null)
            {
                count++;
            }
        }
        
        //si todos los campos son nullos el arreglo se vacia
        if(count === $scope.customFilter.length)
        {
            buscar = null;
        }      
        $("#grdBancos").setGridParam({datatype: 'json', postData: { filtro : buscar} , page : 1}).trigger('reloadGrid');
       
    };
    
    $scope.nombreArchivo = "";
    $scope.fncExportar = function() 
    {
        var postData = $('#grdBancos').getGridParam("postData");
        $scope.nombreArchivo = "bancos "+$scope.fecha.toDateString();;
        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.bancos + 'fncExportar',
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