function ReportConfig($stateProvider, $urlRouterProvider,$locationProvider, CONFIG)
{
    $stateProvider
    .state('report', {
        url: "/report",
        views:{
            'main':{
                templateUrl: CONFIG.PARTIALS + 'report/index.html',
                controller: 'ReportIndexController'
            }
        }
    });
}