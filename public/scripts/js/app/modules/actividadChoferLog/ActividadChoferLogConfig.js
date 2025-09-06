function ActividadChoferLogConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
    $stateProvider
    .state('actividadChoferLog', {
        url: "/actividadChoferLog",
        views:{
            'main':{
                templateUrl: PARTIALPATH.actividaChoferLog + 'index.html',
                controller: 'ActividadChoferLogIndexController'
            }
        }
    });
    
}