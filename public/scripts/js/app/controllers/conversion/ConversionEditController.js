function ConversionEditController($scope,$timeout,$state,$stateParams,PATH,PARTIALPATH,ModalService,CatalogService,ConversionDataService,conversion, $http){
    $scope.conver=conversion;
    $scope.conversionStatus=CatalogService.getConversionStatus(); //definir en CatalogService
    $scope.conversionTypes=CatalogService.getConversionType();
    $scope.save=save;
    
    $scope.back=function(){
        $state.go('^',$stateParams);
    };
    
    $scope.monedas = [];

    $scope.datePicker = {
        format: 'dd-MM-yyyy',
        toggleMin: function () {
            $scope.datePicker.minDate = null
        },
        open: function ($event) {
            $event.preventDefault();
            $event.stopPropagation();
            $scope.datePicker.opened = true;
        },
        dateOptions: {
            formatYear: 'yy',
            startingDay: 1
        }
    };
   
    $scope.datePicker.toggleMin();
    
    this.init = function () {
       $scope.conver.creditos = parseFloat($scope.conver.creditos);
       $scope.conver.compra = parseFloat($scope.conver.compra);
       $scope.conver.venta = parseFloat($scope.conver.venta);
       $scope.listaMonedas();
    };

    function save(){
        
        if($scope.conver){
            $scope.loading=true;
            ConversionDataService.save($scope.conver, {})
                .success(function(data, status, headers, config){
                    $scope.loading=false;
                    if (data.error) {
                         var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
                    } else {
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Registro guardado con éxito!'
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                $scope.back();
                                $scope.grid.refresh();
                            });
                        }
                })
                .error(function(data, status, headers, config){
                    $scope.loading = false;                    
                });
        }
    }
    
    $scope.listaMonedas = function(){
        $scope.loading = true;
        var data = {
            metodo: 'monedas'
        };
        var request = $http({
            method: "post",
            url: PATH.cuentas + '/monedas',
            data: data
        });
        request.success($scope.onListaMonedas);       
    }
    
    $scope.onListaMonedas =  function(data){

        var list = data == null ? [] : (data.monedas instanceof Array ? data.monedas : [data.monedas ]);
      
        $scope.monedas = list;
        
        $scope.conver.moneda = getSelectedMoneda();
        
        if (list.length < 1) {
           alert("SIN NINGÚN RESULTADO EN LA BD");
        }        
        $scope.loading = false;
    }
    
    
    function getSelectedMoneda(  ) {
        var selected = "";
        for( var i = 0; i < $scope.monedas.length; i ++ ) {
            if( $scope.conver.idMoneda == $scope.monedas[i].id ) {
                return $scope.monedas[i];
            }
        }
        return selected;
    }
    
    
    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine) return "";
        return ngModelController.$valid ? "has-success" : "has-error";
    };
    
    this.init();
}