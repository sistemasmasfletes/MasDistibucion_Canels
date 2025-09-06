(function () {
    angular.module('masDistribucion.zone', ["ui.router"])
            .config(['$stateProvider', '$urlRouterProvider', '$locationProvider', 'PARTIALPATH',
                function ZoneConfig($stateProvider, $urlRouterProvider, $locationProvider, PARTIALPATH) {
                    $stateProvider
                            .state('zone', {
                                url: "/zone",
                                views: {
                                    'main': {
                                        templateUrl: PARTIALPATH.zone + 'index.html',
                                        controller: 'ZoneIndexController'
                                    }
                                }
                            })
                            .state('zone.edit', {
                                url: "/edit/{zoneId:[0-9]{1,6}}",
                                views: {
                                    'edit': {
                                        templateUrl: PARTIALPATH.zone + 'edit.html',
                                        controller: 'ZoneEditController',
                                        resolve: {
                                            zone: ['$stateParams', 'UtilsService', 'ZoneDataService', function ($stateParams, UtilsService, ZoneDataService) {
                                                    var data = ZoneDataService.getData();
                                                    var zone = null;

                                                    if (!$stateParams.zoneId)
                                                        return{}

                                                    zone = UtilsService.findById(data, $stateParams.zoneId);

                                                    if (zone) {
                                                        return zone;
                                                    } else {
                                                        var paramsEdit = {zoneId: $stateParams.zoneId}
                                                        var getDataPromise = null;

                                                        getDataPromise = ZoneDataService.getZone({filter: paramsEdit})

                                                        return getDataPromise.then(function (response) {
                                                            var responseData = response.data.data;
                                                            if (angular.isArray(responseData)) {
                                                                return responseData[0];
                                                            } else {
                                                                return {};
                                                            }
                                                        });
                                                    }
                                                }]
                                        }
                                    }
                                }
                            })
                            .state('zone.add', {
                                url: "/add",
                                views: {
                                    'edit': {
                                        templateUrl: PARTIALPATH.zone + 'edit.html',
                                        controller: 'ZoneEditController',
                                        resolve: {
                                            zone: function () {
                                                return {};
                                            }
                                        }
                                    }
                                }
                            })
                }
            ])
            .controller('ZoneIndexController',
                    ['$scope', '$state', '$stateParams', '$injector', 'PARTIALPATH', 'MessageBox', 'ZoneDataService',
                        function ZoneIndexController($scope, $state, $stateParams, $injector, PARTIALPATH, MessageBox, ZoneDataService) {
                            ngTableParams = $injector.get('ngTableParams');
                            UtilsService = $injector.get('UtilsService');
                            $scope.partials = PARTIALPATH.base;

                            $scope.tableParams = new ngTableParams(
                                    {
                                        page: 1,
                                        count: 10,
                                        sorting: {name: 'ASC'}
                                    },
                            {
                                total: 0,
                                getData: function ($defer, params) {
                                    var postParams = UtilsService.createNgTablePostParams(params);

                                    ZoneDataService.getZone(postParams)
                                            .then(function (response) {
                                                $scope.isLoading = false;
                                                var data = response.data;

                                                params.total(data.meta.totalRecords);
                                                $defer.resolve(data.data);

                                                $scope.selectedRowId = null;
                                                $scope.selRow = null;
                                                ZoneDataService.setData(data.data);
                                            });
                                }
                            });
                            $scope.updateSelection = function (zone) {
                                var data = $scope.tableParams.data;
                                for (var i = 0; i < data.length; i++) {
                                    if (data[i].id != zone.id)
                                        data[i].$selected = false;
                                    else
                                        data[i].$selected = true;
                                }
                                $scope.selectedRowId = zone.id;
                                $scope.selRow = zone;
                            }

                            $scope.goEdit = function () {
                                if ($scope.selRow)
                                    $state.go("zone.edit", {zoneId: $scope.selRow.id});
                                else
                                    MessageBox.show("Para editar, es necesario seleccionar primero un registro.");
                            }

                            $scope.goAdd = function () {
                                $state.go('zone.add', $stateParams);
                            }

                            $scope.delete = function (obZone) {
                                if (obZone && obZone.id) {
                                    MessageBox.confirm("¿Estás seguro de eliminar la zona?", "Eliminar").then(function (result) {
                                        $scope.loading = true;
                                        ZoneDataService.delete({id: obZone.id}, {alertOnSuccess: true})
                                                .success(function (data, status, headers, config) {
                                                    $scope.loading = false;
                                                    if (!data.error) {
                                                        $scope.tableParams.reload();
                                                    }
                                                })
                                                .error(function (data, status, headers, config) {
                                                    $scope.loading = false;
                                                });
                                    });
                                } else
                                    MessageBox.show('Para poder eliminar, es necesario seleccionar primero un registro.');
                            }

                            $scope.toggleFilter = function (params) {
                                params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
                            }

                            $scope.customFilter = [
                                {name: 'name', type: 'text', label: 'Zona'}
                            ]

                            $scope.filterOpen = false;
                            $scope.openFilter = function () {
                                $scope.filterOpen = true;
                            }

                            $scope.appFilter = function (filter) {
                                $scope.tableParams.settings().filterDelay = 0
                                $scope.tableParams.$params.filter = filter;
                            }

                        }
                    ]
                    )
            .controller('ZoneEditController',
                    ['$scope', 'ModalService', '$timeout', '$http', '$state', '$stateParams', '$filter', 'PATH', 'PARTIALPATH', 'MessageBox', 'CatalogService', 'ZoneDataService', 'zone',
                        function ZoneEditController($scope,ModalService, $timeout, $http, $state, $stateParams, $filter, PATH, PARTIALPATH, MessageBox, CatalogService, ZoneDataService, zone) {
                            $scope.zone = zone;

                            $scope.back = function () {
                                $state.go('^', $stateParams);
                            }
                            $scope.data = [{id: 1, name: 'Name1'}, {id: 2, name: 'Name2'}];
                            
                            $scope.verificarExistencia = function(){
                                var data = {zona: $scope.zone};
                                var url = PATH.zone + 'verificar';
                                var request = $http({
                                    method: 'POST',
                                    url: url,
                                    data: data
                                });
                                
                                request.success($scope.successVerificar);
                            };
                            
                            $scope.successVerificar = function (data) {
                                $scope.si_existe = 1;
                                $scope.existe = data['existe'];
                                var form_data = new FormData();

                                form_data.append('zoneId', $scope.zone.id);
                                form_data.append('name', $scope.zone.name);
                                if ($scope.existe != $scope.si_existe) {
                                    $scope.loading = true;
                                    $http.post(
                                            PATH.zone + 'save',
                                            form_data,
                                            {
                                                transformRequest: angular.identity,
                                                headers: {'Content-Type': undefined}
                                            }
                                    ).success(function (response) {
                                        $scope.loading = false;
                                        MessageBox.show("Los datos se guardaron satisfactoriamente.")
                                                .then(function () {
                                                    $scope.back();
                                                    $scope.tableParams.reload();
                                                });
                                    });
                                } else {
                                    var modalOptions = {
                                        actionButtonText: 'Aceptar',
                                        bodyText: "El registro ya existe"
                                    };
                                    ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions);
                                    $scope.loading = false;
                                }
                            };

                            $scope.save = function () {
                                $scope.verificarExistencia();
                            }
                            $scope.getFormFieldCssClass = function (ngModelController) {
                                if (ngModelController.$pristine)
                                    return "";
                                return ngModelController.$valid ? "has-success" : "has-error";
                            }

                        }]
                    )
})();