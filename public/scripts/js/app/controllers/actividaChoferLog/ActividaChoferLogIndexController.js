function ActividaChoferLogIndexController($scope, $timeout, $state, $stateParams, PATH, PARTIALPATH, ModalService, JQGridService, ActividaChoferLogDataService) {
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo
    $scope.grid = {};

    $scope.grid.edit = function () {
        grdEdit($scope.actividaChoferLogId);
    };
    $scope.grid.refresh = grdRefresh;
    $scope.grid.add = grdAdd;
     $scope.fecha = new Date();
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdActividaChoferLog');
    };
    $scope.init = function() 
    {
        $scope.loading = true;
    };

    $scope.grid.config = JQGridService.config({
        url: PATH.actividaChoferLog + '/getActividaChoferLog',
        colNames: ["id", "Fecha", "Tipo de Debito", "Tipo de Actividad", "Estatus de Pago", "Monto", "Punto de Actividad", "Orden", "Realizar"],
        colModel: [
            {name: "id", width: 40, align: "right", hidden: true},
            {name: "fecha", index: "fecha", width: 120, align: "center"},
            {name: "tipoDebito", index: "tipoDebito", width: 120},
            {name: "actividadTipo", index: "actividadTipo", width: 120, align: "center"},
            {name: "estatus", index: "estatus", width: 120, align: "center"},
            {name: "montoCreditos", index: "montoCreditos", width: 120, align: "center"},
            {name: "puntoActividad", index: "puntoActividad", width: 120, align: "center"},
            {name: "compraId", index: "compraId", width: 120, align: "center"},
            {name: "estatusActividad", index: "estatusActividad", formatter: "checkbox", formatoptions: { disabled: 'value'}, editoptions: {value: "1:0"}, width: 50, align: "center", options:{ on: 'P', off: '' }}
        ],
        sortname: "id",
        sortorder: "asc",
        caption: "Activides del Chofer",
        autowidth: true,
        onSelectRow: function (id) {
            $timeout(function () {
                $scope.selRow = id;
                $scope.actividaChoferLogId = $scope.grid.apicall('getRowData', id).id
            }, 0);

        },
        beforeSelectRow: function(rowid, e) 
        { 

            var $target = $(e.target), 
                $td = $target.closest("td"),
                iCol = $.jgrid.getCellIndex($td[0]),
                colModel = $(this).jqGrid("getGridParam", "colModel");
                if (iCol >= 0 && $target.is(":checkbox")) {
                    
                    var check = ($target.is(":checked")? "checked" : "unchecked");
                    if(check === "checked" )
                    {
                        grdEdit(rowid);
                    }
                }
            return true;
         },
        loadComplete: function (data) {
            $timeout(function () {
                ActividaChoferLogDataService.setData(data[0]);
                $scope.selRow = null;
                $scope.actividaChoferLogId = null;
                
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
    
    
    function grdEdit(id) 
    {
        if (id)
        {
                $scope.loading = true;
                ActividaChoferLogDataService.save({id: id}).success(function (data) 
                {
                    $scope.loading = false;
                    if (data.error) {
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: modalInfoPath}, modalOptions).then(function (result) {
                            $scope.grid.api.refresh();
                        });
                    } else {
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Registro realizado  con éxito!'
                        };
                        ModalService.showModal({templateUrl: modalInfoPath}, modalOptions).then(function (result) {
                            $scope.grid.api.refresh();
                        });
                    }
                })
                        .error(function (data) {
                            $scope.loading = false;
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: 'Ocurrió un error al realizar el registro.'
                            };
                            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
                        });
            
        }
//            $state.go('actividaChoferLog.edit', {actividaChoferLogId: id})
        else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para poder realizar una actividad, es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions);
            
        }

    }



    function grdRefresh() {
        $scope.grid.api.refresh();
    }

    function grdAdd() {
        $state.go('actividaChoferLog.add');
    }
    
    //funciones para hacer la busqueda
     $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    //crea la ventana modal de los campos de texto para la busqueda
    $scope.customFilter = [ 
        {name:'fecha',type:'date', label:'Fecha'},
        {name:'tipoDebito',type:'text',label:'Tipo de Debito'},
        {name:'actividadTipo',type:'text',label:'Tipo de Actividad'},
        {name:'estatus',type:'text',label:'Estatus'},
        {name:'montoCreditos',type:'text',label:'Monto'},
        {name:'puntoActividad',type:'text',label:'Punto de Actividad'},
        {name:'compraId',type:'text',label:'Compra'},
        {name:'estatusActividad',type:'text', label:'Realizado S/N'}
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

        $("#grdActividaChoferLog").setGridParam({datatype: 'json', postData: { filtro : buscar}, page : 1}).trigger('reloadGrid');
    };
    
    //funciones para exportar a csv 
    $scope.fncExportar = function() 
    {
        var postData = $('#grdActividaChoferLog').getGridParam("postData");
        $scope.nombreArchivo = "Actividades del Chofer " + $scope.fecha.toDateString();
        $scope.columnasCsv = ["Fecha", "Tipo de Debito", "Tipo de Actividad", "Estatus de Pago", "Monto", "Punto de Actividad", "Orden", "Estatus Realizado"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.actividaChoferLog + '/fncExportar',
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