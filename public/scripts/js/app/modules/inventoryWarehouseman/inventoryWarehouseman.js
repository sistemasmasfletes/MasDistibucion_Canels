(function(){
angular.module('masDistribucion.inventoryWarehouseman',[
    'ui.router',
    'ngTable'
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','CONFIG',InventoryWarehousemanConfig])
.controller('InventoryWarehousemanController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
            'ModalService','InventoryWarehousemanDataService','UtilsService','CONFIG',
            InventoryWarehousemanController
            ])
})();