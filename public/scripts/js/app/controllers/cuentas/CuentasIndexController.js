function CuentasIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,CuentasDataService){
    var modalPath = PARTIALPATH.modal
    var modalInfoPath = PARTIALPATH.modalInfo

    $scope.grid = {};
    $scope.grid.delete=function(){grdDelete($scope.cuentId)};
    $scope.grid.edit=function(){grdEdit($scope.cuentId)};
    $scope.grid.refresh=grdRefresh;
    $scope.grid.add=grdAdd;
    $scope.exportar = null;
    $scope.fecha = new Date();
    $scope.grid.rezise = function () 
    {
        //ajusta las columnas al tamaño de la tabla
        JQGridService.resize('grdCuentas');
    };
    $scope.init = function() 
    {
        $scope.loading = true;
    };

    $scope.grid.config = JQGridService.config({
        url: PATH.cuentas + '/getCuentas',
        colNames: ["id","Número de Cuenta","Cuenta","CLABE Interbancaria","Tipo de Moneda","Banco","Tipo de Operador", "País", "Estado","Tipo de Pago"],
        colModel: [
            {name: "id", width: 40, align: "right", hidden: true},
            {name: "numeroCuenta", index: "numeroCuenta", fixed:true,width: 200, align: "center"},
            {name: "cuenta", index: "cuenta", fixed:true,width: 250, align: "center"},
            {name: "clabeInterbancaria", index: "clabeInterbancaria", fixed:true,width: 250, align: "center"},
            {name: "moneda", index: "moneda", fixed:true,width: 250, align: "center"},
            {name: "banco", index: "banco",fixed:true,width: 200, align: "center"},
            {name: "tipoOperador", index: "tipoOperador", fixed:true,width: 200, align: "center"},
            {name: "pais", index: "pais", fixed:true,width: 200, align: "center"},
            {name: "estado", index: "estado", fixed:true,width: 200, align: "center"},
            {name: "tipoPago", index: "tipoPago", fixed:true,width: 200, align: "center"}
        ],
        sortname: "cuenta",
        sortorder: "asc",
        caption: "Cuentas",
        autowidth: true,
        postData: { filtro : null},
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.cuentId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            $timeout(function(){
                CuentasDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.cuentId = null;
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
           $state.go('cuentas.edit',{cuentId:id})
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
                CuentasDataService.delete({id: id})
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
        $state.go('cuentas.add');
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'numeroCuenta',type:'text', label:'Número de Cuenta'},
        {name:'cuenta',type:'text',label:'Cuenta'},
        {name:'clabeInterbancaria',type:'text',label:'Clabe Interbancaria'},
        {name:'moneda',type:'text',label:'Moneda'},
        {name:'banco',type:'text',label:'Banco'},
        {name:'tipoOperador',type:'text',label:'Tipo de Operador'},
        {name:'pais',type:'text',label:'Pais'},
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

        $("#grdCuentas").setGridParam({datatype: 'json', postData: { filtro : buscar}, page : 1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdCuentas').getGridParam("postData");
        $scope.nombreArchivo = "cuentas " + $scope.fecha.toDateString();
        $scope.columnasCsv = ["Número de Cuenta","Cuenta","CLABE Interbancaria","Tipo de Moneda","Banco","Tipo de Operador", "País", "Estado","Tipo de Pago"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.cuentas + '/fncExportar',
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