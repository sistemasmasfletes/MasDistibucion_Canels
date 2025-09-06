function WarehousemanActivityController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,WarehousemanDataService,UtilsService,CONFIG)
{
    var modalInfoPath = PARTIALPATH.modalInfo;
    var $scheduleRouteId = $stateParams.scheduleRouteId;
    var $routePointId = $stateParams.routePointId
    var $routeId = $stateParams.routeId;
    $scope.grid = {};
    $scope.grid.edit = function(){return grdEdit($scope.id, $scope.status, $scope.schedule)};
    $scope.selScheduledDate = {};
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.tableParams = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                start_date:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var postParams = {page:params.page(), rowsPerPage:params.count(),scheduleRouteId:$scheduleRouteId,routePointId:$routePointId};
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                WarehousemanDataService.getWarehousemanActivity(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    //params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                });       
            }
        }

    );
    
    $scope.changeSelection = function(schedule) {
        var data = $scope.tableParams.data;
        for(var i=0;i<data.length;i++){
            if(data[i].id!=schedule.id)
                data[i].$selected=false;
        }
        $scope.id = schedule.id;
        $scope.status = schedule.status;
        $scope.schedule = schedule;
    }
    
    $scope.back=function(){
        $state.go('warehouseman'); //NAVEGA A PANTALLA DE PUNTOS DE LA RUTA
    }
    
    function grdEdit(id, status, schedule){
        if(id){
            if(status == null){                
                //WarehousemanDataService.save(schedule);
                $state.go('warehouseman.form',{scheduleRouteId:$scheduleRouteId,routePointId:$routePointId,routePointActivityId:id});
                /*var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: '¡Transferencia realizada con éxito!'
                }
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
                $scope.tableParams.reload();*/
            } else {
                var modalOptions2 = {
                    actionButtonText: 'Aceptar',
                    bodyText: '¡Este paquete ya ha sido transferido!'
                };
                ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
            }
            //$state.go('warehouseman.form',{scheduleRouteId:$scheduleRouteId,routePointId:$routePointId,routePointActivityId:id});
            //alert(status);
        }
        else{
            var modalOptions3 = {
                actionButtonText: 'Aceptar',
                bodyText: 'Para realizar una trasferencia es necesario seleccionar primero un registro.'
            };
            ModalService.showModal({templateUrl: modalInfoPath}, modalOptions3);
        }
    }
    
    $scope.onSuccess = function() {
    	
        //$scope.code = code;
        $scope.code = document.getElementById("mivalor").value;
        var data = $scope.tableParams.data;
    
        alert(data[0].toSource());
        for(var i=0;i<data.length;i++){
            var status = data[i].status;
			var orderId = data[i].orderId;
            
            if(orderId==$scope.code){
                if(status==null){
                    //WarehousemanDataService.save(data[i]);
					$state.go('warehouseman.form',{scheduleRouteId:$scheduleRouteId,routePointId:$routePointId,routePointActivityId:id});
					var modalOptions = {
						actionButtonText: 'Aceptar',
						bodyText: '¡Transferencia realizada con éxito!'
					}
					ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
					$scope.tableParams.reload();
                } else {
                    var modalOptions2 = {
						actionButtonText: 'Aceptar',
						bodyText: '¡Este paquete ya ha sido transferido!'
					};
					ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
                }
            }
        }
    };
    $scope.onError = function(error) {
        $scope.error = error;
    };
    $scope.onVideoError = function(error) {
        $scope.error = error;
    };
    
