function TransferenciaCreditosEditController($scope, $timeout, $state, $stateParams, PATH,PARTIALPATH, ModalService, CatalogService, TransferenciaCreditosDataService, $http, UsersDataService, transferenciaCreditos) {
   
    var modalPath = PARTIALPATH.modal;
    $scope.transferencia = transferenciaCreditos;
    
    $scope.categorias = [];
    $scope.clientes = [];
    $scope.creditos = 0; 
    $scope.fecha = new Date();
    
    //$scope.transferencia.fecha = new Date().getDate();
    
    $scope.save = save;

    this.init = function() {
        
        $scope.creditosActuales();
        $scope.getFecha();
        $scope.selCliente = {};
        $scope.transferencia.monto = "";
        $scope.transferencia.descripcion = "";
//        $scope.listaCategorias();   
        
    };
    
    $scope.back = function () {
        $state.go('^', $stateParams);
    };
    
    $scope.getFecha = function ()
    {
       $scope.transferencia.fecha = $scope.fecha.toLocaleDateString(); 
    };
    
    function save() 
    {
        if(parseFloat($scope.transferencia.creditos)<parseFloat($scope.transferencia.monto))
        {
            var modalOptions = 
            {
                bodyText: '¡Usted no cuenta con los créditos suficientes!'
            };

            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {});

        }
        else if($scope.transferencia.monto === 0)
        {
            var modalOptions = 
            {
                bodyText: '¡Debe de transferir una cantidad mayor a 0!'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {});
        }
        else
        {
                var modalOptions = {
                    closeButtonText: 'Cancelar',
                    actionButtonText: 'Agregar',
                    bodyText: '¿Estás seguro de agregar el registro?'
                };
                ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) 
                {
                    if ($scope.transferencia) 
                    {
                        $scope.loading = true;
                        TransferenciaCreditosDataService.save($scope.transferencia, {})
                            .success(function (data, status, headers, config) {
                                $scope.loading = false;
                                    if (data.error) {
                                        var modalOptions = {
                                            actionButtonText: 'Aceptar',
                                            bodyText: data.error
                                        };
                                        ModalService.showModal({templateUrl: PARTIALPATH.moalInfo}, modalOptions);
                                    } else {

                                        var modalOptions = {
                                            actionButtonText: 'Aceptar',
                                            bodyText: '¡Registro guardado con éxito! Su nuevo saldo es de: ' + parseFloat($scope.transferencia.creditos-$scope.transferencia.monto).toFixed(2)
                                        };
                                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                            $scope.back();
                                            $scope.grid.refresh();
                                        });
                                    }
                                })
                            .error(function (data, status, headers, config) {
                                $scope.loading = false;
                       });
                    }
                });
            }
        }

//    $scope.listaCategorias = function() 
//    {
//        
//        var data = {
//            metodo: 'categorias'
//        };
//        
//        $.ajax({
//            type: 'POST',
//            data: data,
//            url: PATH.transferenciaCreditos + '/categorias',
//            dataType: "json",
//            success: $scope.onListaCategorias
//        });
//    };
//    
//    $scope.onListaCategorias = function(data) 
//    {
//        var list = [];
//        if(data.error)
//        {
//            $scope.categorias = list;
//            $scope.transferencia.categoria = ""; 
//        }
//        else
//        {
//            list = data === null ? [] : (data.categorias instanceof Array ? data.categorias : [data.categorias]);
//            $scope.categorias = list;
//            $scope.transferencia.categoria = $scope.getSelectedCategoria(); 
//            $scope.listaClientes();
//        }
//    };
//    
//    $scope.getSelectedCategoria = function() 
//    {
//        var selected = "";
//        for( var i = 0; i < $scope.categorias.length; i ++ ) 
//        {
//            
//            if( $scope.transferencia.categoriaId === $scope.categorias[i].id ) {
//                return $scope.categorias[i];
//            }
//        }
//        return selected;
//    };

//    $scope.listaClientes = function() {
//        
//        if( $scope.transferencia.categoria !== "" ) 
//        {
//            var data = {
//                idCategoria: $scope.transferencia.categoria.id
//            };
//            var url = PATH.transferenciaCreditos + '/usuarios';
//            $scope.loading = true;
//            var request = $http({
//                method: "post",
//                url: url,
//                data: data
//            });
//            request.success( $scope.onListaClientes );
//        } 
//        else 
//        {
//            $scope.clientes = [];
//            $scope.transferencia.cliente = "";
//        }
//       
//    };
//    
//    $scope.onListaClientes = function( data ) 
//    {
//        var list = [];
//        if(data.error)
//        {
//           $scope.clientes = list;
//           $scope.transferencia.cliente = "";
//        }
//        else
//        {
//            list = data === null ? [] : (data.clientes instanceof Array ? data.clientes : [data.clientes]);
//            $scope.clientes = list;
//        }
//        $scope.loading = false;
//        
//    };
    
    $scope.creditosActuales = function() 
    {
        var data = {
            metodo: 'creditos'
        };
        
        $.ajax({
            type: 'POST',
            data: data,
            url: PATH.transferenciaCreditos + '/creditos',
            dataType: "json",
            success: $scope.obtenerCreditos
        });
    };
    
    $scope.obtenerCreditos = function(data) 
    {
        var list = [];
        if(data.error)
        {
            $scope.transferencia.creditos = "0.00";
        }
        else
        {
            list = data === null ? [] : (data.clientes instanceof Array ? data.clientes : [data.clientes]);
            $scope.creditos = list;
            $scope.transferencia.creditos = $scope.creditos[0].creditos.toFixed(2);
        }
    };
    
    
    $scope.validarCreditos = function() 
    {
        if(parseFloat($scope.transferencia.creditos)<parseFloat($scope.transferencia.monto))
        {
            var modalOptions = 
            {
                bodyText: '¡Usted no cuenta con los créditos suficientes!'
            };

            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {});
        
        }
       
    };
    
    $scope.getFormFieldCssClass = function(ngModelController) {
        if(ngModelController.$pristine)
            
        return "";
            
        return ngModelController.$valid ? "has-success" : "has-error";
    };
    
  
    var selectOptions = {
        displayText: "Seleccione...",
        emptyListText:"No hay elementos a desplegar",
        emptySearchResultText:"No se encontraron resultados para '$0'",
        searchDelay:"500"
    };
    
    $scope.getClientes=function(value)
    {
        if(value.length===0) return;
        return TransferenciaCreditosDataService.getClienteByName({param1:value})
        .then(function(response){
            return response.data[0];
        });
    };
    $scope.selCliente={id:$scope.transferencia.cliente , commercialName:$scope.transferencia.cliente}
    
    $scope.selClienteOptions = angular.extend({},selectOptions
        ,{onSelect:function($item){
           $scope.transferencia.cliente = ($item)? $item.id : null;
        }});    
      
    this.init();

}