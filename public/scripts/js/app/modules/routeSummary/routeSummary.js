(function(){
angular.module('masDistribucion.routeSummary',[
    'ui.router',
    'ngTable',
    'qrScanner'
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','CONFIG',RouteSummaryConfig])
.controller('RouteSummaryIndexController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
            'ModalService','MessageBox','RouteSummaryDataService','UtilsService','CONFIG',
            RouteSummaryIndexController
            ])
            
.controller('RouteSummaryEditController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams',
             'PARTIALPATH','ModalService','RouteSummaryDataService','UtilsService','CONFIG',
            RouteSummaryEditController
            ])

.controller('RouteSummaryEditPackageController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
            'ModalService','RouteSummaryDataService','UtilsService','CONFIG',
            RouteSummaryEditPackageController
            ])
            
.controller('ActivityPackageController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
            'ModalService','RouteSummaryDataService','UtilsService','CONFIG',
            ActivityPackageController
            ])            
            
.controller('RouteSummaryEditPointsController',
            ['$scope','$timeout','$state','$stateParams','ngTableParams',
            'PARTIALPATH','ModalService','MessageBox','CatalogService','RouteSummaryDataService',
            'routeSummary','CONFIG','routePointActivity',
            RouteSummaryEditPointsController
            ])
            
/*.controller('PacksRouteController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams','PARTIALPATH',
            'ModalService','RouteSummaryDataService','UtilsService','CONFIG',
            PacksRouteController
            ])*/            

.controller('RouteSummaryEvidenceController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams',
            'ModalService','RouteSummaryDataService','UtilsService','CONFIG',
            RouteSummaryEvidenceController
            ])
})();