/* ---------------------------------------------- SCANNER --------------------------------------------    *

    $(function() {
        var resultCollector = Quagga.ResultCollector.create({
            capture: true,
            capacity: 20,
            blacklist: [{code: "3574660239843", format: "ean_13"}],
            filter: function(codeResult) {
                return true;
            }
        });
        var App = {
            init : function() {
                var self = this;

                Quagga.init(this.state, function(err) {
                    if (err) {
                        return self.handleError(err);
                    }
                    Quagga.registerResultCollector(resultCollector);
                    App.attachListeners();
                    Quagga.start();
                });
            },
            handleError: function(err) {
                console.log(err);
            },
            attachListeners: function() {
                var self = this;

                $(".controls").on("click", "button.stop", function(e) {
                    e.preventDefault();
                    Quagga.stop();
                    self._printCollectedResults();
                });

                $(".controls .reader-config-group").on("change", "input, select", function(e) {
                    e.preventDefault();
                    var $target = $(e.target),
                        value = $target.attr("type") === "checkbox" ? $target.prop("checked") : $target.val(),
                        name = $target.attr("name"),
                        state = self._convertNameToState(name);

                    console.log("Value of "+ state + " changed to " + value);
                    self.setState(state, value);
                });
            },
            _printCollectedResults: function() {
                var results = resultCollector.getResults(),
                    $ul = $("#result_strip ul.collector");


                    var barcode = $(".code");

                results.forEach(function(result) {
                    var $li = $('<li><div class="thumbnail"><div class="imgWrapper"><img /></div><div class="caption"><h4 class="code"></h4></div></div></li>');

                    $li.find("img").attr("src", result.frame);
                    $li.find("h4.code").html(result.codeResult.code + " (" + result.codeResult.format + ")");
                    $ul.prepend($li);
                });
            },
            _accessByPath: function(obj, path, val) {
                var parts = path.split('.'),
                    depth = parts.length,
                    setter = (typeof val !== "undefined") ? true : false;

                return parts.reduce(function(o, key, i) {
                    if (setter && (i + 1) === depth) {
                        o[key] = val;
                    }
                    return key in o ? o[key] : {};
                }, obj);
            },
            _convertNameToState: function(name) {
                return name.replace("_", ".").split("-").reduce(function(result, value) {
                    return result + value.charAt(0).toUpperCase() + value.substring(1);
                });
            },
            detachListeners: function() {
                $(".controls").off("click", "button.stop");
                $(".controls .reader-config-group").off("change", "input, select");
            },
            setState: function(path, value) {
                var self = this;

                if (typeof self._accessByPath(self.inputMapper, path) === "function") {
                    value = self._accessByPath(self.inputMapper, path)(value);
                }

                self._accessByPath(self.state, path, value);

                console.log(JSON.stringify(self.state));
                App.detachListeners();
                Quagga.stop();
                App.init();
            },
            inputMapper: {
                inputStream: {
                    constraints: function(value){
                        var values = value.split('x');
                        return {
                            width: parseInt(values[0]),
                            height: parseInt(values[1]),
                            facing: "environment"
                        }
                    }
                },
                numOfWorkers: function(value) {
                    return parseInt(value);
                },
                decoder: {
                    readers: function(value) {
                        return [value + "_reader"];
                    }
                }
            },
            state: {
                inputStream: {
                    type : "LiveStream",
                    constraints: {
                        width: 320,
                        height: 240,
                        facing: "environment" // or user
                    }
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: 4,
                decoder: {
                    readers : ["code_128_reader", "ean_reader"]
                },
                locate: true
            },
            lastResult : null
        };

        App.init();

        Quagga.onProcessed(function(result) {
            var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

            if (result) {
                if (result.boxes) {
                    drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                    result.boxes.filter(function (box) {
                        return box !== result.box;
                    }).forEach(function (box) {
                        Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                    });
                }

                if (result.box) {
                    Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
                }

                if (result.codeResult && result.codeResult.code) {
                    Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
                }
            }
        });

        Quagga.onDetected(function(result) {
            var code = result.codeResult.code;
            var data = $scope.tableParams.data;
            

            if (App.lastResult !== code) {
                App.lastResult = code;
                var $node = null, canvas = Quagga.canvas.dom.image;

                $node = $('<li><div class="thumbnail"><div class="imgWrapper"><img /></div><div class="caption"><h4 class="code"></h4></div></div></li>');
                $node.find("img").attr("src", canvas.toDataURL());
                $node.find("h4.code").html(code);
                $("#result_strip ul.thumbnails").prepend($node);
                
                for(var i=0;i<data.length;i++){
                    var status = data[i].status;
                    var orderId = data[i].orderId;
                    
                    if(orderId == code){
                        if(status == null){                
                            WarehousemanDataService.save(data[i]);
                            //$state.go('warehouseman.form',{scheduleRouteId:$scheduleRouteId,routePointId:$routePointId,routePointActivityId:id});
                            var modalOptions = {
                                actionButtonText: 'Aceptar',
                                bodyText: '¡Transferencia realizada con éxito!'
                            }
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
                            $scope.tableParams.reload();
                        } else {
                            var modalOptions2 = {
                                actionButtonText: 'Aceptar',
                                bodyText: '¡Este paquete ya ha sido transferido!'
                            };
                            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
                        }
                    } else {
                        var modalOptions3 = {
                            actionButtonText: 'Aceptar',
                            bodyText: 'Para realizar una trasferencia es necesario seleccionar primero un registro.'
                        };
                        ModalService.showModal({templateUrl: modalInfoPath}, modalOptions3);
                    }
                }

            }
        });
    });
/ ---------------------------------------------- SCANNER ----------------------------------------------------*/
}