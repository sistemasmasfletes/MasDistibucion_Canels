(function(){
angular.module('masDistribucion.points',[
    "ui.router",
    'ngTable'
])
.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', 'CONFIG',PointsConfig])
.controller('PointsIndexController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams',
            'PARTIALPATH','ModalService','PointsDataService','UtilsService','CONFIG',
            PointsIndexController
            ])
            
.controller('PointsEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','PointsDataService',
            'point','CONFIG','UtilsService','$filter',
            PointsEditController
            ])

.controller('ContactIndexController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams',
            'PARTIALPATH','ModalService','PointsDataService','UtilsService','CONFIG',
            ContactIndexController
            ])
            
.controller('ContactEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','PointsDataService',
            'contact','CONFIG',
            ContactEditController
            ])
.controller('ClassificationEditController',
            ['$scope','$timeout','$state','$stateParams',
            'PARTIALPATH','ModalService','CatalogService','PointsDataService',
            'classification','CONFIG',
            ClassificationEditController
            ]);
})();