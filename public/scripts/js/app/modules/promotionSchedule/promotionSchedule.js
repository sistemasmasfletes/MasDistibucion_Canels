(function(){
angular.module('masDistribucion.promotionSchedule',[
    "ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',
        function PromotionScheduleConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
            $stateProvider
            .state('promotionSchedule', {
                url: "/promotionSchedule",
                views:{
                    'main':{
                        templateUrl: PARTIALPATH.promotionSchedule + 'index.html',
                        controller: 'PromotionScheduleIndexController'
                    }
                }
            })
        }
        ])
.controller('PromotionScheduleIndexController',
            ['$scope','$state','$stateParams','PARTIALPATH',
            'ModalService','MessageBox','PromotionScheduleDataService','CatalogService','$injector',
            function PromotionScheduleIndexController($scope,$state,$stateParams,PARTIALPATH,ModalService,MessageBox,PromotionScheduleDataService,CatalogService,$injector){
                
                ngTableParams = $injector.get('ngTableParams');
                $scope.UtilsService = $injector.get('UtilsService');
                $scope.partials = PARTIALPATH.base;
                $scope.getInterestLevel = CatalogService.getInterestLevel();

                $scope.tableParams = new ngTableParams(
                    {   page:1,
                        count:10,
                        sorting:{creationDate:'asc'}
                    },
                    {
                        total:0,
                        getData:function($defer,params){
                            var postParams = $scope.UtilsService.createNgTablePostParams(params,{clientId:null});
                            
                            PromotionScheduleDataService.getSchedule(postParams)
                            .then(function(response){
                                var data=response.data;
                                $scope.isLoading=false;
                                params.total(data.meta.totalRecords);
                                $defer.resolve(data.data);                                
                                
                                PromotionScheduleDataService.setData(data.data);
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

                $scope.openSurveyDesc=function(promotion){
                    return ModalService.showModal(
                    {   templateUrl: 'templateShowSurvey.html'/* plantilla en partials/promotionSchedule/index.html */,
                        controller:'ShowSurveyController',size:'md',
                        keyboard:false,
                        resolve:{
                            promotion:function(){
                                return promotion;
                            }
                        }
                    },
                    {});
                }

                ShowSurveyController = function($scope, $modalInstance,CatalogService,UtilsService,MessageBox,promotion){
                    $scope.promotion = promotion;
                    $scope.getInterestLevel = CatalogService.getInterestLevel();
                    $scope.getConsumerType = CatalogService.getConsumerType();
                    $scope.getRequest = CatalogService.getRequest();

                    $scope.UtilsService = UtilsService;
                    //Botones de la ventana emergente
                    $scope.ok = function (result) {                        
                        $modalInstance.close(promotion);                        
                    };

                    
                }


            }
            ]);
})();
