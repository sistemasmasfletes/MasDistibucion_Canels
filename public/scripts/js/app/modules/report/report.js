(function(){
angular.module('masDistribucion.report',[
    'ui.router',
    'ngTable'
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','CONFIG',ReportConfig])
.controller('ReportIndexController',
            ['$rootScope','$scope','$timeout','$state','$stateParams','ngTableParams',
            'ModalService','ReportDataService','UtilsService','CONFIG',
            ReportIndexController
            ])
})();