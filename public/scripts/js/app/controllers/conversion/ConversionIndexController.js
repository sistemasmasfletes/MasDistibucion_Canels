function ConversionIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,ConversionDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.converId)};
    $scope.grid.edit=function(){grdEdit($scope.converId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;
    $scope.exportar = null;
    $scope.fecha = new Date();
    
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdConversion');
    };
    
    $scope.init = function() 
    {
        $scope.loading = true;
    };

    $scope.grid.config = JQGridService.config({
    url: PATH.conversion + '/getConversion',
    colNames: ["id", "Moneda","Compra", "Venta","Fecha", "Créditos"],
    colModel: [
        {name: "id", width: 40, align: "right", hidden: true},
        {name: "moneda", index: "moneda", width: 120, align: "center"},
        {name: "compra", index: "compra", width: 120, align: "center"},
        {name: "venta", index: "venta", width: 120, align: "center"},
        {name: "fecha", index: "fecha", width: 120, align: "center"},
        {name: "creditos", index: "creditos", width: 120, align: "center"}
        
        
    ],
    sortname: "moneda",
    sortorder: "asc",
    caption: "Conversión",
    autowidth: true,
    postData: { filtro : null},
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.converId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                ConversionDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.converId = null;
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
           $state.go('conversion.edit',{converId:id})
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
                ConversionDataService.delete({id: id})
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
        $state.go('conversion.add');
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'moneda',type:'text',label:'Moneda'},
        {name:'compra',type:'text',label:'Compra'},
        {name:'venta',type:'text',label:'Venta'},
        {name:'fecha',type:'date',label:'Fecha'},
        {name:'creditos',type:'text',label:'Créditos'}
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

        $("#grdConversion").setGridParam({datatype: 'json', postData: { filtro : buscar}, page : 1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdConversion').getGridParam("postData");
        $scope.nombreArchivo = "conversion " + $scope.fecha.toString();
        $scope.columnasCsv = ["Moneda","Compra", "Venta","Fecha", "Créditos"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.conversion + '/fncExportar',
            dataType: "json"
        });
        
    };
    $scope.init();
}