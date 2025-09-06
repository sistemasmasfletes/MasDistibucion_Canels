function ActividaChoferLogConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('actividaChoferLog', {
        url: "/actividaChoferLog",
        views:{
            'main':{
                templateUrl: PARTIALPATH.actividaChoferLog + 'index.html',
                controller: 'ActividaChoferLogIndexController'
            }
        }
    });
    
}