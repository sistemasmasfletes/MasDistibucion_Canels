function PagosIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,PagosDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.pagoId)};
    $scope.grid.edit=function(){grdEdit($scope.pagoId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;
    $scope.fecha = new Date();
    $scope.grid.rezise = function () 
    {
        //ajusta las columnas al tamaño de la tabla
        JQGridService.resize('grdPagos');
    };
    
    $scope.init = function() 
    {
        $scope.loading = true;
    };

    $scope.grid.config = JQGridService.config({
        url: PATH.pagos + '/getPagos',
        colNames: ["No.","Fecha", "Tipo de Movimiento", "Tipo de Pago", "Referencia", "Concepto", "Estatus","Valor"],
        colModel: [
            {name: "id", index: "id", fixed:true,width: 100, align: "center"},
            {name: "fecha", index: "fecha", fixed:true,width: 150, align: "center"},
            {name: "tipoConcepto", index: "tipoConcepto",fixed:true,width: 300, align: "center"},
            {name: "tipoDebito", index: "tipoDebito", fixed:true,width: 300, align: "center"},
            {name: "orden", index: "orden", fixed:true,width: 200, align: "center"},
            {name: "descripcion", index: "descripcion", fixed:true,width: 300, align: "center"},
            {name: "estatus", index: "estatus", fixed:true,width: 150, align: "center"},
            {name: "montoCreditos", index: "montoCreditos", fixed:true,width: 200, align: "center"}

        ],
        sortname: "fecha",
        sortorder: "asc",
        caption: "Pagos",
        autowidth: true,
        postData: { filtro : null},
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.pagoId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) 
        {   
            $timeout(function(){
                PagosDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.pagoId = null;
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
           $state.go('pagos.edit',{pagoId:id})
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
                PagosDataService.delete({id: id})
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
        $state.go('pagos.add');
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'fecha',type:'date', label:'Fecha'},
        {name:'tipoConcepto',type:'text',label:'Tipo de Movimiento'},
        {name:'tipoDebito',type:'text',label:'Tipo de Pago'},
        {name:'orden',type:'text',label:'Referencia'},
        {name:'descripcion',type:'text',label:'Concepto'},
        {name:'estatus',type:'text',label:'Estatus'},
        {name:'montoCreditos',type:'text',label:'Valor'}
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
        $("#grdPagos").setGridParam({datatype: 'json', postData: { filtro : buscar}, page:1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdPagos').getGridParam("postData");
        $scope.nombreArchivo = "Pagos " + $scope.fecha.toDateString();
        $scope.columnasCsv = ["Fecha", "Tipo de Movimiento", "Tipo de Pago", "Referencia", "Concepto", "Estatus","Valor"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.pagos + '/fncExportar',
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