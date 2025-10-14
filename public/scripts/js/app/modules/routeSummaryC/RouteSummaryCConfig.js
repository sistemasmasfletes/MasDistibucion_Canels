function RouteSummaryCConfig($stateProvider, $urlRouterProvider, $locationProvider, PARTIALPATH){
    
    $stateProvider //Index
    .state('routeSummaryC',{
        url:"/routeSummaryC",
            views:{
                'main':{
                    templateUrl: PARTIALPATH.routeSummaryC + 'index.html',
                    controller: 'RouteSummaryIndexController'
                }
            }
    })
}