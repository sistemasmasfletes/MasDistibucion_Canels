//Pantalla salvar evidencia
function RouteSummaryEditPointsController($scope,$timeout,$state,$stateParams,ngTableParams,PARTIALPATH,ModalService,MessageBox,CatalogService,RouteSummaryDataService,routeSummary,CONFIG,routePointActivity){
    
    $scope.routeSummaries=routeSummary;
    $scope.routeSummaries.Image=raw_image_data;
    
    $scope.save=save;
    $scope.back=function(){$state.go('^',$stateParams)};
    var $OC = $stateParams.id;
    
    RECHAZAR=1;
    NO_ENTREGAR=2;
    ENTREGAR=3;

    TIPO_COBRO_TOTAL = 1;
    TIPO_COBRO_50_POR_CIENTO = 2;
   
    STATUS_PROMOCION_ENTREGADO = 3;
    STATUS_PROMOCION_RECHAZADO = 6;
    STATUS_PROMOCION_NO_ENTREGADO = 7;
    
    ACTIVDAD_ENTREGAR = 3
    ACTIVDAD_RECOLECTAR = 1
    
    var $scheduleId = $stateParams.scheId;
    var $pointId = $stateParams.point;
    var $rpa_Id = $stateParams.rpaId;
    var $ocId = $stateParams.id;
    var TRANSACTION_TYPE_ORDER = 1;
    $scope.isLoading=false;
    $scope.partials = CONFIG.PARTIALS;
    $scope.oc = $stateParams.id;
    
    var $acname = (routePointActivity.status==ACTIVDAD_RECOLECTAR ? 'Recolectar' : (routePointActivity.status==ACTIVDAD_ENTREGAR ? 'Entregar' : ''));
    var actividadComentarios = (routePointActivity.status==ACTIVDAD_RECOLECTAR ? 'Recolección' : (routePointActivity.status==ACTIVDAD_ENTREGAR ? 'Entrega' : ''));
    $scope.routeSummaries.acname = $acname;
    $scope.routeSummariesStatus = CatalogService.getRouteSummaryStatus($acname);
    $scope.routeSummaries.status = routePointActivity.status;
    $scope.routeSummaries.comentarios = actividadComentarios + " de la Orden #"+routePointActivity.id + " en " + routePointActivity.name;
    $scope.routeSummaries.receptor = (routePointActivity.status==ACTIVDAD_RECOLECTAR ? routePointActivity.chofer : null);
    CatalogService.getCatalogCauses().then(function(response){$scope.getCausesEvidence = response.data;});
                 
    $scope.info=function(){
        $state.go('routeSummary.info',{scheId:$scheduleId,point:$pointId,id:$ocId,rpaId:$rpa_Id}) //NAVEGAR A INFORMACIÓN DEL PAQUETE
    }
    
    function save(){
        var $fileName="";
        if($scope.routeSummaries){
            $scope.routeSummaries.routePointActivityId=$rpa_Id;
            $scope.routeSummaries.transactionid=$ocId;
            $scope.routeSummaries.ttype = TRANSACTION_TYPE_ORDER;
            var imgdata = angular.element('#imagen').val();
            if(!imgdata){
                MessageBox.show('Para poder guardar debe capturar la evidencia.');
                return;
            }
            
            $scope.loading=true;
            if($scope.routeSummaries.causeId==='') $scope.routeSummaries.causeId=null;
            RouteSummaryDataService.save($scope.routeSummaries, {})
                .success(function(data, status, headers, config){
                    $scope.loading=false;
                    if (data.error) {
                        alert(data.error);
                         var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions);
                    
                    } else {                        
                        
                        $fileName = data;
                        var res = $fileName.replace($rpa_Id);
                        
                        //var raw_image_data;
                        //var imagen = document.getElementById('imagen').value;
                        var ajaxurl = 'test.php';
                        
                        data =  {'action': imgdata,'nombre':res};
                        $.post(ajaxurl, data, function (response) {});
                        console.log(res);
                        console.log($rpa_Id);
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Evidencia almacenada con exito!'
                        };
                        var hasPromotion = false;
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                            hasPromotion = (routePointActivity.promotionid>0 && !(routePointActivity.curpoint==null && routePointActivity.endpoint==null) && (routePointActivity.curpoint==routePointActivity.endpoint));
                            if(hasPromotion){
                                var promotion=null;        
                                $scope.openPromotion(routePointActivity).then(function(result){
                                    promotion = result.promotion;                                    
                                    switch(result.action){
                                        case RECHAZAR:
                                            $scope.showNotDeliveryReason().then(function(result){
                                                promotion = angular.extend({},promotion,{comments:result.comments});
                                                $scope.rejectPromotion(promotion).then(function(){
                                                    $state.go('routeSummary.add',{id2:$scheduleId,id:$pointId});
                                                });
                                            });
                                            break;
                                        case NO_ENTREGAR:
                                            $scope.showNotDeliveryReason().then(function(result){                        
                                                promotion = angular.extend({},promotion,{comments:result.comments});
                                                $scope.notDeliver(promotion).then(function(){
                                                    $state.go('routeSummary.add',{id2:$scheduleId,id:$pointId});
                                                });
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
                                                    $scope.showSurvey(promotion).then(function(promotion){                    
                                                        $state.go('routeSummary.add',{id2:$scheduleId,id:$pointId});
                                                    });
                                                });                        
                                            });
                                            break;
                                    } 
                                });
                            }else
                                $state.go('routeSummary.add',{id2:$scheduleId,id:$pointId}); //NAVEGA A PANTALLA DE PAQUETES
                            });
                        }
                })
                .error(function(data, status, headers, config){
                    $scope.loading = false;                    
                });
        }
    }
    
    $scope.regresar=function(){
        $state.go('routeSummary.add',{id2:$scheduleId,id:$pointId});
    }    
    
    $scope.openPromotion=function(pack){
        if(pack.numfiles==1)
            return $scope.showResource(pack);
        else
            return $scope.openPromotionWindow(pack);
    }

    $scope.rejectPromotion = function(pack){       
        if(pack){
            return RouteSummaryDataService.updateStatusPromotion(
                {   promotionScheduleId:pack.promotionscheduleid,
                    routePointActivityId:pack.routePointActivityId,
                    tipoCobro:TIPO_COBRO_TOTAL,
                    comentarios:(typeof pack.comments==='undefined' ? 'Promoción rechazada por el destinatario' : pack.comments),
                    status: STATUS_PROMOCION_RECHAZADO
                }
            );
        }//Si no hay paquete, regresar promesa vacía, para poder ejecutar el then en la llamada.
        else{
            return $timeout(function(){return {}},500);
        }
    }

    $scope.notDeliver = function(pack){
        if(pack){
            return RouteSummaryDataService.updateStatusPromotion(
                {   promotionScheduleId:pack.promotionscheduleid,
                    routePointActivityId:pack.routePointActivityId,
                    tipoCobro:TIPO_COBRO_50_POR_CIENTO,
                    comentarios:pack.comments,
                    status: STATUS_PROMOCION_NO_ENTREGADO
                }
            );
        }//Si no hay paquete, regresar promesa vacía, para poder ejecutar el then en la llamada.
        else{
            return $timeout(function(){return {}},500);
        }
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
                    },
                    activityDetail:function(){
                        return $scope.routeSummaries;
                    }
                }
            },
            {});
    }

    ShowSurveyController = function($scope, $modalInstance,CatalogService,PromotionScheduleDataService,MessageBox,promotion,activityDetail){
        $scope.promotionSchedule = {id:promotion.promotionscheduleid, receivingUser:activityDetail.receptor}
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
}
