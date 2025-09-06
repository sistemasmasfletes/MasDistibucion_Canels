(function(){
angular.module('masDistribucion.schedule',[
    "ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',ScheduleConfig])

.controller('ScheduleIndexController',
    ['$rootScope','$scope','$state','$stateParams','ModalService','ScheduleDataService','PATH','PARTIALPATH','$injector', ScheduleIndexController]
    )
.controller('ScheduleDetailController',
    ['$rootScope','$scope','$state','$stateParams','ModalService','ScheduleDataService','PARTIALPATH','$injector', ScheduleDetailController]
    )
.controller('ScheduleEditController',
    ['$scope','$timeout','$state','$stateParams','$filter',
    'PARTIALPATH','ModalService','CatalogService','ScheduleDataService','RoutesDataService','VehiclesDataService','UsersDataService','UtilsService',
    'schedule',
    ScheduleEditController
    ])
    .controller('ScheduledDatesController',
        ['$scope','$state','$stateParams','ModalService','ScheduleDataService','PARTIALPATH','$injector', ScheduledDatesController]
    )
    .controller('ScheduledDatesEditController',
        ['$scope','$timeout','$state','$stateParams','$filter','PARTIALPATH','ModalService','CatalogService','ScheduleDataService','RoutesDataService','VehiclesDataService','UsersDataService','UtilsService','schedule', ScheduledDatesEditController]
    );
})();