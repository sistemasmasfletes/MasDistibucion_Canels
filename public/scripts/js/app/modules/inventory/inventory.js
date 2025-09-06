(function(){
angular.module('masDistribucion.inventory',[
    'ui.router',
    'ngTable'
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','CONFIG',InventoryConfig])
.controller('InventoryIndexController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams',
            'ModalService','InventoryDataService','UtilsService','CONFIG',
            InventoryIndexController
            ])
.controller('InventoryPacksController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams',
            'ModalService','InventoryDataService','UtilsService','CONFIG',
            InventoryPacksController
            ])
})();