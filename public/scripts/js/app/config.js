function config($httpProvider,$stateProvider, $urlRouterProvider,$locationProvider, CONFIG) {
    $locationProvider.hashPrefix('!');
    $urlRouterProvider.otherwise("/");
    $stateProvider
        .state('index', {
            url: "/",
            views:{
                'main':{
                templateUrl: CONFIG.PARTIALS + 'init.html',
                controller: 'InitController'
                }
            }
        })
        .state('forbidden',{
            url:"/forbidden",
            views:{
                'main':{
                    templateUrl: CONFIG.PARTIALS + 'forbidden.html'
                    //controller: 'InitController'
                }
            }
        });

    $httpProvider.interceptors.push('HttpInterceptor');
}