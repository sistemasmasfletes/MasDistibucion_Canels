//pantalla puntos de la ruta
function RouteSummaryEditController($rootScope, $scope, $timeout, $state, $stateParams, ngTableParams, PARTIALPATH, ModalService, RouteSummaryDataService, UtilsService, CONFIG) {
    var barcode = "";
    var comp = false;

    $scope.isLoading = false;
    $scope.selScheduledDate = {};
    $scope.partials = CONFIG.PARTIALS;
    //$scope.patpart = CONFIG.PATH+CONFIG.JS+'dist/webqr.js';
    $scope.tableParams = new ngTableParams(
            {page: 1,
                count: 10,
                sorting: {
                    start_date: 'desc'
                }
            },
    {
        total: 0,
        getData: function ($defer, params) {
            var idPoint = $stateParams.id;
            var postParams = {page: params.page(), rowsPerPage: params.count(), stateParams: $stateParams, idrow: idPoint};
             var filter=params.filter();
             var sorting = params.sorting();
             var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];
             
             if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
             if(filter) angular.extend(postParams,{filter:filter});

            RouteSummaryDataService.getRouteSummaryPoints1(postParams)
                    .then(function (response) {
                        var data = response.data;

                        $scope.isLoading = false;

                        params.total($stateParams);
                        $defer.resolve(data.data);

                        if (data.meta.totalRecords == 0 || data.data[0].HoraActual != null ) {//SI LA RUTA NO TIENE PUNTOS PROGRAMADOS O TODOS LOS PUNTOS TIENEN UN VALOR EN EL DATO HORA ACTUAL LA RUTA ES FINALIZADA AUTOMATICAMENTE
                            var modalOptions3 = {
                                actionButtonText: 'Aceptar',
                                bodyText: '¡Ruta finalizada con éxito!'
                            };
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions3).then(function (result) {
                                RouteSummaryDataService.saveEndHourRoute($stateParams).then(function(){
                                    $scope.$parent.tableParams.reload().then(function(){$state.go('routeSummary');});//NAVEGA A PANTALLA DE RUTAS
                                })
                            });
                        }

                    });
        }
    }

    );

    $scope.changeSelection = function (schedule) {
        var data = $scope.tableParams.data;
        for (var i = 0; i < data.length; i++) {
            if (data[i].id != schedule.id)
                data[i].$selected = false;
        }
        $scope.selScheduledDate = schedule;
    }

    $scope.go = function (schedule) { //Pantalla ver actividad
        if (schedule)
            var $scheduleRouteId = schedule.Identificador;
        var $routePoint_id = schedule.routePoint_id;
        var $activity = schedule.validar;

        if ($activity >= 1) {
            $state.go('routeSummary.activity', {id2: $scheduleRouteId, id1: $routePoint_id}, {location:false});
        }
        else {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Punto de venta sin actividades.'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
            });
        }

    }

    $scope.hoverTrIn = function () {
        this.hoverGoSchedule = true;
    }

    $scope.hoverTrOut = function () {
        this.hoverGoSchedule = false;
    }

    //--------------------------------------------------------------------------


    function alerta2() {
        var data2 = $scope.tableParams.data; //objetos de scope

        var hp = data2[0].Formato; //obtener la fecha programada en DB del objeto scope
        var d1 = new Date(hp); //convertir fecha programada de tipo string a tipo date
        var c1 = d1.getHours(); //obteniendo hora de fecha programada de scope

        var d2 = new Date(); //fecha actual
        var c2 = d2.getHours(); //obtener la hora de fecha actual

        var dif; //variable donde se almacena la diferencia de hora programada vs hora actual
        var q; //variable donde se almacena la conversion de tipo string a tipo date de la variable dif

        if (data2[0].HoraActual != null) {
            clearInterval(alerta);
        } else
        if (c2 < c1) {


            dif = d1 - d2;
            dif = 1000 * Math.round(dif / 1000); //formula matematica para la comparación de dos horas
            q = new Date(dif);
            var h = q.getUTCHours();
            var m = q.getUTCMinutes();
            var s = q.getUTCSeconds();

            if (h < 10) {
                h = '0' + h
            }
            if (m < 10) {
                m = '0' + m
            }
            if (s < 10) {
                s = '0' + s
            }

            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: "El punto de venta: " + data2[0].Nombre + " está disponible en " + h + ":" + m + ":" + s + " horas"
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
            });

        } else {
            dif = d2 - d1;
            dif = 1000 * Math.round(dif / 1000);
            q = new Date(dif);
            var h2 = q.getUTCHours();
            var m2 = q.getUTCMinutes();
            var s2 = q.getUTCSeconds();

            if (h2 < 10) {
                h2 = '0' + h2
            }
            if (m2 < 10) {
                m2 = '0' + m2
            }
            if (s2 < 10) {
                s2 = '0' + s2
            }
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: "El punto de venta: " + data2[0].Nombre + " tiene un retrazo de " + h2 + ":" + m2 + ":" + s2 + " horas"
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
            });
        }

    }


    /*---------------------Alerta de tiempos----------------------*/
    var alerta = setTimeout(function () {
        alerta2();
    }, 600000);
    //120000 = 02 minutos
    //600000 = 10 minutos
    //1200000= 20 minutos
    /*---------------------Alerta de tiempos----------------------*/

   

    $scope.regresar = function () {
        $state.go('routeSummary'); //NAVEGA A PANTALLA DE RUTAS
    }

    $scope.onSuccess = function (/*code*/) {
        var data = $scope.tableParams.data;
        $scope.code = angular.element("#mivalor").val();
        var validar = data[0].validar;
        var pointCode = data[0].Codigo;
        var scheduleRouteId = $stateParams.id;
        var routePoint_id = data[0].routePoint_id;

        var progress = 0;

        if (data[0].Progreso != null) {
            progress = data[0].Progreso;
        }

        if (pointCode == $scope.code) {
            if (validar > 0) {
                var snd = new Audio("../images/sound_alert.wav");
                snd.play();
                var progPunto = (1 / data.length) * 100;
                data[0].progress = progPunto + progress;
                RouteSummaryDataService.saveProgress(data[0]);
                $state.go('routeSummary.add', {id2: scheduleRouteId, id: routePoint_id});
            } else {
                var alert = new Audio("../images/sound_alert.wav");
                alert.play();
                var progPunto2 = (1 / data.length) * 100;
                data[0].progress = progPunto2 + progress;
                RouteSummaryDataService.saveProgress(data[0]);
                RouteSummaryDataService.savePointEvidence(data[0]);
                $scope.tableParams.reload();
                var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: 'Punto sin actividades, visitar siguiente punto en la ruta.'
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                    $scope.tableParams.reload();
                });
                
            }
        } else {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'El punto al que esta accesando no es el siguiente en la ruta.'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
            });
        }
        clearInterval(alerta);
    };

    $scope.onError = function (error) {
        $scope.error = error;
    };
    $scope.onVideoError = function (error) {
        $scope.error = error;
    };

    $scope.toggleFilter = function (params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.customFilter = [
        {name: 'Nombre', type: 'text', label: 'Nombre'},
        {name: 'Codigo', type: 'text', label: 'Código'} 
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function () {
        $scope.filterOpen = true;
    }

    $scope.appFilter = function (filter) {
        $scope.tableParams.settings().filterDelay = 0;
        $scope.tableParams.$params.filter = filter;
    }
    
    $scope.showModal = function(){
        $scope.nuevoMiembro = {};
        var modalInstance = ModalService.showModal({
            templateUrl: PARTIALPATH.routeSummary + 'modal.html'
        })
    }
    
    $scope.showRoutePointActivities=function(schedule){
        return ModalService.showModal(
            {   templateUrl: 'templateShowRoutePointActivities.html'/* plantilla en partials/routeSummary/edit.html */,
                controller:'ShowRoutePointActivitiesController',size:'xl',
                keyboard:false,
                resolve:{
                    schedule: function(){ return schedule}
                }
            },
            {});
    }
    
    ShowRoutePointActivitiesController = function($scope, $modalInstance,$stateParams,schedule){
        $scope.isLoading=false;
        $scope.partials = CONFIG.PARTIALS;
        var scheduleId= $stateParams.id2;    
        $scope.tableParams = new ngTableParams(
            {   page:1,
                count:10,
                sorting:{
                    Paquete:'desc'
                }
            },
            {
                total:0,
                getData:function($defer,params){
                    var idPack= $stateParams.id1; //routePointId
                    var schedId = $stateParams.id2; //scheduleRouteId
                    var oc = $stateParams.id4; //orden de compra
                    var rpaId = $stateParams.id3; //routePointActivityId
                    
                    $stateParams.id1=schedule.routePoint_id;
                     
                    var postParams = {page:params.page(), rowsPerPage:params.count(),stateParams:$stateParams,idrow:idPack,idrow2:schedId,idrow3:oc,idrow4:rpaId};
                    var filter=params.filter();
                    var sorting = params.sorting();
                    var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                    if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                    if(filter) angular.extend(postParams,{filter:filter});

                    RouteSummaryDataService.getActivityPackage(postParams)
                    .then(function(response){
                        var data=response.data;
                        $scope.isLoading=false;
                        params.total(data.meta.totalRecords);
                        $defer.resolve(data.data);
                    });       
                }
            }

        );

        $scope.changeSelection = function(pack) {
            var data = $scope.tableParams.data;        
            for(var i=0;i<data.length;i++){
                if(data[i].id!=pack.id)
                    data[i].$selected=false;
            }

        }

        $scope.regresar=function(){
            $state.go('routeSummary.edit',{id:scheduleId}); //NAVEGA A PANTALLA DE PUNTOS DE LA RUTA
        }

        $scope.toggleFilter = function (params) {
            params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
        }

        $scope.customFilter = [
            {name: 'Paquete', type: 'text', label: 'Paquete'},
            {name: 'Actividad', type: 'text', label: 'Actividad'} 
        ]

        $scope.filterOpen = false;
        $scope.openFilter = function () {
            $scope.filterOpen = true;
        }

        $scope.appFilter = function(filter) {
            $scope.tableParams.settings().filterDelay = 0;
            $scope.tableParams.$params.filter = filter;
        }
        
    
        $scope.ok = function (result) {
            $modalInstance.close();       
        };
    }
}