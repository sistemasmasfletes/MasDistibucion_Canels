function AdministracionLogClienteIndexController($scope, $timeout, $state, $stateParams, PATH, PARTIALPATH, ModalService, JQGridService, AdministracionLogClienteDataService) {
    var modalInfoPath = PARTIALPATH.modalInfo;
    $scope.grid = {};
    $scope.fecha = new Date();
    
    $scope.init = function() 
    {
        $scope.loading = true;
    };
    
    $scope.grid.rezise = function () 
    {
        JQGridService.resize('grdAdministracionLogCliente');
    };
    
    $scope.grid.refresh = grdRefresh;

    $scope.grid.config = JQGridService.config({
        url: PATH.administracionLogCliente + '/getAdministracionLogCliente',
        colNames: ["id", "Fecha", "Referencia", "Banco", "Concepto", "Créditos", "Tipo de Pago", "Monto", "Saldo" ],
        colModel: [
            {name: "id", width: 40, align: "right", hidden: true},
            {name: "fecha", index: "fecha", fixed:true,width: 200, align: "center"},
            {name: "referencia", index: "referencia", fixed:true,width: 200, align: "center"},
            {name: "banco", index: "banco", fixed:true,width: 200, align: "center"},
            {name: "concepto", index: "concepto", fixed:true,width: 250, align: "center"},
            {name: "creditos", index: "creditos", fixed:true,width: 200, align: "center"},
            {name: "tipoPago", index: "tipoPago", fixed:true,width: 200, align: "center"},
            {name: "monto", index: "monto", fixed:true,width: 200, align: "center"},
            {name: "saldo", index: "saldo", fixed:true,width: 200, align: "center"}
        ],
        sortname: "fecha",
        sortorder: "asc",
        caption: "Administración de Depositos",
        autowidth: true,
        postData: { filtro : null},
        onSelectRow: function (id) {
            $timeout(function () {
                $scope.selRow = id;
                $scope.adminCrediId = $scope.grid.apicall('getRowData', id).id
            }, 0);

        },
        loadComplete: function (data) {
            
            $timeout(function () {
                AdministracionLogClienteDataService.setData(data[0]);
                $scope.selRow = null;
                $scope.adminCrediId = null;
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



    function grdRefresh() {
        $scope.grid.api.refresh();
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };

    $scope.customFilter = [ 

        {name:'fecha',type:'date', label:'Fecha'},
        {name:'referencia',type:'text',label:'Referencia'},
        {name:'banco',type:'text',label:'Banco'},
        {name:'concepto',type:'text',label:'Concepto'},
        {name:'creditos',type:'text',label:'Créditos'},
        {name:'tipoPago',type:'text',label:'TipoPago'},
        {name:'monto',type:'text',label:'Monto'},
        {name:'saldo',type:'text',label:'Saldo'}
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

        $("#grdAdministracionLogCliente").setGridParam({datatype: 'json', postData: { filtro : buscar}, page : 1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdAdministracionLogCliente').getGridParam("postData");
        $scope.nombreArchivo = "Administración de Depositos " + $scope.fecha.toDateString();
        $scope.columnasCsv = ["Fecha", "Referencia", "Banco", "Concepto", "Créditos", "Tipo de Pago", "Monto", "Saldo"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.administracionLogCliente + '/fncExportar',
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