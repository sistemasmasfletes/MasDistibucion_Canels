(function(){
angular.module('masDistribucion.promotionReceived',[
    "ui.router"
])
.config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',
        function PromotionReceivedConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
            $stateProvider
            .state('promotionReceived', {
                url: "/promotionReceived",
                views:{
                    'main':{
                        templateUrl: PARTIALPATH.promotionReceived + 'index.html',
                        controller: 'PromotionReceivedIndexController'
                    }
                }
            })
        }
        ])
.controller('PromotionReceivedIndexController',
            ['$scope','$state','$stateParams','PARTIALPATH',
            'ModalService','MessageBox','PromotionReceivedDataService','CatalogService','$injector',
            function PromotionReceivedIndexController($scope,$state,$stateParams,PARTIALPATH,ModalService,MessageBox,PromotionReceivedDataService,CatalogService,$injector){
                
                RECHAZAR=1;
                NO_ENTREGAR=2;
                ENTREGAR=3;
                TIPO_COBRO_TOTAL = 1;
                TIPO_COBRO_50_POR_CIENTO = 2;
               
                STATUS_PROMOCION_ENTREGADO = 3;
                STATUS_PROMOCION_RECHAZADO = 6;
                STATUS_PROMOCION_NO_ENTREGADO = 7;

                ngTableParams = $injector.get('ngTableParams');
                $scope.UtilsService = $injector.get('UtilsService');
                $scope.partials = PARTIALPATH.base;
                $scope.getInterestLevel = CatalogService.getInterestLevel();

                $scope.tableParams = new ngTableParams(
                    {   page:1,
                        count:10,
                        sorting:{creationDate:'desc'}
                    },
                    {
                        total:0,
                        getData:function($defer,params){
                            var postParams = $scope.UtilsService.createNgTablePostParams(params,{clientId:null});
                            
                            //PromotionReceivedDataService.getSchedule(postParams)
                            PromotionReceivedDataService.getReceived(postParams)
                            .then(function(response){
                                var data=response.data;
                                $scope.isLoading=false;
                                params.total(data.meta.totalRecords);
                                $defer.resolve(data.data);                                
                                
                                PromotionReceivedDataService.setData(data.data);
                            });       
                        }
                    }

                );

                $scope.changeSelection = function(pkg) {       
                    var data = $scope.tableParams.data;        
                    for(var i=0;i<data.length;i++){
                    if(data[i].id!=pkg.id)
                        data[i].$selected=false;
                    }
                    $scope.selectedRowId = pkg.id;
                    $scope.selRow=pkg;                    
                }
                
                $scope.openSurveyDesc=function(promotion){
                    return ModalService.showModal(
                    {   templateUrl: 'templateShowSurvey.html'/* plantilla en partials/promotionSchedule/index.html */,
                        controller:'ShowSurveyController',size:'md',
                        keyboard:false,
                        resolve:{
                            promotion:function(){
                                return promotion;
                            }
                        }
                    },
                    {});
                }

                ShowSurveyController = function($scope, $modalInstance,CatalogService,UtilsService,MessageBox,promotion){
                    $scope.promotion = promotion;
                    $scope.getInterestLevel = CatalogService.getInterestLevel();
                    $scope.getConsumerType = CatalogService.getConsumerType();
                    $scope.getRequest = CatalogService.getRequest();

                    $scope.UtilsService = UtilsService;
                    //Botones de la ventana emergente
                    $scope.ok = function (result) {                        
                        $modalInstance.close(promotion);                        
                    };
                }
                
                $scope.rejectPromotion = function(pack){       
                    if(pack){
                        var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
                            bodyText: 'Esta acción no se puede deshacer, ¿Esta seguro que desea rechazar la promoción1?'
                                };
                        
                        ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {
                        	PromotionReceivedDataService.updateStatusPromotion(
                                {   promotionScheduleId:pack.id,
                                    routePointActivityId:pack.idactivitypoint,
                                    tipoCobro:TIPO_COBRO_TOTAL,
                                    comentarios:(typeof pack.comments==='undefined' ? 'Promoción rechazada por el destinatario' : pack.comments),
                                    status: STATUS_PROMOCION_RECHAZADO
                                }
                            ).then(function(response){
                                $scope.tableParams.reload().then(function(){
                                    //checkIsDone();
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
                        	PromotionReceivedDataService.updateStatusPromotion(
                                {   
                                	promotionScheduleId:pack.id,
                                	//promotionScheduleId:promotion.id,
                                	routePointActivityId:pack.idactivitypoint,
                                    //routePointActivityId:promotion.idactivitypoint,
                                    tipoCobro:TIPO_COBRO_50_POR_CIENTO,
                                    comentarios:pack.comments,
                                    status: STATUS_PROMOCION_NO_ENTREGADO
                                }

                            ).then(function(response){
                                $scope.tableParams.reload().then(function(){
                                    //checkIsDone();
                                })                    
                            });
                        });

                    }
                } 
                
                $scope.getPromotionResult=function(pack){
                	PromotionReceivedDataService.saveHourPoint(pack);
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
                                        routePointActivityId:promotion.idactivitypoint,
                                        ttype:2,
                                        transactionid:promotion.promotionId
                                    };

                                //Guardar evidencia de entrega
                                PromotionReceivedDataService.save(obActivityDetail, {})
                                .success(function(data, status, headers, config){                        
                                    //Actualizar estatus y balance
                                	PromotionReceivedDataService.updateStatusPromotion(
                                        {   promotionScheduleId:promotion.id,
                                            routePointActivityId:promotion.idactivitypoint,
                                            tipoCobro:TIPO_COBRO_TOTAL,
                                            comentarios:null,
                                            status: STATUS_PROMOCION_ENTREGADO
                                        }
                                    ).then(function(response){                          
                                        $scope.showSurvey(pack).then(function(promotion){                    
                                            $scope.tableParams.reload().then(function(){
                                                //checkIsDone();
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
                	
                	$scope.promotion = pack;

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
                    
                	$scope.promotion = promotion
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

                    $scope.checkedLink = function(rowid,rows){
                    	for(var i=0;i<rows.length;i++){
                            if(rows[i].id==rowid){
                                rows[i].checked=true;
                                break;
                            }
                        }
                    }
                    
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

                    $scope.loaData(promotion.promotionId);
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
                        {   templateUrl: 'templateShowSurvey1.html'/* plantilla en partials/routeSummary/edit2.html */,
                            controller:'ShowSurveyController1',size:'md',
                            keyboard:false,
                            resolve:{
                                promotion:function(){
                                    return pack;
                                }
                            }
                        },
                        {});
                }

                ShowSurveyController1 = function($scope, $modalInstance,CatalogService,PromotionScheduleDataService,MessageBox,promotion){
                    $scope.promotionSchedule = {id:promotion.promotionscheduleid}
                    $scope.select = {};
                    $scope.getInterestLevel = CatalogService.getInterestLevel();
                    $scope.getConsumerType = CatalogService.getConsumerType();
                    $scope.getRequest = CatalogService.getRequest();
                    $scope.promotionSchedule.receivingUser = promotion.client;

                    //Botones de la ventana emergente
                    $scope.ok = function (result) {
                        $scope.saveSurvey().then(function(response){
                            $modalInstance.close(promotion);
                        });
                    };

                    $scope.saveSurvey = function(){
                        $scope.promotionSchedule.id = promotion.id;
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
                
            }
            ]);
})();
