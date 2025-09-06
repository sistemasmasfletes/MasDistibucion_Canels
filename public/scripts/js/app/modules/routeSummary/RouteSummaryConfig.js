function RouteSummaryConfig($stateProvider, $urlRouterProvider, $locationProvider, CONFIG)
{
    //Pantalla de rutas
    $stateProvider
            .state('routeSummary', {
                url: "/routeSummary",
                views: {
                    'main': {
                        templateUrl: CONFIG.PARTIALS + 'routeSummary/index.html',
                        controller: 'RouteSummaryIndexController'
                    }
                }
            })
            //Pantalla informativa de paquetes de la ruta
            .state('routeSummary.routeActivity', {
                url: "/scheduleRouteId/{id}/info",
                views: {
                    'edit': {
                        templateUrl: CONFIG.PARTIALS + 'routeSummary/edit6.html',
                        controller: 'PacksRouteController'

                    }
                }
            })
            //pantalla de puntos
            .state('routeSummary.edit', {
                url: "/scheduleRouteId/{id}",
                views: {
                    'edit': {
                        templateUrl: CONFIG.PARTIALS + 'routeSummary/edit.html',
                        controller: 'RouteSummaryEditController'

                    }
                }
            })
            //Ver actividad sin escannear paquetes
            .state('routeSummary.activity', {
                parent: 'routeSummary.edit',
                url: "/:id2/:id1/",
                views: {
                    'activity@routeSummary.edit': {
                        templateUrl: CONFIG.PARTIALS + 'routeSummary/edit5.html',
                        controller: 'ActivityPackageController'

                    }
                }
            })
            //Pantalla de paquetes    
            .state('routeSummary.add', {
                url: "/scheduleRouteId/{id2}/routePointId/{id}",
                views: {
                    'edit': {
                        templateUrl: CONFIG.PARTIALS + 'routeSummary/edit2.html',
                        controller: 'RouteSummaryEditPackageController'

                    }
                }
            })

            .state('routeSummary.view', {//Salvar evidencia
                url: "/scheduleRouteId/{scheId:[0-9]{1,8}}/routePointId/{point:[0-9]{1,8}}/ocId/{id:[0-9]{1,8}}/routePointActivityId/{rpaId:[0-9]{1,8}}/",
                views: {
                    'edit': {
                        templateUrl: CONFIG.PARTIALS + 'routeSummary/edit3.html',
                        controller: 'RouteSummaryEditPointsController',
                        resolve: {
                            routeSummary: function () {
                                return {};
                            },
                            routePointActivity:['$stateParams','RouteSummaryDataService',function($stateParams,RouteSummaryDataService){
                                var params = {idrow:null, idrow2:null,routePointActivityId:$stateParams.rpaId}
                                return RouteSummaryDataService.getRouteSummaryPackage(params)
                                .then(function(response){
                                    var data=response.data.data;
                                    if(data && angular.isArray(data) && data.length>0)
                                        return data[0];
                                    else
                                        return {};
                                });
                            }]
                        }
                    }
                }
            })

            .state('routeSummary.info', {//Información del paquete
                url: "/scheduleRouteId/{scheId:[0-9]{1,8}}/routePointId/{point:[0-9]{1,8}}/ocId/{id:[0-9]{1,8}}/routePointActivityId/{rpaId:[0-9]{1,8}}/info/",
                views: {
                    'edit': {
                        templateUrl: CONFIG.PARTIALS + 'routeSummary/edit4.html',
                        controller: 'RouteSummaryEvidenceController',
                        resolve: {
                            routeSummary: function () {
                                return {};
                            }
                        }
                    }
                }
            });
}