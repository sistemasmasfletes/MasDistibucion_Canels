(function(){
angular.module('masDistribucion.estados',[
    "ui.router",
    'ngTable'
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH', 'CONFIG',EstadosConfig])

.controller('EstadosIndexController',
    ['$rootScope','$scope','$state','$stateParams','ModalService','EstadosDataService','PATH','PARTIALPATH','$injector','CONFIG',  EstadosIndexController]
    )
.controller('EstadosEditController',
    ['$scope','$timeout','$state','$stateParams','$filter',
    'PARTIALPATH','ModalService','CatalogService','EstadosDataService','UtilsService',
    'estado', 'CONFIG','$http', 'PATH',
    EstadosEditController
    ]);
})();