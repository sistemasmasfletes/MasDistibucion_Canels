(function () {
    angular.module('masDistribucion.cardOperators', ["ui.router"])
            .config(['$stateProvider', '$urlRouterProvider', '$locationProvider', 'PARTIALPATH',
                function CardOperatorsConfig($stateProvider, $urlRouterProvider, $locationProvider, PARTIALPATH) {
                    $stateProvider
                            .state('cardOperators', {
                                url: "/cardOperators",
                                views: {
                                    'main': {
                                        templateUrl: PARTIALPATH.cardOperators + 'index.html',
                                        controller: 'CardOperatorsIndexController'
                                    }
                                }
                            })
                            .state('cardOperators.edit', {
                                url: "/cardOperators/{cardOperatorId:[0-9]{1,6}}",
                                views: {
                                    'edit': {
                                        templateUrl: PARTIALPATH.cardOperators + 'edit.html',
                                        controller: 'CardOperatorsEditController',
                                        resolve: {
                                            cardOperator: ['$stateParams', 'UtilsService', 'CardOperatorsDataService', function ($stateParams, UtilsService, CardOperatorsDataService) {
                                                    var data = CardOperatorsDataService.getData();
                                                    var cardOperator = null;

                                                    if (!$stateParams.cardOperatorId)
                                                        return{}

                                                    cardOperator = UtilsService.findById(data, $stateParams.cardOperatorId);

                                                    if (cardOperator) {
                                                        return cardOperator;
                                                    } else {
                                                        var paramsEdit = {cardOperatorId: $stateParams.cardOperatorId}
                                                        var getDataPromise = null;

                                                        getDataPromise = CardOperatorsDataService.getCardOperator({filter: paramsEdit})

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
                            .state('cardOperators.add', {
                                url: "/add",
                                views: {
                                    'edit': {
                                        templateUrl: PARTIALPATH.cardOperators + 'edit.html',
                                        controller: 'CardOperatorsEditController',
                                        resolve: {
                                            cardOperator: function () {
                                                return {};
                                            }
                                        }
                                    }
                                }
                            })
                }
            ])
            .controller('CardOperatorsIndexController',
                    ['$scope', '$state', '$stateParams', '$injector', 'PARTIALPATH', 'MessageBox', 'CardOperatorsDataService',
                        function CardOperatorsIndexController($scope, $state, $stateParams, $injector, PARTIALPATH, MessageBox, CardOperatorsDataService) {
                            ngTableParams = $injector.get('ngTableParams');
                            UtilsService = $injector.get('UtilsService');
                            $scope.partials = PARTIALPATH.base;

                            $scope.tableParams = new ngTableParams(
                                    {
                                        page: 1,
                                        count: 10,
                                        sorting: {chrOperator: 'ASC'}
                                    },
                            {
                                total: 0,
                                getData: function ($defer, params) {
                                    var postParams = UtilsService.createNgTablePostParams(params);

                                    CardOperatorsDataService.getCardOperator(postParams)
                                            .then(function (response) {
                                                $scope.isLoading = false;
                                                var data = response.data;

                                                params.total(data.meta.totalRecords);
                                                $defer.resolve(data.data);

                                                $scope.selectedRowId = null;
                                                $scope.selRow = null;
                                                CardOperatorsDataService.setData(data.data);
                                            });
                                }
                            });
                            $scope.updateSelection = function (cardOperator) {
                                var data = $scope.tableParams.data;
                                for (var i = 0; i < data.length; i++) {
                                    if (data[i].id != cardOperator.id)
                                        data[i].$selected = false;
                                    else
                                        data[i].$selected = true;
                                }
                                $scope.selectedRowId = cardOperator.id;
                                $scope.selRow = cardOperator;
                            }

                            $scope.goEdit = function () {
                                if ($scope.selRow)
                                    $state.go("cardOperators.edit", {cardOperatorId: $scope.selRow.id});
                                else
                                    MessageBox.show("Para editar, es necesario seleccionar primero un registro.");
                            }

                            $scope.goAdd = function () {
                                $state.go('cardOperators.add', $stateParams);
                            }

                            $scope.delete = function (obCardOperator) {
                                if (obCardOperator && obCardOperator.id) {
                                    MessageBox.confirm("¿Estás seguro de eliminar el registro seleccionado?", "Eliminar").then(function (result) {
                                        $scope.loading = true;
                                        CardOperatorsDataService.delete({cardOperatorId: obCardOperator.id}, {alertOnSuccess: true})
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
                                {name: 'chrOperator', type: 'text', label: 'Operador'}
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
            .controller('CardOperatorsEditController',
                    ['$scope', '$timeout', '$http', '$state', '$stateParams', '$filter', 'PATH', 'PARTIALPATH', 'MessageBox', 'CatalogService', 'CardOperatorsDataService', 'cardOperator',
                        function CardOperatorsEditController($scope, $timeout, $http, $state, $stateParams, $filter, PATH, PARTIALPATH, MessageBox, CatalogService, CardOperatorsDataService, cardOperator) {
                            $scope.cardOperator = cardOperator;

                            $scope.back = function () {
                                $state.go('^', $stateParams);
                            }
                            $scope.data = [{id: 1, chrOperator: 'Name1'}, {id: 2, chrOperator: 'Name2'}];

                            $scope.save = function () {
                                var form_data = new FormData();

                                form_data.append('cardOperatorId', $scope.cardOperator.id);
                                form_data.append('chrOperator', $scope.cardOperator.chrOperator);

                                $scope.loading = true;
                                $http.post(
                                        PATH.cardOperators + 'save',
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
                            }
                            $scope.getFormFieldCssClass = function (ngModelController) {
                                if (ngModelController.$pristine)
                                    return "";
                                return ngModelController.$valid ? "has-success" : "has-error";
                            }

                        }]
                    )
})();