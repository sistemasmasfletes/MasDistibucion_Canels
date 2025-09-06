(function(){
angular.module('masDistribucion.promotion',["ui.router"])
    .config(['$stateProvider', '$urlRouterProvider','$locationProvider','PARTIALPATH',
        function PromotionConfig($stateProvider, $urlRouterProvider,$locationProvider, PARTIALPATH){
            $stateProvider
            .state('promotion', {
                url: "/promotion",
                    views:{
                        'main':{
                            templateUrl: PARTIALPATH.promotion + 'index.html',
                            controller: 'PromotionIndexController'
                        }
                    }
                })
            .state('promotion.edit',{
                url:"/edit/{promotionId:[0-9]{1,6}}",
                views:{
                    'edit':{
                        templateUrl: PARTIALPATH.promotion + 'edit.html',
                        controller: 'PromotionEditController',
                        resolve: {
                            promotion: ['$stateParams','UtilsService','PromotionDataService',function($stateParams,UtilsService,PromotionDataService){
                                var data = PromotionDataService.getData();
                                var promotion = null;

                                if(!$stateParams.promotionId) return {}

                                promotion = UtilsService.findById(data,$stateParams.promotionId);
                                
                                if(promotion) return promotion
                                else{
                                    var paramsEdit = {promotionId: $stateParams.promotionId}                                   
                                    var getDataPromise = null;
                              
                                    getDataPromise = PromotionDataService.getPromotion({filter:paramsEdit})
                                
                                    return getDataPromise.then(function(response){

                                                var responseData = response.data.data;
                                                if(angular.isArray(responseData))
                                                    return responseData[0];
                                                else
                                                    return {};
                                            });
                                }
                            }
                        ]}
                    }
                }
            })
            .state('promotion.add', {
                url:"/add",
                views:{
                    'edit': {
                        templateUrl: PARTIALPATH.promotion + 'edit.html',
                        controller: 'PromotionEditController',
                        resolve: {
                            promotion: function(){
                                return {};                                  
                            }
                        }
                    }
                }          
            })
        }
    ])
    .controller('PromotionIndexController',
        ['$scope','$state','$stateParams','$injector','PARTIALPATH','ModalService','MessageBox','CatalogService','PromotionDataService','PromotionScheduleDataService',
        function PromotionIndexController($scope,$state,$stateParams,$injector,PARTIALPATH,ModalService,MessageBox,CatalogService,PromotionDataService,PromotionScheduleDataService){
            ngTableParams = $injector.get('ngTableParams');
            UtilsService = $injector.get('UtilsService');
            $scope.partials = PARTIALPATH.base;

            $scope.tableParams = new ngTableParams(
                {   page:1,
                    count:10,
                    sorting:{name:'ASC'}
                },
                {
                    total:0,
                    getData:function($defer,params){
                        var postParams = UtilsService.createNgTablePostParams(params);
                        
                        PromotionDataService.getPromotion(postParams)
                        .then(function(response){
                            $scope.isLoading=false;
                            var data=response.data;
                            
                            params.total(data.meta.totalRecords);
                            $defer.resolve(data.data);                                
                           
                            $scope.selectedRowId = null;
                            $scope.selRow = null;
                            //Setear el listado al DAO, útil a la hora de ir a una vista dependiente
                            PromotionDataService.setData(data.data);
                            $scope.promotionCosting = data.meta.promotionCosting;
                        });       
                    }
                }
            );
            
            $scope.updateSelection=function(promotion){                
                var data = $scope.tableParams.data;        
                for(var i=0;i<data.length;i++){
                    if(data[i].id!=promotion.id)
                        data[i].$selected=false;
                    else
                        data[i].$selected=true;
                }
                $scope.selectedRowId = promotion.id;
                $scope.selRow = promotion;


            }

            $scope.goEdit=function(){
                if($scope.selRow)
                    $state.go("promotion.edit",{promotionId:$scope.selRow.id});
                else
                    MessageBox.show('Para poder editar, es necesario seleccionar primero un registro.');
            }

            $scope.goAdd = function(){
                $state.go('promotion.add',$stateParams);
            }

            $scope.delete = function(obPromotion){
                if(obPromotion && obPromotion.id){
                    MessageBox.confirm("¿Estás seguro de eliminar la promoción y sus archivos asociados?","Eliminar").then(function(result){
                        $scope.loading = true;
                        PromotionDataService.delete({id:obPromotion.id}, {alertOnSuccess:true})
                        .success(function(data, status, headers, config){
                            $scope.loading=false;                    
                            if (!data.error) {
                                $scope.tableParams.reload();
                            }                        
                        })
                        /*.error(function(data, status, headers, config){
                            $scope.loading = false;                    
                        });*/
                    });
                }else
                    MessageBox.show('Para poder eliminar, es necesario seleccionar primero un registro.');
            }

            $scope.toggleFilter = function(params) {
                params.settings().$scope.show_filter = !params.settings().$scope.show_filter;
            }

            $scope.customFilter = [                
                {name:'name',type:'text',label:'Promoción'}               
            ]

            $scope.filterOpen = false;
            $scope.openFilter = function(){
                $scope.filterOpen = true;  
            }

            $scope.appFilter = function(filter){
                $scope.tableParams.settings().filterDelay=0
                $scope.tableParams.$params.filter=filter;                
            }
            
            $scope.schedulePromotion = function(clientId,point,isRegistered,row){
                var modalOptions = {
                    actionButtonText: 'Aceptar',
                    bodyText: ''
                };
                ModalService.showModal(
                    {   templateUrl: 'templatePromotion.html',
                        controller:'PromoController',size:'xl',
                        resolve:{
                            promotiondata:function(){
                                return {user: 88, client:100, point:100, isRegistered:true,row:row}
                            }
                        }
                    },
                    modalOptions).then(function (result) {});
            }            
            
            PromoController = function($scope,$http,$filter,ModalService,$modalInstance,PromotionDataService,promotiondata,CatalogService){   
                $scope.promotiondata=promotiondata;
                $scope.promotionSchedule = {userid:promotiondata.user, clientid:promotiondata.client, promotionid:null};
                $scope.branchesUser = [];
                $scope.deliveryDates = [];
                $scope.promotions=[];
                $scope.clients=[];
                $scope.categories=[];
                $scope.select = {};
                $scope.selectroute = false;
                $scope.selectclient = false;

                $scope.select.selBranche = [];
                
                //Obtener promociones del usuario, desde el cachÃ© local o desde el servidor
                	PromotionDataService.getRoutesList({filter:{name:""}}).then(function(response){
                        var arrData = response.data.data;
                        $scope.promotions = arrData;
                	});     

                	/*$scope.calculateDelivery=function(idcombo){
                		var selectedcombo;
                		if(idcombo == 1){
                			selectedcombo = $scope.select.selPromotion.id
                		}else{
                			selectedcombo = $scope.select.selBranche.rid
                		}
                		
                    	PromotionDataService.getScheduledList({id:selectedcombo,filter:{name:""}}).then(function(response){
                    	//PromotionDataService.getScheduledList({id:,filter:{name:""}}).then(function(response){
                            var arrData = response.data.data;
                            $scope.deliveryDates = arrData;
                        })
                    }*/
               
                	PromotionDataService.getCategoriesList({filter:{name:""}}).then(function(response){
                        var arrData = response.data.data;
                        $scope.categories = arrData;
                	});     
                	
                    $scope.getClients=function(){
                    	PromotionDataService.getClientsList({id:$scope.select.selCategory.id,filter:{name:""}}).then(function(response){
                            var arrData = response.data.data;
                            $scope.clients = arrData;
                        })
                    }
                    
                    $scope.getBranches=function(){
                        PromotionDataService.getBranchesUser({id:$scope.select.selClient.id}).then(function(response){
                            $scope.select.selBranche.id = response.data.data[0].id;
                            $scope.select.selBranche.rid = response.data.data[0].rid;
                        	var arrData = response.data.data;
                            $scope.branchesUser = arrData;
                        });
                    }
                    
                    $scope.showselrute = function(){
                        $scope.selectclient = false;
                        $scope.selectroute = true;
                        $scope.deliveryDates = [];
                        $scope.promotionSchedule = [];
                        $scope.clients=[];
                        $scope.branchesUser = [];
                    }
                    
                    $scope.showselclient = function(){
                        $scope.selectclient = true;
                        $scope.selectroute = false;
                        $scope.deliveryDates = [];
                        $scope.promotionSchedule = [];
                    }
                    
                	$scope.cerrar= function (result) {                        
                        $modalInstance.close(result);                        
                    };
                	
                    $scope.sendPromotion = function(){
                    	
                    	var validate = "undefined";
                    	var idruta = false;
                    	var idclientval = false;
                    	var idroutepointval = false;
                    	
                    	if($scope.selectclient){
                    		validate = typeof ($scope.select.selBranche);
                    		/*idruta = (validate  !== "undefined")?$scope.select.selBranche.rid:false;*/
                    		idruta = $scope.select.selBranche.rid;
                    		idroutepointval = $scope.select.selBranche.id;
                    		idclientval = $scope.select.selClient.id;
                    	}
                    	
                    	if($scope.selectroute){
                    		validate = typeof ($scope.select.selPromotion)
                    		idruta = (validate  !== "undefined")?$scope.select.selPromotion.id:false;
                    	}

                    	/*if(typeof ($scope.promotionSchedule.promotionDate)=== "undefined"){
                    		validate = "undefined";
                    	}*/
                    	
                    	if(validate  !== "undefined"){
                    		MessageBox.confirm("¿Deseas enviar la promocion?","Aceptar").then(function(){
                    			PromotionDataService.setSendPromo({id:idruta, pid:promotiondata.row.id, idclient:idclientval,idroutepoint:idroutepointval}, {alertOnSuccess:true})
                                //PromotionDataService.setSendPromo({id:idruta, pdate:$scope.promotionSchedule.promotionDate.name, pid:promotiondata.row.id, idclient:idclientval,idroutepoint:idroutepointval}, {alertOnSuccess:true})
                                .success(function(data, status, headers, config){
                                    if (!data.error) {
                                    	$scope.cerrar();
                                    }                        
                                })
                                .error(function(data, status, headers, config){
                                });
                            });
                    		
                    	}else{
                    		
                    		MessageBox.confirm("Debe llenar todos los campos","Aceptar").then(function(){
                    	    });

                    	}
                    }
                    
            }            
            
        }
    ])
    .controller('PromotionEditController',
        ['$scope','$timeout','$http','$state','$stateParams','$filter','PATH','PARTIALPATH','MessageBox','CatalogService','PromotionDataService','promotion',
        function PromotionEditController($scope,$timeout,$http,$state,$stateParams,$filter,PATH,PARTIALPATH,MessageBox,CatalogService,PromotionDataService,promotion){
            $scope.promotion = promotion;
            PromotionDataService.getPromotionCosting()
            .then(function(response){
                var promotionCosting = response.data.promotionCosting;

                $scope.alerts = [{type:"info",msg:"Cada recurso tiene un valor de "+parseFloat(promotionCosting)+" créditos. Si al programar una promoción, un recurso no se entrega, éste tendrá un costo de "+(parseFloat(promotionCosting)/2)+" créditos."}]
            });

            $scope.back = function(){
                $state.go('^',$stateParams);
            }

            $scope.closeAlert = function(index) {
                $scope.alerts.splice(index, 1);
            };

            $scope.getResourceType = [{id:1,type:'Archivo'},{id:2,type:'Dirección Web'}]


            $scope.resource = [];
            $scope.newResource = {};
            $scope.newResource.resourceType=1;
            $scope.sumBytes = 5000;

            $scope.loaData = function(paramId){
                $scope.loadingDetail = true;
                PromotionDataService.getPromotionDetail({filter:{promotionId:paramId/*$stateParams.promotionId*/}})
                .then(function(response){
                    var resourceData = response.data.data;
                    $scope.postMaxSize = response.data.postMaxSize;
                    $scope.fileMaxSize = response.data.fileMaxSize;
                    $scope.resource = transformResourceArray(resourceData);
                    $scope.loadingDetail = false;
                }); 
            }

            $scope.loaData($stateParams.promotionId);

            transformResourceArray = function(data){
                var ext, path,filename, uniqueNameChars=13, lastslash, lastdot
                for(var i=0;i<data.length;i++){
                    data[i]["num"] = i+1;
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

            $scope.addResource = function(){
                
                if($scope.newResource){
                    var oFile = angular.element('#recurso')[0].files[0];                   
                    if(!$scope.newResource.name) {MessageBox.show("El nombre del recurso es requerido.");return;}
                    if($scope.newResource.resourceType==1 && !oFile) {MessageBox.show("Debe seleccionar un archivo.");return;}
                    
                    if(oFile){
                        if(!$scope.validateFileExtension(oFile.name)){
                            MessageBox.show("Has seleccionado un tipo de archivo inválido");                            
                            return;
                        }

                        if(oFile.size>$scope.fileMaxSize){
                            MessageBox.show("El tamaño de cada archivo individual debe ser menor a " + $scope.fileMaxSize/1048576 + "Mb.");                            
                            return;
                        }

                        $scope.sumBytes+=oFile.size;
                    }                    

                    if($scope.newResource.resourceType==1 && $scope.sumBytes>$scope.postMaxSize){
                        MessageBox.show("El tamaño de la información rebasa el límite permitido de "+$scope.postMaxSize/1048576+"Mb.");
                        $scope.sumBytes -= oFile.size;
                        return;
                    }

                    if($scope.newResource.resourceType==2 && !$scope.newResource.path) {MessageBox.show("La dirección web del recurso es requerido.");return;}

                    $scope.resource.push({
                        id:($scope.resource.length+1)*-1,
                        name:$scope.newResource.name,
                        resourceType:$scope.newResource.resourceType,
                        file: $scope.newResource.resourceType == 1 ? oFile : null,
                        path: $scope.newResource.path
                    });

                    transformResourceArray($scope.resource);
                }
            }

            $scope.delResource = function(row){
                if(row){
                    MessageBox.confirm("¿Está seguro de eliminar este recurso?","Eliminar").then(function(){
                       for(var i=0;i<$scope.resource.length;i++){
                            if($scope.resource[i].id==row.id){
                                if($scope.resource[i].file) $scope.sumBytes -= $scope.resource[i].file.size;
                                $scope.resource.splice(i,1);
                                break;
                            }
                        } 
                    });
                    
                }
            }

            $scope.save = function(){
                if($scope.resource.length==0) {MessageBox.show("Debe agregar al menos un recurso/archivo.");return;}
                var form_data = new FormData();

                form_data.append('promotionId', $scope.promotion.id);
                form_data.append('name', $scope.promotion.name);
                                
                //Subir solo elementos nuevos
                var arrPostItems = [];
                angular.forEach($scope.resource,function(item,key){
                    //if(item.id<0)
                        this.push(item);
                },arrPostItems);

                //if(!arrPostItems.length>0) {MessageBox.show("No hay elementos nuevos");return;}

                for(var i=0;i<arrPostItems.length;i++){
                    form_data.append('id'+i, arrPostItems[i].id);
                    form_data.append('resourceType'+i, arrPostItems[i].resourceType);
                    form_data.append('name'+i, arrPostItems[i].name);
                    form_data.append('path'+i, arrPostItems[i].path);
                    form_data.append('filedata'+i, arrPostItems[i].file);
                }
                
                form_data.append('items', arrPostItems.length);

                $scope.loading = true;
                $http.post( 
                    PATH.promotion + 'save', 
                    form_data, 
                    {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}
                    }
                ).success(function (response) {
                    $scope.loading = false;
                    MessageBox.show("Los datos se guardaron satisfactoriamente.")
                    .then(function(){
                        $scope.back();
                        $scope.tableParams.reload();
                    });
                    
                });
            }

            $scope.getFormFieldCssClass = function(ngModelController) {
                if(ngModelController.$pristine) return "";
                return ngModelController.$valid ? "has-success" : "has-error";
            }

            $scope.validateFileExtension=function(filename){
                var extensionsValid = [".xlsx",".xls",".csv",".txt",".jpg",".jpeg",".png",".bmp",".html",".htm",".pdf",".wav",".wma",".mp3",".mp4",".m4a",".ogg",".flac",".wmv",".avi",".mpeg"];
                
                fileExt = filename.substring(filename.lastIndexOf('.'));
                if (extensionsValid.indexOf(fileExt.toLowerCase()) < 0)                    
                    return false;                
                else return true;
            }
            
        }   
    ])
})();