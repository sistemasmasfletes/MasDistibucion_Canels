function CompraCreditosIndexController($scope, $timeout, $state, $stateParams, PATH, PARTIALPATH, ModalService, JQGridService,  CompraCreditosDataService, $http) {
    var modalPath = PARTIALPATH.modal;
    var modalInfoPath = PARTIALPATH.modalInfo;
    
    $scope.ROL_CONTROLADOR = 7;
    
    $scope.puedeAgregar = true;
    $scope.exportar = null;
    $scope.fecha = new Date();
    
    
    $scope.init = function() 
    {
        $scope.obtieneConfigracion();
        $scope.loading = true;
    };
    
    $scope.grid = {};
    $scope.grid.delete = function () {
        grdDelete($scope.compraId);
    };
    $scope.grid.edit = function () {
        grdEdit($scope.compraId);
    };
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdCompraCreditos');
    };
    $scope.grid.refresh = grdRefresh;
    $scope.grid.add = grdAdd;

    $scope.grid.config = JQGridService.config({
        
        url: PATH.compraCreditos + '/getCompraCreditos',
        colNames: [ 
            "id", 
            "Fecha", 
            "Empresa", 
            "Tipo de Pago", 
            "Monto de Compra", 
            "Moneda", 
            "Banco", 
            "Cuenta", 
            "Créditos", 
            "Referencia",  
            "Estatus",
            "Comentarios"
        ],
        colModel: [
            {name: "id", width: 40, align: "right", hidden: true},
            {name: "fecha", index: "fecha", fixed:true,align: "center"},
            {name: "cliente", index: "cliente", fixed:true,width: 250, align: "center"},
            {name: "tipoPago", index: "tipoPago", align: "center", width: 300, fixed:true },
            {name: "montoCompra", index: "montoCompra", fixed:true,width: 200, align: "center"},
            {name: "moneda", index: "moneda", fixed:true,width: 200, align: "center"},
            {name: "banco", index: "banco", fixed:true,width: 200,align: "center"},
            {name: "cuenta", index: "cuenta", fixed:true,width: 250, align: "center"},
            {name: "creditos", index: "creditos", fixed:true, align: "center"},
            {name: "referencia", index: "referencia", fixed:true,align: "center"},
            {name: "estatus", index: "estatus", fixed:true, align: "center"},
            {name: "comentario", index: "comentario", fixed:true, width: 300,align: "center"}
        ],
        sortname: "fecha",
        sortorder: "desc",
        caption: "Compra de Créditos",
        autowidth: true,
        postData: { filtro : null},
        onSelectRow: function (id) {
            $timeout(function () {
                $scope.selRow = id;
                $scope.compraId = $scope.grid.apicall('getRowData', id).id;
            }, 0);

        },
        loadComplete: function (data) 
        {
            $timeout(function () {
                CompraCreditosDataService.setData(data[0]);
                $scope.selRow = null;
                $scope.compraId = null;
            }, 0);
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
    }, {id: "id"});


    function grdEdit(id) {
        if (id)
            $state.go('compraCreditos.edit', {compraId: id});
        else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder editar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }

    }



    function grdDelete(id) {
        if (id) {
            var modalOptions = {
                closeButtonText: 'Cancelar',
                actionButtonText: 'Eliminar',
                bodyText: '¿Estás seguro de eliminar el registro?'
            };
            ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
                $scope.loading = true;
                CompraCreditosDataService.delete({id: id})
                        .success(function (data, status, headers, config) {
                            $scope.loading = false;
                            if (data.error) {
                                var modalOptions = {
                                    actionButtonText: 'Aceptar',
                                    bodyText: data.error
                                };
                                ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
                            } else {
                                var modalOptions = {
                                    actionButtonText: 'Aceptar',
                                    bodyText: '¡Registro eliminado con éxito!'
                                };
                                ModalService.showModal({templateUrl: modalInfoPath}, modalOptions).then(function (result) {
                                    $scope.grid.api.refresh();
                                });
                            }
                        })
                        .error(function (data, status, headers, config) {
                            $scope.loading = false;
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: 'Ocurrió un error al eliminar el registro.'
                            };
                            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
                        });
            });
        } else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder eliminar, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
        }
    }

    function grdRefresh() {
        $scope.grid.api.refresh();
    }

    function grdAdd() {
        $state.go('compraCreditos.add');
    }
    
    $scope.obtieneConfigracion = function() {
            
        var data = { };
        var url = PATH.compraCreditos + '/obtenerConfiguracion';

        $scope.loading = true;

        var request = $http({
            method: "post",
            url: url,
            data: data
        });

        request.success( $scope.onObtieneConfiguracion );
        
    };
    
    
    $scope.onObtieneConfiguracion = function( data ) {
        if( data.role === $scope.ROL_CONTROLADOR ) {
            $scope.puedeAgregar = false;            
        }
        $scope.loading = false;
    };
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'fecha',type:'date', label:'Fecha'},
        {name:'cliente',type:'text',label:'Cliente'},
        {name:'tipoPago',type:'text',label:'Tipo de Pago'},
        {name:'montoCompra',type:'text',label:'Monto de Compra'},
        {name:'moneda',type:'text',label:'Moneda'},
        {name:'banco',type:'text',label:'Banco'},
        {name:'cuenta',type:'text',label:'Cuenta'},
        {name:'creditos',type:'text',label:'Créditos'},
        {name:'referencia',type:'text',label:'Referencia'},
        {name:'estatus',type:'text',label:'Estado'}
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

        $("#grdCompraCreditos").setGridParam({datatype: 'json', postData: { filtro : buscar}, page : 1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdCompraCreditos').getGridParam("postData");
        $scope.nombreArchivo = "CompraCreditos " + $scope.fecha.toDateString();
        $scope.columnasCsv = [
            "Fecha", 
            "Cliente", 
            "Tipo de Pago", 
            "Monto de Compra", 
            "Moneda", 
            "Banco", 
            "Cuenta", 
            "Créditos", 
            "Referencia",  
            "Estatus"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.compraCreditos + '/fncExportar',
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