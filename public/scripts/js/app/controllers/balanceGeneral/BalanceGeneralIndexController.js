function BalanceGeneralIndexController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,JQGridService,BalanceGeneralDataService){

    var modalInfoPath = PARTIALPATH.modalInfo;
    $scope.grid = {};
    $scope.grid.refresh=grdRefresh;
    $scope.fecha = new Date();
    $scope.grid.rezise = function () 
    {
        //ajusta las columnas al tamaño de la tabla
        JQGridService.resize('grdBalanceGeneral');
    };
    
    $scope.init = function() 
    {
        $scope.loading = true;
    };
    $scope.grid.config = JQGridService.config({
    url: PATH.balanceGeneral + '/getBalanceGeneral',
    colNames: ["id", "Numero de Orden de Compra","Fecha","Tipo de Movimiento", "Referencia","Concepto", "Estatus", /*"Creditos", */ "Ingreso", "Egreso", "Congelado", "Saldo", "Créditos"],
    colModel: [
        {name: "id", hidden: true},
        {name: "orden", index: "orden", align: "center", fixed:true, width:210},
        {name: "fecha", index: "fecha", align: "center", fixed:true},
        {name: "tipoConcepto", index: "tipoConcepto", fixed:true, width: 250, align: "center"},
        {name: "referencia", index: "referencia", align: "center",fixed:true},
        {name: "concepto", index: "concepto", fixed:true, width: 350, align: "center"},
        {name: "estatus", index: "estatus", align: "center", fixed:true},
        /*{name: "monto", index: "monto", align: "center", fixed:true},*/
        {name: "ingresos", index: "ingresos", align: "center", fixed:true},
        {name: "egresos", index: "egresos", align: "center", fixed:true},
        {name: "congelado", index: "congelado", align: "center", fixed:true},
        {name: "conversion", index: "conversion", align: "center", fixed:true},
        {name: "balance", index: "balance", align: "center", fixed:true}
    ],
    sortname: "fecha",
    sortorder: "desc",
    caption: "Balance General",
    autowidth: true,
    postData: { filtro : null},
        onSelectRow: function(id){
            $timeout(function(){
                $scope.selRow = id;
                $scope.balanceId = $scope.grid.apicall('getRowData', id).id
            },0);
            
        },
        loadComplete: function (data) {        
            for (var i = 0; i < data[0].length; i++) {
            	//var status = $("#" + data.rows[i]).find("td").eq(2).html();
                //if (status == "Complete") {
                $("#" + data[0][i].id).find("td").eq(11).css("color", "green");
                //$("#" + data[0][i].id).find("td").eq(11).css("background-color", "green");
                //}
            }
            $timeout(function(){
                BalanceGeneralDataService.setData(data[0]); 
                $scope.selRow =null;
                $scope.balanceId = null;
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

    function grdRefresh(){
        $scope.grid.api.refresh();
    }
    
    $scope.toggleFilter = function(params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    };
    
    $scope.customFilter = [ 
        {name:'fecha',type:'date', label:'Fecha'},
        {name:'tipoConcepto',type:'text',label:'Tipo de Movimiento'},
        {name:'referencia',type:'text',label:'Referencia'},
        {name:'concepto',type:'text',label:'Concepto'},
        {name:'estatus',type:'text',label:'Estatus'},
        {name:'creditos',type:'text',label:'Créditos'},
        {name:'ingresos',type:'text',label:'Ingreso'},
        {name:'egresos',type:'text',label:'Egreso'},
        {name:'balance',type:'text',label:'Balance'}        
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
        $("#grdBalanceGeneral").setGridParam({datatype: 'json', postData: { filtro : buscar}, page:1}).trigger('reloadGrid');
    };
    
    $scope.fncExportar = function() 
    {
        var postData = $('#grdBalanceGeneral').getGridParam("postData");
        $scope.nombreArchivo = "Balance General " + $scope.fecha.toDateString();
        $scope.columnasCsv = ["Fecha","Tipo de Movimiento", "Referencia","Concepto", "Estatus", "Creditos", "Ingreso", "Egreso", "Balance"];

        var data = {
            metodo: 'exportar',
            sortDir: postData["sortDir"],
            sortField: postData["sortField"],
            filtro: postData["filtro"]
        };
        return $.ajax({
            type: 'POST',
            data: data,
            url: PATH.balanceGeneral + '/fncExportar',
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