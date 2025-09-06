(function(){
    angular.module('masDistribucion.warehouseman',[
    'ui.router',
    'ngTable',
    'qrScanner'
    ])

.config(['$stateProvider', '$urlRouterProvider','$locationProvider','CONFIG',WarehousemanConfig])
.controller('WarehousemanIndexController',
    ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
    'ModalService','WarehousemanDataService','UtilsService','CONFIG',
    WarehousemanIndexController
    ])
    
.controller('WarehousemanPointController',
    ['$scope','$location','$anchorScroll','$timeout','$state','$stateParams','ngTableParams',
    'ModalService','WarehousemanDataService','UtilsService','CONFIG',
    WarehousemanPointController
    ])
    
.controller('WarehousemanPackController',
    ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
    'ModalService','WarehousemanDataService','UtilsService','CONFIG',
    WarehousemanPackController
    ])
    
.controller('WarehousemanActivityController', [
    '$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
    'ModalService','WarehousemanDataService','UtilsService','CONFIG',
    WarehousemanActivityController
    ])
    
.controller('WarehousemanSaveTransferController',
    ['$scope','$timeout','$state','$stateParams','PARTIALPATH','ModalService','CatalogService',
    'WarehousemanDataService','warehouseman','CONFIG',
    WarehousemanSaveTransferController
    ])
    
.controller('WarehousemanPackageTrackingController',
    ['$scope','$timeout','$state','$stateParams','ngTableParams','ModalService',
    'WarehousemanDataService','UtilsService','CONFIG',
    WarehousemanPackageTrackingController
    ])

/************************************PAQUETES RECHAZADOS ************************************************/
.controller('WarehousemanRejectedController',
    	    ['$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH','ModalService',
    	    'WarehousemanDataService','UtilsService','CONFIG',
    	    WarehousemanRejectedController
    ])
    
.controller('WarehousemanRejectededitController',
            ['$scope','$timeout','$state','$stateParams','PARTIALPATH','ModalService',
             'WarehousemanDataService','UtilsService','orderId','$filter',
            WarehousemanRejectededitController
            ])
    
/************************************PAQUETES RECHAZADOS ************************************************/
    
})();