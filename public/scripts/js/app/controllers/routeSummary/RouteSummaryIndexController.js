//Pantalla de rutas asignadas
function RouteSummaryIndexController($rootScope, $scope, $timeout, $state, $stateParams, ngTableParams, PARTIALPATH, ModalService,MessageBox, RouteSummaryDataService, UtilsService, CONFIG) {
    $scope.Quagga = {};
    $scope.grid = {};
    $scope.selScheduledDate = {};
    $scope.isLoading = false;
    $scope.partials = CONFIG.PARTIALS;
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
            var postParams = UtilsService.createNgTablePostParams(params);
            RouteSummaryDataService.getRouteSummary(postParams)
                    .then(function (response) {
                        var data = response.data;
                        $scope.isLoading = false;
                        params.total(data.meta.totalRecords);
                        $defer.resolve(data.data);
                        setTimeout(function () {
                            alerta1();
                        }, 2000); //sólo aparece al inicio
                    });
        }
    }

    );

    setInterval(function () {
        reloj()
    }, 1000); //reloj en pantalla de rutas

    //--------------------------- primer alerta --------------------------------
    function alerta1() {
        var data2 = $scope.tableParams.data;
        var hp = data2[0].Formato;
        var d1 = new Date(hp);
        var c1 = d1.getHours();

        var d2 = new Date();
        var c2 = d2.getHours();

        var dif;
        var q;

        if (c2 < c1) {
            //alert("programada "+d1);
            //alert("actual "+d2);
            dif = d1 - d2;
            dif = 1000 * Math.round(dif / 1000);
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
                bodyText: "Tu recorrido inicia en: " + h + ":" + m + ":" + s + " horas"
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
            });
            //alert("Tu recorrido inicia en: "+h+":"+m+":"+s+" horas");
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------- Segunda alerta -------------------------------
    function alerta2() {
        var data2 = $scope.tableParams.data; //objetos de scope
        for (var i = 0; i < data2.length; i++) {
            var hp = data2[i].Formato; //obtener la fecha programada en DB del objeto scope
            var d1 = new Date(hp); //convertir fecha programada de tipo string a tipo date
            var c1 = d1.getUTCHours(); //obteniendo hora de fecha programada de scope

            var d2 = new Date(); //fecha actual
            var c2 = d2.getUTCHours(); //obtener la hora de fecha actual

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
                    bodyText: "La ruta: " + data2[0].Nombre + " Inicia en " + h + ":" + m + ":" + s + " horas"
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                });
                //alert("La ruta: "+data2[0].Nombre+" Inicia en "+h+":"+m+":"+s+" horas");
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
                    bodyText: "La ruta: " + data2[0].Nombre + " tiene un retrazo de " + h2 + ":" + m2 + ":" + s2 + " horas"
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
                });
                //alert("La ruta: "+data2[0].Nombre+" tiene un retrazo de "+h2+":"+m2+":"+s2+" horas");
            }
            comp = true;
        }
    }



    /*---------------------Alerta de tiempos----------------------*/

    var alerta = setInterval(function (){alerta2();},600000); //aparece a cada diez minutos y desaparece al iniciar la ruta
    //60000  = 01 minuto
    //120000 = 02 minutos
    //600000 = 10 minutos
    //1200000= 20 minutos
    /*---------------------Alerta de tiempos----------------------*/

    $scope.changeSelection = function (schedule) {
        var data = $scope.tableParams.data;
        for (var i = 0; i < data.length; i++) {
            if (data[i].id != schedule.id)
                data[i].$selected = false;
        }
        $scope.selScheduledDate = schedule;
    }

    $scope.go = function (schedule) {
        var data = $scope.tableParams.data;
        var $id = schedule.id;
        var endRoute = schedule.endDate;
        var $driver = schedule.driver;
        var monthNames = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];

        var dayNames = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
        ];

        var currentDate = new Date();
        var currentDay = parseInt(currentDate.getDate()) < 10 ? '0' + currentDate.getDate().toString(): currentDate.getDate().toString() ;
        var currentMonth = monthNames[currentDate.getMonth()];
        var currentYear = currentDate.getFullYear();
        
        var currentDateInString =  currentDay + '-' + currentMonth + '-' + currentYear;     
        if(!$id){
            MessageBox.show('Debe seleccionar primero una ruta.');
            return;
        }
        if(schedule.Estado==2){
            MessageBox.show('La ruta ya está finalizada.');
            return;
        }
        if (2!=2/*schedule.Fecha.replace(/\s/g, '') != currentDateInString*/) {
            var modalOptions4 = {
                actionButtonTex: 'Aceptar',
                bodyText: "La fecha actual no coincide con\n\n\
                la fecha de inicio de la ruta seleccionada."
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions4).then(function (result) {
            });
        } else if (2==2/*data[0].Estado == null && data[0] == schedule*/) {
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Bienvenido ' + $driver + '!'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                RouteSummaryDataService.saveCurrentHour(schedule).then(function(){
                    $state.go('routeSummary.edit', {id: $id}); //ir a pantalla de puntos
                })
            });
            clearInterval(alerta);
            //clearTimeout(alerta1);            
            
        } else if (data[0].Estado == 1 && data[0] == schedule) {
            $state.go('routeSummary.edit', {id: $id}); //ir a pantalla de puntos
        } else if (schedule.Estado == 2) {
            var modalOptions2 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Esta ruta ya ha sido finalizada.'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result) {
            });
        } else {
            var modalOptions3 = {
                actionButtonText: 'Aceptar',
                bodyText: '¡Está no es tu ruta en proceso!\n\
                Ingresa a la ruta que se encuentra en proceso \n\
                o inicia tu primer recorrido.'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions3).then(function (result) {
            });
        }
    }

    $scope.goPacks = function (schedule) { //Pantalla ver actividad
        if (schedule)
            var $scheduleRouteId = schedule.id;
        $state.go('routeSummary.routeActivity', {id: $scheduleRouteId});
    }

    $scope.hoverTrIn = function () {
        this.hoverGoSchedule = true;
    }

    $scope.hoverTrOut = function () {
        this.hoverGoSchedule = false;
    }

    function reloj() {
        var r = new Date();
        document.getElementById("clock").innerHTML = r.toLocaleTimeString();
    }

    $scope.toggleFilter = function (params) {
        params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
    }

    $scope.customFilter = [
        {name: 'Fecha', type: 'date', label: 'Fecha'},
        {name: 'Nombre', type: 'text', label: 'Nombre de la ruta'},
        {name: 'Vehiculo', type: 'text', label: 'Vehículo'}
    ]

    $scope.filterOpen = false;
    $scope.openFilter = function () {
        $scope.filterOpen = true;
    }

    $scope.appFilter = function(filter) {
        $scope.tableParams.settings().filterDelay = 0;
        $scope.tableParams.$params.filter = filter;
    }
}