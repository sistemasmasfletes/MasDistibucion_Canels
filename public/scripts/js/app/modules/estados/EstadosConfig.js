function EstadosConfig($stateProvider, $urlRouterProvider, $locationProvider, PARTIALPATH, CONFIG) {
    $stateProvider
            .state('estados', {
                url: "/estados",
                views: {
                    'main': {
                        templateUrl: PARTIALPATH.estados + '/index.html',
                        controller: 'EstadosIndexController'
                    }
                }
            })
            .state('estados.add', {
                url: "/add",
                views: {
                    'edit': {
                        templateUrl: PARTIALPATH.estados + 'edit.html',
                        controller: 'EstadosEditController',
                        resolve: {
                            estado: function () {
                                return{};
                            }
                        }
                    }
                }
            })
            .state('estados.edit', {
                url: "/{estadoId:[0-9]{1,9}}",
                views: {
                    'edit': {
                        templateUrl: CONFIG.PARTIALS + 'estados/edit.html',
                        controller: 'EstadosEditController',
                        resolve: {
                            estado: ['$stateParams', 'UtilsService', 'EstadosDataService', function ($stateParams, UtilsService, EstadosDataService) {
                                    var data = EstadosDataService.getData();
                                    var estado = UtilsService.findById(data, $stateParams.estadoId);
                                    if (estado) {
                                        return estado;
                                    } else {
                                        return EstadosDataService.getEstadoById({id: $stateParams.estadoId})
                                                .then(function (response) {
                                                    if (response.data && response.data.data.length > 0) {
                                                        return response.data.data[0];
                                                    } else {
                                                        return {};
                                                    }
                                                })
                                    }
                                }]
                        }
                    }
                }
            });
}