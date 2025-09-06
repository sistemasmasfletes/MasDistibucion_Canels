(function(){
angular.module('masDistribucion.ciudades',[
    "ui.router",
    'ngTable'
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH', 'CONFIG',CiudadesConfig])

.controller('CiudadesIndexController',
    ['$rootScope','$scope','$state','$stateParams','ModalService','CiudadesDataService','PATH','PARTIALPATH','$injector','CONFIG', CiudadesIndexController]
    )
.controller('CiudadesEditController',
    ['$scope','$timeout','$state','$stateParams','$filter',
    'PARTIALPATH','ModalService','CatalogService','CiudadesDataService','UtilsService',
    'ciudad', 'CONFIG',
    CiudadesEditController
    ]);
})();