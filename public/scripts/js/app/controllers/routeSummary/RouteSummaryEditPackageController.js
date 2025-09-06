//Pantalla de actividades de paquetes en un punto
function RouteSummaryEditPackageController($rootScope,$scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,RouteSummaryDataService,UtilsService,CONFIG){

	$scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    var scheduleId= $stateParams.id2;

    RECHAZAR=1;
    NO_ENTREGAR=2;
    ENTREGAR=3;
    TIPO_COBRO_TOTAL = 1;
    TIPO_COBRO_50_POR_CIENTO = 2;
   
    STATUS_PROMOCION_ENTREGADO = 3;
    STATUS_PROMOCION_RECHAZADO = 6;
    STATUS_PROMOCION_NO_ENTREGADO = 7;


    RouteSummaryDataService.getCountPacks($stateParams)
    .then(function(response){
        var data=response.data;
        if(data.meta.totalRecords == 0){
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Operación finalizada, pasar al siguiente punto en la ruta.'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){
                 $state.go('routeSummary.edit',{id:scheduleId}); //NAVEGA A PANTALLA DE PUNTOS DE LA RUTA
            });           
        }

    });
    
    

    checkIsDone = function(){
        var data = $scope.tableParams.data;
        var count=0;
        for(var i=0;i<data.length;i++){
            if(data[i].Estado!=null)
                count++;
        }
        if(count==data.length){
            var modalOptions = {
                actionButtonText: 'Aceptar',
                bodyText: 'Operación finalizada, pasar al siguiente punto en la ruta.'
            };
            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){
                $state.go('routeSummary.edit',{id:scheduleId});
            });
        }

    }
    


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
                var idPack= $stateParams.id; //routePointId
                var schedId = $stateParams.id2; //scheduleRouteId
                var oc = $stateParams.id4; //orden de compra
                var rpaId = $stateParams.id3; //routePointActivityId

                var postParams = {page:params.page(), rowsPerPage:params.count(),stateParams:$stateParams,idrow:idPack,idrow2:schedId,idrow3:oc,idrow4:rpaId};
                var filter=params.filter();
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                if(filter) angular.extend(postParams,{filter:filter});

                RouteSummaryDataService.getRouteSummaryPackage(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                    $scope.poType = data.data[0].pointType;
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
    
    $scope.onSuccess = function(/*code*/) {
        var code = angular.element("#mivalor").val();

    	if($scope.code != code){    		
	        $scope.code = code;
	        var data = $scope.tableParams.data;
	        var hasPromotion = false;
	        for(var i=0;i<data.length;i++){
	            var $scheduleId = data[i].scheduleId;
	            var $routePointId = data[i].routePoint_id;
	            var $id = data[i].id; //Orden de compra
	            var $rpa_id = data[i].routePointActivityId
	            var $hour = data[i].horaReal;
	            var packcode = data[i].id; //Orden de compra
	            var $pointType = data[i].pointType;
	            var $status = data[i].Estado;
	            var flag = false;
	            
	            if(packcode == $scope.code){
	                if($status === null && $pointType === 1){
                            RouteSummaryDataService.saveHourPoint(data[i]);	                    
                            $state.go('routeSummary.view',{scheId:$scheduleId,point:$routePointId,id:$id,rpaId:$rpa_id}); //NAVEGA A PANTALLA DE SALVAR EVIDENCIA
	                } else if($status === null && $pointType === 2) {
	                    if($hour === null){
	                        $scope.tableParams.reload();
	                        RouteSummaryDataService.saveHourPoint(data[i]);
	                        RouteSummaryDataService.saveEvidenceCI(data[i]);
	                        $scope.tableParams.reload();
	                        var snd = new Audio("../images/sound_alert.wav");
	                        snd.play();
	                        $scope.tableParams.reload();
	                    }
	                } else{
	                    var modalOptions2 = {
	                        actionButtonText: 'Aceptar',
	                        bodyText: '¡Actividad realizada! Realizar la siguiente acción.'
	                    };
	                    ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){
                                $scope.tableParams.reload();
                            });
	                }
                    break;
	            }
	        }
    	}
    };
    $scope.onError = function(error) {
        console.log(error);
    };
    $scope.onVideoError = function(error) {
        console.log(error);
    };
    
    $scope.goCancel = function (pack) { //Cancelar el ruteo posterior y ajustar los creditos
        if (pack)
            var $OrderId = pack.id;
        
    	var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
				bodyText: 'Esta acción no se puede deshacer, ¿esta seguro de continuar?'
					};
    	ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {

            RouteSummaryDataService.packNoFound(pack)
            .then(function(response){
            	if(response.data.res == true){
            		$scope.tableParams.reload();
            		var modalOptions2 = {
	                        actionButtonText: 'Aceptar',
	                        bodyText: 'Se han cancelado exitosamente las actividades referentes a la orden.'
	                };
            		ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions2).then(function (result){});
            		//alert($scope.tableParams.data.toSource())
            	}
            });
        });
        
    }

    $scope.rejectPromotion = function(pack){       
        if(pack){
            var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
                bodyText: 'Esta acción no se puede deshacer, ¿Esta seguro que desea rechazar la promoción?'
                    };
            
            ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {
                RouteSummaryDataService.updateStatusPromotion(
                    {   promotionScheduleId:pack.promotionscheduleid,
                        routePointActivityId:pack.routePointActivityId,
                        tipoCobro:TIPO_COBRO_TOTAL,
                        comentarios:(typeof pack.comments==='undefined' ? 'Promoción rechazada por el destinatario' : pack.comments),
                        status: STATUS_PROMOCION_RECHAZADO
                    }
                ).then(function(response){
                    $scope.tableParams.reload().then(function(){
                        checkIsDone();
                    })                    
                });
            });
        }
    }

    $scope.notDeliver = function(pack){
        if(pack){
            var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
                bodyText: 'Esta acción no se puede deshacer, ¿Esta seguro de proceder con la No entrega?'
                    };
            
            ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {
                RouteSummaryDataService.updateStatusPromotion(
                    {   promotionScheduleId:pack.promotionscheduleid,
                        routePointActivityId:pack.routePointActivityId,
                        tipoCobro:TIPO_COBRO_50_POR_CIENTO,
                        comentarios:pack.comments,
                        status: STATUS_PROMOCION_NO_ENTREGADO
                    }

                ).then(function(response){
                    $scope.tableParams.reload().then(function(){
                        checkIsDone();
                    })                    
                });
            });

        }
    }

    $scope.getPromotionResult=function(pack){
        RouteSummaryDataService.saveHourPoint(pack);
        var promotion=null;        
        $scope.openPromotion(pack).then(function(result){
            promotion = result.promotion;
            //console.log(result);return;
            switch(result.action){
                case RECHAZAR:
                    $scope.showNotDeliveryReason().then(function(result){
                        promotion = angular.extend({},promotion,{comments:result.comments});
                        $scope.rejectPromotion(promotion);
                    });
                    break;
                case NO_ENTREGAR:
                    $scope.showNotDeliveryReason().then(function(result){                        
                        promotion = angular.extend({},promotion,{comments:result.comments});
                        $scope.notDeliver(promotion);
                    });
                    break;
                case ENTREGAR:
                    var obActivityDetail = {status:STATUS_PROMOCION_ENTREGADO,
                            comentarios:'Evidencia automática de entrega de promoción',
                            routePointActivityId:promotion.routePointActivityId,
                            ttype:promotion.ttype,
                            transactionid:promotion.id
                        };

                    //Guardar evidencia de entrega
                    RouteSummaryDataService.save(obActivityDetail, {})
                    .success(function(data, status, headers, config){                        
                        //Actualizar estatus y balance
                        RouteSummaryDataService.updateStatusPromotion(
                            {   promotionScheduleId:promotion.promotionscheduleid,
                                routePointActivityId:promotion.routePointActivityId,
                                tipoCobro:TIPO_COBRO_TOTAL,
                                comentarios:null,
                                status: STATUS_PROMOCION_ENTREGADO
                            }
                        ).then(function(response){                          
                            $scope.showSurvey(pack).then(function(promotion){                    
                                $scope.tableParams.reload().then(function(){
                                    checkIsDone();
                                });
                            });
                        });                        
                    });
                    break;
            } 
        });
    }
    
    $scope.openPromotion=function(pack){
        if(pack.numfiles==1)
            return $scope.showResource(pack);
        else
            return $scope.openPromotionWindow(pack);
    }

    // **************** Código de la ventana emergente de promociones ****************
    $scope.openPromotionWindow = function(pack){
        return ModalService.showModal(
            {   templateUrl: 'templateShowPromotion.html'/* plantilla en partials/routeSummary/edit2.html */,
                controller:'ShowpromoController',size:'xl',
                keyboard:false,
                resolve:{
                    promotion:function(){
                        return pack;
                    },
                    showResourceFunc:function(){return $scope.showResource}
                }
            },
            {});
    }

    ShowpromoController = function($scope, $http,ModalService,$modalInstance,PromotionDataService,MessageBox,promotion,showResourceFunc){
        $scope.promotion = promotion;

        $scope.showResource = function(pack){
            showResourceFunc(angular.extend({},pack,{isLoadedFromPromoList:true})).then(function(result){
                checkPromotionShown(pack.id,$scope.resource);
            });
        }

        $scope.loaData = function(paramId){
            $scope.loadingDetail = true;
            PromotionDataService.getPromotionDetail({filter:{promotionId:paramId}})
            .then(function(response){
                var resourceData = response.data.data;
                $scope.resource = transformResourceArray(resourceData);
                $scope.loadingDetail = false;
            });
        }

        //Botones de la ventana emergente
        $scope.ok = function (result) {
            if(promotionShown($scope.resource))
                $modalInstance.close({promotion:result,action:ENTREGAR});
            else
                MessageBox.show("Quedan archivos por mostrar");
        };

        $scope.rejectPromotion = function (result) {
                $modalInstance.close({promotion:result,action:RECHAZAR});
        };

        $scope.notDeliver = function (result) {
                $modalInstance.close({promotion:result,action:NO_ENTREGAR});
        };

        promotionShown = function(rows){
            var num =0;
            for(var i=0;i<rows.length;i++){
                if(rows[i].checked)
                    num++;
            }
            return num==rows.length
        }

        checkPromotionShown = function(rowid,rows){
            for(var i=0;i<rows.length;i++){
                if(rows[i].id==rowid){
                    rows[i].checked=true;
                    break;
                }
            }
        }


        // Funciones utilería
        transformResourceArray = function(data){
            var ext, path,filename, uniqueNameChars=13, lastslash, lastdot
            for(var i=0;i<data.length;i++){
                data[i]["num"] = i+1;
                data[i]["checked"] = false;
                path = data[i].path;
                data[i]["filename"]="";                  
                if(path){
                    if(data[i].resourceType==1){
                        ext = path.substring(path.lastIndexOf("."));
                        lastslash = path.lastIndexOf("/");
                        lastdot = path.lastIndexOf(".");
                        filename = path.substring(lastslash+1+uniqueNameChars,lastdot);
                        filename = filename.substring(0,filename.length>25 ? 25 : filename.length);
                        filename+=(filename.length==25 ? '...' : '') + ext;
                        data[i]["filename"] = filename;
                    }else
                        data[i]["filename"] = path;
                }
            }
            return data;
        }

        $scope.loaData(promotion.promotionid);
    }

    // **************** Código de la ventana de mostrar recurso ****************
    $scope.showResource = function(pack){
        return ModalService.showModal(
            {   templateUrl: 'templateShowResource.html'/* plantilla en partials/routeSummary/edit2.html */,
                controller:'ShowResourceController',size:'xl',
                keyboard:false,
                resolve:{
                    resource:function(){
                        return pack;
                    }
                },
                windowClass: 'app-modal-window'
            },
            {});
    }

    ShowResourceController = function($scope, $modalInstance,resource){
        $scope.resource = resource;        
        //Botones de la ventana emergente
        $scope.ok = function (result) {
                $modalInstance.close({promotion:result,action:ENTREGAR});
        };

        $scope.rejectPromotion = function (result) {
                $modalInstance.close({promotion:result,action:RECHAZAR});
        };

        $scope.notDeliver = function (result) {
                $modalInstance.close({promotion:result,action:NO_ENTREGAR});
        };

    }

    // **************** Código de la ventana de cuestionario ****************
    $scope.showSurvey = function(pack){
        return ModalService.showModal(
            {   templateUrl: 'templateShowSurvey.html'/* plantilla en partials/routeSummary/edit2.html */,
                controller:'ShowSurveyController',size:'md',
                keyboard:false,
                resolve:{
                    promotion:function(){
                        return pack;
                    }
                }
            },
            {});
    }

    ShowSurveyController = function($scope, $modalInstance,CatalogService,PromotionScheduleDataService,MessageBox,promotion){
        $scope.promotionSchedule = {id:promotion.promotionscheduleid}
        $scope.select = {};
        $scope.getInterestLevel = CatalogService.getInterestLevel();
        $scope.getConsumerType = CatalogService.getConsumerType();
        $scope.getRequest = CatalogService.getRequest();

        //Botones de la ventana emergente
        $scope.ok = function (result) {
            $scope.saveSurvey().then(function(response){
                $modalInstance.close(promotion);
            });
        };

        $scope.saveSurvey = function(){
            $scope.promotionSchedule.consumerType = $scope.select.consumerType.id;
            $scope.promotionSchedule.interestLevel = $scope.select.interestLevel.id;
            $scope.promotionSchedule.request = $scope.select.request.id;
            
            return PromotionScheduleDataService.saveSurvey($scope.promotionSchedule,{});            
        }

        $scope.getFormFieldCssClass = function(ngModelController) {
            if(ngModelController.$pristine) return "";
            return ngModelController.$valid ? "has-success" : "has-error";
        };
    }

    $scope.showNotDeliveryReason = function(){
        return ModalService.showModal(
            {   templateUrl: 'templateShowNotDeliveryReason.html'/* plantilla en partials/routeSummary/edit2.html */,
                controller:'ShowNotDeliveryReasonController',size:'md',
                keyboard:false,
                resolve:{}
            },
            {});
    }

    ShowNotDeliveryReasonController = function($scope, $modalInstance,MessageBox){
        //Botones de la ventana emergente
        $scope.reason = {comments:null}
        $scope.ok = function (result) {
            //if($scope.reason.comments!=null)
                $modalInstance.close({comments: $scope.reason.comments});
            
        };
    }
    
    $scope.showFavorites=function(){
        return ModalService.showModal(
            {   templateUrl: 'templateShowFavorites.html'/* plantilla en partials/routeSummary/edit2.html */,
                controller:'ShowFavoritesController',size:'xl',
                keyboard:false,
                resolve:{}
            },
            {});
    }
    
    ShowFavoritesController = function($scope, $modalInstance,$stateParams){
        $scope.tableParamsFavoritos = new ngTableParams(
        {   page:1,
            count:10,
            sorting:{
                Proveedor:'desc'
            }
        },
        {
            total:0,
            getData:function($defer,params){
                var idPack= $stateParams.id; //routePointId
                var schedId = $stateParams.id2; //scheduleRouteId
                var oc = $stateParams.id4; //orden de compra
                var rpaId = $stateParams.id3; //routePointActivityId

                var postParams = {page:params.page(), rowsPerPage:params.count(),stateParams:$stateParams,idrow:idPack,idrow2:schedId,idrow3:oc,idrow4:rpaId};
                var filter=params.filter();
                var sorting = params.sorting();
                var sortField=UtilsService.getKeysFromJsonOnject(sorting)[0];

                if(sorting) angular.extend(postParams,{sortField:sortField,sortDir:sorting[sortField]});
                if(filter) angular.extend(postParams,{filter:filter});

                RouteSummaryDataService.getRouteSummaryFavorites(postParams)
                .then(function(response){
                    var data=response.data;
                    $scope.isLoading=false;
                    params.total(data.meta.totalRecords);
                    $defer.resolve(data.data);
                });
            }
        }
        );
        
        $scope.addFavoriteToCar = function (product) {
            var routePointId = $stateParams.id; //routePointId
            //path: Variable enviada desde PHP que contien la ruta raíz de la aplicación. .../public
            var urlCarDetails = path +"/User/Store/viewCart/routePointId/" + routePointId;
            RouteSummaryDataService.addProductToCar({id: product.idProducto, routePoint:routePointId})
                    .success(function (data, status, headers, config) {
                        location.href = urlCarDetails;
                    });
        }
    
        $scope.ok = function (result) {
            $modalInstance.close();       
        };
    }
}
