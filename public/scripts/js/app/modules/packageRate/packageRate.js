(function(){
angular.module('masDistribucion.packageRate',[
    "ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',
        function ClientPackageConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
            $stateProvider
            .state('packageRate', {
                url: "/packageRate",
                views:{
                    'main':{
                        templateUrl: PARTIALPATH.packageRate + 'index.html',
                        controller: 'IndexController'
                    }
                }
            })
            .state('packageRate.listRates',{
                url:"/rateFor{elementId:[0-9]{1,6}}/t{elementType:[1]{1}}",
                views:{
                    'rateList':{
                        templateUrl: PARTIALPATH.packageRate + 'listRate.html',
                        controller: 'ListRateController'
                    }
                }
            })
            .state('packageRate.edit',{
                url:"/edit{elementId:[0-9]{1,6}}/t{elementType:[1]{1}}/rate{rateId:[0-9]{1,6}}",
                views:{
                    'edit':{
                        templateUrl: PARTIALPATH.packageRate + 'edit.html',
                        controller: 'EditController',
                        resolve: {
                            rate: ['$stateParams','UtilsService','PackageRateDataService',function($stateParams,UtilsService,PackageRateDataService){                                  
                                
                                var data = PackageRateDataService.getData(1);
                                var rate = null;

                                if(!$stateParams.rateId) return {}

                                if(data.length>0){
                                    for(var i=0;i<data.length;i++){
                                        if(data[i].rateId==$stateParams.rateId){
                                            rate = data[i];
                                            break;
                                        }
                                    }
                                }
                                
                                if(rate) return rate
                                else{
                                    var paramsEdit = {rateId: $stateParams.rateId}                                   
                                    var getDataPromise = null;

                                    //Editar tarifa                                    
                                    if($stateParams.elementType==1)
                                        getDataPromise = PackageRateDataService.getRoutesWithRates(paramsEdit)
                                    else
                                        getDataPromise = PackageRateDataService.getPointsWithRates(paramsEdit)
                                
                                    return getDataPromise.then(function(response){

                                                var responseData = response.data
                                                if(angular.isArray(responseData) && responseData[0].length>0)
                                                    return responseData[0][0];
                                                else
                                                    return {};
                                            });
                                }
                            }]
                        }
                    }
                }                
            })
            .state('packageRate.add', {
                url:"/edit{elementId:[0-9]{1,6}}/t{elementType:[1]{1}}/add",
                views:{
                    'edit': {
                        templateUrl: PARTIALPATH.packageRate + 'edit.html',
                        controller: 'EditController',
                        resolve: {
                            rate: ['$stateParams','UtilsService','PackageRateDataService',function($stateParams,UtilsService,PackageRateDataService){
                                
                                var data = PackageRateDataService.getData(1);
                                var rate = null;

                                if(!$stateParams.elementId) return {}

                                if(data.length>0){
                                    for(var i=0;i<data.length;i++){
                                        if(data[i].id==$stateParams.elementId){
                                            rate = data[i];
                                            break;
                                        }
                                    }
                                }

                                var paramsEdit = {rateId: $stateParams.rateId}                                   
                                    var getDataPromise = null;                                   
                                
                                if(rate) return {rateId:null ,name:rate.name,element_id:rate.element_id, element_type:rate.element_type,date:null,client_rate:0.00,provider_fee:0.00}
                                else {
                                    var getDataPromise = null;

                                    if($stateParams.elementType==1)
                                        getDataPromise = PackageRateDataService.getRoutesWithRates({routeId: $stateParams.elementId})
                                    else
                                        getDataPromise = PackageRateDataService.getPointsWithRates({routePointId: $stateParams.elementId})
                                
                                    return getDataPromise.then(function(response){
                                                var responseData = response.data
                                                if(angular.isArray(responseData) && responseData[0].length>0){
                                                    var rate = responseData[0][0];
                                                    return {rateId:null ,name:rate.name,element_id:rate.element_id, element_type:rate.element_type,date:null,client_rate:0.00,provider_fee:0.00}
                                                }
                                                else
                                                    return {};
                                            });
                                }
                        }]
                        }
                    }
                }          
            })
            .state('packageRate.points',{
                url:"/route{routeId:[0-9]{1,6}}/points",
                views:{
                    'points':{
                        templateUrl: PARTIALPATH.packageRate + 'points.html',
                        controller: 'PointsController'
                    }
                }                
            })
            .state('packageRate.points.edit',{
                url:"/edit{elementId:[0-9]{1,6}}/t{elementType:[2]{1}}/rate{rateId:[0-9]{1,6}}",
                views:{
                    'edit':{
                        templateUrl: PARTIALPATH.packageRate + 'edit.html',
                        controller: 'EditController',
                        resolve: {
                            rate: ['$stateParams','UtilsService','PackageRateDataService',function($stateParams,UtilsService,PackageRateDataService){                                  
                                
                                var data = PackageRateDataService.getData(2);
                                var rate = null;

                                if(!$stateParams.rateId) return {}

                                if(data.length>0){
                                    for(var i=0;i<data.length;i++){
                                        if(data[i].rateId==$stateParams.rateId){
                                            rate = data[i];
                                            break;
                                        }
                                    }
                                }
                                
                                if(rate) return rate
                                else{
                                    var paramsEdit = {rateId: $stateParams.rateId}                                   
                                    var getDataPromise = null;

                                    //Editar tarifa                                    
                                    if($stateParams.elementType==1)
                                        getDataPromise = PackageRateDataService.getRoutesWithRates(paramsEdit)
                                    else
                                        getDataPromise = PackageRateDataService.getPointsWithRates(paramsEdit)
                                
                                    return getDataPromise.then(function(response){

                                                var responseData = response.data
                                                if(angular.isArray(responseData) && responseData[0].length>0)
                                                    return responseData[0][0];
                                                else
                                                    return {};
                                            });
                                }
                            }]
                        }
                    }
                }
            })
            .state('packageRate.points.add', {
                url:"/edit{elementId:[0-9]{1,6}}/t{elementType:[2]{1}}/add",
                views:{
                    'edit': {
                        templateUrl: PARTIALPATH.packageRate + 'edit.html',
                        controller: 'EditController',
                        resolve: {
                            rate: ['$stateParams','UtilsService','PackageRateDataService',function($stateParams,UtilsService,PackageRateDataService){
                                
                                var data = PackageRateDataService.getData(2);
                                var rate = null;

                                if(!$stateParams.elementId) return {}

                                if(data.length>0){
                                    for(var i=0;i<data.length;i++){
                                        if(data[i].id==$stateParams.elementId){
                                            rate = data[i];
                                            break;
                                        }
                                    }
                                }

                                var paramsEdit = {rateId: $stateParams.rateId}                                   
                                    var getDataPromise = null;                                   
                                
                                if(rate) return {rateId:null ,name:rate.name,element_id:rate.element_id, element_type:rate.element_type,date:null,client_rate:0.00,provider_fee:0.00}
                                else {
                                    var getDataPromise = null;

                                    if($stateParams.elementType==1)
                                        getDataPromise = PackageRateDataService.getRoutesWithRates({routeId: $stateParams.elementId})
                                    else
                                        getDataPromise = PackageRateDataService.getPointsWithRates({routePointId: $stateParams.elementId})
                                
                                    return getDataPromise.then(function(response){
                                                var responseData = response.data
                                                if(angular.isArray(responseData) && responseData[0].length>0){
                                                    var rate = responseData[0][0];
                                                    return {rateId:null ,name:rate.name,element_id:rate.element_id, element_type:rate.element_type,date:null,client_rate:0.00,provider_fee:0.00}
                                                }
                                                else
                                                    return {};
                                            });
                                }
                        }]
                        }
                    }
                }          
            })
            .state('packageRate.points.listRates',{
                url:"/rateFor{elementId:[0-9]{1,6}}/type{elementType:[2]{1}}",
                views:{
                    'rateList':{
                        templateUrl: PARTIALPATH.packageRate + 'listRate.html',
                        controller: 'ListRateController'
                    }
                }
            });
        }
        ])

.controller('IndexController',
            ['$scope','$state','$stateParams','$injector','PATH','PARTIALPATH','ModalService','MessageBox','PackageRateDataService','PointsDataService','RoutesDataService',
            function IndexController($scope,$state,$stateParams,$injector,PATH,PARTIALPATH,ModalService,MessageBox,PackageRateDataService,PointsDataService,RoutesDataService){
                
                ngTableParams = $injector.get('ngTableParams');
                UtilsService = $injector.get('UtilsService');
                $scope.partials = PARTIALPATH.base;
                var modalPath = PARTIALPATH.modal
                var modalInfoPath = PARTIALPATH.modalInfo
                
                $scope.tableParams = new ngTableParams(
                    {   page:1,
                        count:10,
                        sorting:{id:'asc'}
                    },
                    {
                        total:0,
                        getData:function($defer,params){
                            var postParams = UtilsService.createNgTablePostParams(params);
                            
                            PackageRateDataService.getRoutesWithRates(postParams)
                            .then(function(response){
                                $scope.isLoading=false;
                                var data=response.data;
                                
                                params.total(data[1][0].records);
                                $defer.resolve(data[0]);                                
                               
                                $scope.selectedRowId = null;
                                $scope.selRow = null;
                                //Setear el listado al DAO, útil a la hora de ir a una vista dependiente
                                PackageRateDataService.setData(data[0],1);
                            });       
                        }
                    }

                );
                
                $scope.changeSelection = function(route) {       
                    var data = $scope.tableParams.data;        
                    for(var i=0;i<data.length;i++){
                    if(data[i].id!=route.id)
                        data[i].$selected=false;
                    }
                    $scope.selectedRowId = route.id;
                    $scope.selRow=route;                    
                }

                $scope.goPoints = function(route){
                    if(route)
                        $state.go('packageRate.points',{routeId:route.id});
                }

                $scope.goEdit = function(){
                    if($scope.selRow){
                        if($scope.selRow.rateId)
                            $state.go('packageRate.edit',{elementId: $scope.selRow.element_id,elementType:$scope.selRow.element_type,rateId:$scope.selRow.rateId});
                        else
                            $state.go('packageRate.add',{elementId: $scope.selRow.element_id,elementType:$scope.selRow.element_type,});
                    }else
                        MessageBox.show("Para editar una tarifa, primero debe seleccionar una ruta.");
                }

                $scope.goAdd = function(){
                    if($scope.selRow){
                        $state.go('packageRate.add',{elementId: $scope.selRow.element_id,elementType:$scope.selRow.element_type});
                    }else
                        MessageBox.show("Para agregar una tarifa, primero debe seleccionar una ruta.");
                }

                $scope.goRateList = function(){
                    if($scope.selRow){
                        $state.go('packageRate.listRates',{elementId: $scope.selRow.element_id,elementType:1});
                    }else
                        MessageBox.show("Para ver tarifas de una ruta, primero debe seleccionarla.");
                }
            }
            ])

.controller('EditController',
    ['$scope','$timeout','$state','$stateParams','$filter','PARTIALPATH','ModalService','MessageBox','CatalogService','PackageRateDataService','rate',
    function EditController($scope,$timeout,$state,$stateParams,$filter,PARTIALPATH,ModalService,MessageBox,CatalogService,PackageRateDataService,rate){
       $scope.back=function(){$state.go('^',$stateParams)};
       $scope.rate = rate;
       var modalInfoPath = PARTIALPATH.modalInfo

        $scope.dt = $filter('stringToDate')($scope.rate.date);
        /* DatePicker*/
        $scope.datePicker = {
            format: 'dd-MM-yyyy',
            toggleMin: function(){
                $scope.datePicker.minDate = null//$scope.datePicker.minDate ? null : new Date();
            },
            open: function($event){
                $event.preventDefault();
                $event.stopPropagation();
                $scope.datePicker.opened = true;
            },
            dateOptions : {
                formatYear: 'yy',
                startingDay: 1        
            }
        }

        $scope.getFormFieldCssClass = function(ngModelController) {
            if(ngModelController.$pristine) return "";
            return ngModelController.$valid ? "has-success" : "has-error";
        }

        //Selector de rutas/PVs
        $scope.selectOptions = {
            displayText: "Seleccione...",
            emptyListText:"No hay elementos a desplegar",
            emptySearchResultText:"No se encontraron resultados para '$0'",
            searchDelay:"500",
            onSelect:function($item){
                $scope.rate.element_id = ($item) ? $item.id : null;
            }
        }

        $scope.save = function(){
            if($scope.rate){
                //$scope.progressbar.loading=true;
                $scope.rate.date = $filter('date')($scope.dt, 'yyyy-MM-dd HH:mm:ss');
                /*
                if($scope.rate.client_rate <= 0 || $scope.rate.provider_fee<=0){                   
                    MessageBox.show('El valor de la tarifa del cliente o la comisión del proveedor no pueden ser igual a cero.');
                    return;
                }
                */
                
                PackageRateDataService.save($scope.rate, {alertOnSuccess:true})
                    .success(function(data, status, headers, config){
                        //$scope.progressbar.loading=false;                    
                        if (!data.error) {
                            if($scope.$parent)
                                $scope.$parent.tableParams.reload();
                            $state.go('^', $stateParams);

                        }                        
                    })
                    .error(function(data, status, headers, config){
                        //$scope.progressbar.loading = false;                    
                    });
            }
        }

        
    }
    ])

.controller('PointsController',
    ['$scope','$state','$stateParams','$injector','PATH','PARTIALPATH','MessageBox','PackageRateDataService',
    function PointsController($scope,$state,$stateParams,$injector,PATH,PARTIALPATH,MessageBox,PackageRateDataService){
        
        ngTableParams = $injector.get('ngTableParams');
        UtilsService = $injector.get('UtilsService');
        $scope.partials = PARTIALPATH.base;
        var modalPath = PARTIALPATH.modal
        var modalInfoPath = PARTIALPATH.modalInfo
        
        $scope.tableParams = new ngTableParams(
            {   page:1,
                count:30,
                sorting:{date:'desc'}
            },
            {
                total:0,
                getData:function($defer,params){               
                    var postParams = UtilsService.createNgTablePostParams(params,{routeId:$stateParams.routeId});
                    $scope.isLoading=true;
                    PackageRateDataService.getPointsWithRates(postParams)
                    .then(function(response){
                        $scope.isLoading=false;
                        var data=response.data;
                        
                        params.total(data[1][0].records);
                        $defer.resolve(data[0]);
                       
                        $scope.selectedRowId = null;
                        $scope.selRow = null;
                        $scope.headerData = data[2][0];
                        PackageRateDataService.setData(data[0],2);
                    });                    
                }
            }

        );
        
        $scope.changeSelection = function(route) {       
            var data = $scope.tableParams.data;        
            for(var i=0;i<data.length;i++){
            if(data[i].id!=route.id)
                data[i].$selected=false;
            }
            $scope.selectedRowId = route.id;
            $scope.selRow = route;
        }

        $scope.goEdit = function(){
            if($scope.selRow){
                if($scope.selRow.rateId)
                    $state.go('packageRate.points.edit',{elementId: $scope.selRow.element_id,elementType:$scope.selRow.element_type,rateId:$scope.selRow.rateId});
                else
                    $state.go('packageRate.points.add',{elementId: $scope.selRow.element_id,elementType:$scope.selRow.element_type,});
            }else
                MessageBox.show("Para editar una tarifa, primero debe seleccionar un punto de venta.");
        }

        $scope.goAdd = function(){
            if($scope.selRow){
                $state.go('packageRate.points.add',{elementId: $scope.selRow.element_id,elementType:$scope.selRow.element_type});
            }else
                MessageBox.show("Para agregar una tarifa, primero debe seleccionar un punto de venta.");
        }

        $scope.goRateList = function(){
            if($scope.selRow){
                $state.go('packageRate.points.listRates',{elementId: $scope.selRow.element_id,elementType:2});
            }else
                MessageBox.show("Para ver tarifas de un punto de venta, primero debe seleccionarlo.");
        }
    }
    ])
.controller('ListRateController',['$scope','$state','$stateParams','$injector','PATH','PARTIALPATH','MessageBox','PackageRateDataService', 
    function($scope,$state,$stateParams,$injector,PATH,PARTIALPATH,MessageBox,PackageRateDataService){
        ngTableParams = $injector.get('ngTableParams');
        UtilsService = $injector.get('UtilsService');
        $scope.partials = PARTIALPATH.base;
              
        $scope.tableParams = new ngTableParams(
            {   page:1,
                count:30//,
                //sorting:{unk:'desc'}
            },
            {
                total:0,
                getData:function($defer,params){               
                    var postParams = UtilsService.createNgTablePostParams(params,{elementId:$stateParams.elementId, elementType:$stateParams.elementType});
                    $scope.isLoading=true;
                    PackageRateDataService.getRateByElement(postParams)
                    .then(function(response){
                        $scope.isLoading=false;
                        var data=response.data;
                        
                        params.total(data[1][0].records);
                        $defer.resolve(data[0]);
                       
                        $scope.selectedRowId = null;
                        $scope.selRow = null;
                        $scope.headerData = data[2][0];
                        PackageRateDataService.setData(data[0]);
                    });                    
                }
            }

        );
        
        $scope.changeSelection = function(route) {       
            var data = $scope.tableParams.data;        
            for(var i=0;i<data.length;i++){
            if(data[i].id!=route.id)
                data[i].$selected=false;
            }
            $scope.selectedRowId = route.id;
            $scope.selRow = route;
        }

        $scope.back=function(){$state.go('^',$stateParams)};
        
    }
    ]);
})();