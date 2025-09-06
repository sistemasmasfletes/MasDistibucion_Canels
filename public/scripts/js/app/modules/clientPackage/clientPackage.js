(function(){
angular.module('masDistribucion.clientPackage',[
    "ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',
        function ClientPackageConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
            $stateProvider
            .state('clientPackage', {
                url: "/clientPackage",
                views:{
                    'main':{
                        templateUrl: PARTIALPATH.clientPackage + 'index.html',
                        controller: 'ClientPackageIndexController'
                    }
                }
            })
            .state('clientPackage.edit', {
                url:"/edit_{packageId:[0-9]{1,6}}",
                views:{
                    'edit': {
                        templateUrl: PARTIALPATH.clientPackage + 'edit.html',
                        controller: 'ClientPackageEditController',
                        resolve: {
                            package: ['$stateParams','ClientPackageDataService',function($stateParams,ClientPackageDataService){
                            var data=ClientPackageDataService.getData();
                            var package = ClientPackageDataService.findById(data,$stateParams.packageId)
                            if(package) return package;
                            else{
                                return ClientPackageDataService.getPackages({packageId: $stateParams.packageId})
                                    .then(function(response){
                                        var data = response.data.data;
                                        if(response && angular.isArray(data) && data.length>0)
                                            return data[0];
                                        else
                                            return {};
                                    })
                            }
                            
                        }]
                        }
                    }
                  }          
            })
            .state('clientPackage.add', {
                url:"/add",
                views:{
                    'edit': {
                        templateUrl: PARTIALPATH.clientPackage + 'edit.html',
                        controller: 'ClientPackageEditController',
                        resolve: {
                            package: function(){
                                return {};                                  
                            }
                        }
                    }
                }          
            })
        }
        ])
.controller('ClientPackageIndexController',
            ['$scope','$state','$stateParams',
            'PATH','PARTIALPATH','MessageBox','ClientPackageDataService','$injector',
            function ClientPackageIndexController($scope,$state,$stateParams,PATH,PARTIALPATH,MessageBox,ClientPackageDataService,$injector){
                
                ngTableParams = $injector.get('ngTableParams');
                UtilsService = $injector.get('UtilsService');
                $scope.partials = PARTIALPATH.base;
                var idusr;
                
                $scope.tableParams = new ngTableParams(
                    {   page:1,
                        count:10,
                        sorting:{name:'asc'}
                    },
                    {
                        total:0,
                        getData:function($defer,params){
                            var postParams = UtilsService.createNgTablePostParams(params,{clientId:null});
                            
                            ClientPackageDataService.getPackages(postParams)
                            .then(function(response){
                                var data=response.data;
                                $scope.isLoading=false;
                                params.total(data.meta.totalRecords);
                                $defer.resolve(data.data);  
                                idusr = data.meta.idusr;
                                
                                ClientPackageDataService.setData(data.data);
                            });       
                        }
                    }

                );

                $scope.changeSelection = function(pkg) {       
                    var data = $scope.tableParams.data;        
                    for(var i=0;i<data.length;i++){
                    if(data[i].id!=pkg.id)
                        data[i].$selected=false;
                    }
                    $scope.selectedRowId = pkg.id;
                    $scope.selRow=pkg;                    
                }

                $scope.delete = function(obPackage){
                    if(obPackage && obPackage.id){
                       	if(obPackage.user_id != 58){
	                        MessageBox.confirm("¿Estás seguro de eliminar este paquete?","Elimimar").then(function(result){
	                            $scope.loading = true;
	                            ClientPackageDataService.delete({id:obPackage.id}, {alertOnSuccess:true})
	                            .success(function(data, status, headers, config){
	                                $scope.loading=false;                    
	                                if (!data.error) {
	                                    $scope.tableParams.reload();
	                                }                        
	                            })
	                            .error(function(data, status, headers, config){
	                                $scope.loading = false;                    
	                            });
	                        });
                       	}else{
                            MessageBox.show('Este registro no puede ser eliminado.');
                       	}
                    }else
                        MessageBox.show('Para poder eliminar, es necesario seleccionar primero un registro.');
                    
                }

                $scope.goEdit=function(package){
                    if(package && package.id){
                    	if(package.user_id != 58){
                    		$state.go('clientPackage.edit',{packageId:package.id});
                    	}else{
                    		if(package.user_id == idusr){
                        		$state.go('clientPackage.edit',{packageId:package.id});
                    		}else{
                                MessageBox.show('Este registro corresponde a un catálogo general y no puede ser modificado.');
                    		}
                    	}
                    }else{
                        MessageBox.show('Para poder editar, es necesario seleccionar primero un registro.');
                    }
                }

                $scope.goAdd = function(){
                    $state.go('clientPackage.add');
                }

            }
            ])
.controller('ClientPackageEditController',
    ['$scope','$timeout','$state','$stateParams','$injector','PARTIALPATH','MessageBox','ClientPackageDataService','package',
    function  ClientPackageEditController($scope,$timeout,$state,$stateParams,$injector,PARTIALPATH,MessageBox,ClientPackageDataService,package){
        $scope.package=package;

        ngTableParams = $injector.get('ngTableParams');
        UtilsService = $injector.get('UtilsService');
        $scope.partials = PARTIALPATH.base;
        
        $scope.tableParams = new ngTableParams(
            {   page:1,
                count:10
            },
            {
                total:0,
                getData:function($defer,params){
                    var postParams = UtilsService.createNgTablePostParams(params,{packageId:$stateParams.packageId});
                    
                    ClientPackageDataService.getProductsFromPackage(postParams)
                    .then(function(response){
                        var data=response.data;
                        $scope.isLoading=false;
                        params.total(data.meta.totalRecords);
                        $defer.resolve(data.data);                                
                    });       
                }
            }

        );

        $scope.save = function(){
            if($scope.package){
                $scope.loading=true;
                ClientPackageDataService.save($scope.package, {alertOnSuccess:true})
                    .success(function(data, status, headers, config){
                        $scope.loading=false;                    
                        if (!data.error) {
                            if($scope.$parent)
                                $scope.$parent.tableParams.reload();
                            $state.go('^', $stateParams);
                        }
                    })
                    .error(function(data, status, headers, config){
                        $scope.loading = false;
                    });

            }
        }


        $scope.back=function(){
            $state.go('^',$stateParams)
        }

        $scope.getFormFieldCssClass = function(ngModelController) {
            if(ngModelController.$pristine) return "";
            return ngModelController.$valid ? "has-success" : "has-error";
        }
    }
    ]);
})();
