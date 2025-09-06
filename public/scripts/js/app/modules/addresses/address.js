(function(){
    angular.module('masDistribucion.addresses',[
        "ui.router",
        'ngTable'
    ])
.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', 'CONFIG',AddressConfig])
.controller('AddressIndexController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
            'ModalService','AddressDataService','UtilsService','CONFIG',
            AddressIndexController
            ])
            
.controller('AddressesEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','AddressDataService',
            'address','CONFIG',
            AddressesEditController
            ]);
    
})();