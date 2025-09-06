(function(){
angular.module('masDistribucion.bancos', ['ui.router','ngSanitize', 'ngCsv'])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',BancosConfig])
.controller('BancosIndexController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','JQGridService','BancosDataService',
            BancosIndexController
            ])

.controller('BancosEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PATH','PARTIALPATH','ModalService','CatalogService','BancosDataService',
            'banco','$http',
            BancosEditController
            ]);
})();