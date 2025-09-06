function TransferenciaCreditosIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,TransferenciaCreditosDataService){
    var modalPath = PARTIALPATH.modal;
    var modalInfoPath = PARTIALPATH.modalInfo;

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.transferenciaId)};
    $scope.grid.edit=function(){grdEdit($scope.transferenciaId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;
    $scope.fecha = new Date();
    
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdTransferenciaCreditos');
    };
    $scope.init = function() 
    {
        $scope.loading = true;
    };

    $scope.grid.config = JQGridService.config({
    url: PATH.transferenciaCreditos + '/getTransferenciaCreditos',
    colNames: ["id", "Empresa", "Fecha", "Saldo","Créditos", "Categoría", "Comentarios"],
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
        {name: "client", index: "client", fixed:true, width: 300, align: "center"},
        {name: "fecha", index: "fecha", fixed:true, align: "center"},
        {name: "creditos", index: "creditos", fixed:true, align: "center"},
        {name: "monto", index: "monto", fixed:true,align: "center"},
        {name: "category", index: "category", fixed:true, width: 250, align: "center"},
        {name: "descripcion", index: "descripcion", fixed:true, width: 350, align: "center"}
    ],
    sortname: "fecha",
    sortorder: "asc",
    caption: "Transferencia de Créditos",
    autowidth: true,
        postData: { filtro : null},
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.transferenciaId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                TransferenciaCreditosDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.transferenciaId = null;
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
           $state.go('transferenciaCreditos.edit',{transferenciaId:id});
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
                TransferenciaCreditosDataService.delete({id: id})
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
        $state.go('transferenciaCreditos.add');
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'cliente',type:'text',label:'Cliente'},
        {name:'fecha',type:'date', label:'Fecha'},
        {name:'creditos',type:'text',label:'Créditos'},
        {name:'monto',type:'text',label:'Monto de Transferencia'},
        {name:'categoria',type:'text',label:'Categoria'},
        {name:'comentarios',type:'text',label:'Comentarios'}
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

        $("#grdTransferenciaCreditos").setGridParam({datatype: 'json', postData: { filtro : buscar}, page : 1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdTransferenciaCreditos').getGridParam("postData");
        $scope.nombreArchivo = "Transferencia de Créditos " + $scope.fecha.toDateString();
        $scope.columnasCsv = ["Cliente", "Fecha", "Créditos","Monto de Transferencia", "Categoría", "Comentarios"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.transferenciaCreditos + '/fncExportar',
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