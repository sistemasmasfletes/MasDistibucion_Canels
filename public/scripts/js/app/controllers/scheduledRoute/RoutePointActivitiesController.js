function RoutePointActivitiesController($scope, $timeout, $state, $stateParams, ngTableParams, ModalService, ActivityReportDataService, UtilsService, CONFIG) {
    $scope.isLoading = false;    $scope.urlbase = path;
    $scope.partials = CONFIG.PARTIALS;
    $scope.currentData = [];
    $scope.selectedPackage = {};
    $scope.tableParams = new ngTableParams(
            {page: 1,
                count: 50,
                sorting: {
                    "tr.transaction_id": 'asc'
                }
            },
    {
        groupBy: 'originDestiny',
        total: 0,
        getData: function ($defer, params) {
            var postParams = UtilsService.createNgTablePostParams(params, {scheduledRouteId: $stateParams.scheduledRouteId, routePointId: $stateParams.routePointId});

            ActivityReportDataService.getRoutePointActivity(postParams)
                    .then(function (response) {
                        $scope.currentData = response.data;
                        $scope.isLoading = false;
                        params.total($scope.currentData.meta.totalRecords);
                        $defer.resolve($scope.currentData.data[0]);

                    });
        }
    }

    );

    $scope.changeSelection = function (package) {
        var data = $scope.tableParams.data;
        for (var i = 0; i < data.length; i++) {
            if (data[i].id != package.id)
                data[i].$selected = false;
        }
        if (package.$selected)
            $scope.selectedPackage = package;
        else
            $scope.selectedPackage = {};
    }


    $scope.toggleFilter = function (params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }


    $scope.back = function () {
        $state.go('^', $stateParams)
    };

    $scope.stopPackage = function () {
        if ($scope.selectedPackage.id) {
            var postParams = {routePointActivityId: $scope.selectedPackage.routePointActivityId,transaction_id: $scope.selectedPackage.transaction_id};
            ActivityReportDataService.stopPackage(postParams)
                    .then(function (response) {
                        //$state.reload();
                        $state.go($state.current, {}, {reload: true});
                    })
        } else {
            alert("Para poder detener un paquete, debe seleccionarlo primero.");
        }
    }

    $scope.customFilter = [
        {name: 'iniDate', type: 'date', label: 'Fecha inicial'},
        {name: 'endDate', type: 'date', label: 'Fecha final'},
        {name: 'routeName', type: 'text', label: 'Ruta'},
        {name: 'driverName', type: 'text', label: 'Conductor'},
        {name: 'vehicleName', type: 'text', label: 'Vehículo'}
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