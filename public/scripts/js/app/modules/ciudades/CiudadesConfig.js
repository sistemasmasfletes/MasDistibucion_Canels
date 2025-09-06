function CiudadesConfig($stateProvider, $urlRouterProvider, $locationProvider, PARTIALPATH, CONFIG) {
    $stateProvider
            .state('ciudades', {
                url: "/ciudades",
                views: {
                    'main': {
                        templateUrl: PARTIALPATH.ciudades + '/index.html',
                        controller: 'CiudadesIndexController'
                    }
                }
            })
            .state('ciudades.add', {
                url: "/add",
                views: {
                    'edit': {
                        templateUrl: PARTIALPATH.ciudades + 'edit.html',
                        controller: 'CiudadesEditController',
                        resolve: {
                            ciudad: function () {
                                return{};
                            }
                        }
                    }
                }
            })
            .state('ciudades.edit', {
                url: "/{ciudadId:[0-9]{1,9}}",
                views: {
                    'edit': {
                        templateUrl: CONFIG.PARTIALS + 'ciudades/edit.html',
                        controller: 'CiudadesEditController',
                        resolve: {
                            ciudad: ['$stateParams', 'UtilsService', 'CiudadesDataService', function ($stateParams, UtilsService, CiudadesDataService) {
                                    var data = CiudadesDataService.getData();
                                    var ciudad = UtilsService.findById(data, $stateParams.ciudadId);
                                    if (ciudad) {
                                        return ciudad;
                                    } else {
                                        return CiudadesDataService.getCiudadById({id: $stateParams.ciudadId})
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