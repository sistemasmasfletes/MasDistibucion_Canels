(function(){
angular.module('masDistribucion.configuration',[
    "ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',
        function ConfigurationConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
            $stateProvider
            .state('configuration', {
                url: "/configuration",
                views:{
                    'main':{
                        templateUrl: PARTIALPATH.configuration + 'index.html',
                        controller: 'ConfigurationIndexController'
                    }
                }
            })
        }])
.controller('ConfigurationIndexController',
            ['$scope','$state','$stateParams','$injector','PATH','PARTIALPATH','ModalService','MessageBox','ConfigurationDataService',
            function ConfigurationIndexController($scope,$state,$stateParams,$injector,PATH,PARTIALPATH,ModalService,MessageBox,ConfigurationDataService){
                $scope.configuration = {};
                $scope.progressbar = {};
                
                function updateData(){
                    $scope.progressbar.loading=true; 
                    ConfigurationDataService.getConfiguration(null,null).then(function(response){
                       $scope.configuration = response.data[0];
                       $scope.progressbar.loading = false;
                    });
                }

                $scope.save = function(){
                    if($scope.configuration){
                        $scope.progressbar.loading=true; 
                        ConfigurationDataService.save($scope.configuration, {alertOnSuccess:true})
                        .success(function(data, status, headers, config){                                                
                            if (!data.error) updateData();
                            $scope.progressbar.loading=false;

                        })
                        .error(function(data, status, headers, config){
                            $scope.progressbar.loading = false;                    
                        });
                    }
                }

                updateData();
            }
        ])
})();