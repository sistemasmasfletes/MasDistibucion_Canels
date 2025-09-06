function PointsEditController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,CatalogService,PointsDataService,point,CONFIG,UtilsService,$filter){
    $scope.point=point;
    $scope.save=save;
    $scope.back=function(){$state.go('^',$stateParams)};
    $scope.point.type= 1;
    $scope.point.status= 1;
    $scope.point.categoryId_id = 173;
    $scope.point.intNumber = "SN";
	$scope.point.address_id = 413;
	$scope.point.userbancheid = "";
	
	if(!$scope.point.code){
	    CatalogService.getMaxpointId()
	    .then(function(response){
	    	$scope.point.code = 'CPN-'+response.data[0][1];
	     }); 
	}
	
      	if (idbranche !== '' ) {
           var separa = idbranche.split(",");
           $scope.point.neighborhood = separa[3];
           $scope.point.zipcode = separa[4];
           $scope.point.extNumber = separa[1];
           $scope.point.intNumber = (separa[2] ===" ")?"SN":separa[2];
           $scope.point.name = separa[5];
           $scope.point.phone = separa[6];
           $scope.point.contact = separa[5];
           $scope.point.userbancheid = separa[7];
           $scope.point.comments = separa[0]+", "+separa[1]+", "+separa[3]+", "+separa[4];
           $scope.point.brancheid = separa[8];
           /*$scope.point.type= 1;
           $scope.point.status= 1;
           $scope.point.categoryId_id = 173;*/
           }
    
    PointsDataService.getCountry($scope.point, {})
        .then(function(response){
            $scope.getCountry = response.data;
        });

    PointsDataService.getRoute($scope.point, {})
    .then(function(response){
        $scope.getRoute = response.data;
    });
    
    $scope.getSelectedCountry = function () {
    	if(!$scope.point.country_id){
	    /*    var countryId = $scope.point.country_id;
    	}else{*/
    		$scope.point.country_id = 143
    		//var countryId = 143;
    	}
	     
    	var postParams = {id:$scope.point.country_id};
	        PointsDataService.getState(postParams)
	            .then(function(response){
	                $scope.getState = response.data;
	            });
    	
    };
    
    $scope.getSelectedState = function(){
    	if(!$scope.point.state_id){
    		$scope.point.state_id = 24
    	}
	        $scope.getState;
	        //var stateId = $scope.point.state_id;
	        var postParams = {id:$scope.point.state_id};
	        PointsDataService.getCity(postParams)
	            .then(function(response){
	                $scope.getCity = response.data;
	            });
    	
    };
    
    $scope.getSelectedCity = function(){
    	if(!$scope.point.city_id){
    		$scope.point.city_id = 1829;
    	}
	        $scope.getCity;
	        //var cityId = $scope.point.city_id;
	        var postParams = {id:$scope.point.city_id};
	        PointsDataService.getAddress(postParams)
	            .then(function(response){
	                $scope.getAddress = response.data;
	            });
    	
    };
    
    $scope.pointTypes = CatalogService.getPointType();
    $scope.pointStatus = CatalogService.getPointStatus();
    $scope.getCategories=[];
    CatalogService.getBranchCategories()
        .then(function(response){
            $scope.getCategories = response.data;
         });
         
    $scope.hour = new Date("2020-01-01 10:00:00");
    $scope.hourEnd =  new Date("2020-01-01 20:10:00");
    $scope.actime = new Date("2020-01-01 00:05:00"); 	$scope.isoHour = $scope.hour.toISOString();
    if($scope.point.opening_time){
    	var opstr = $scope.point.opening_time;
    	var clstr = $scope.point.closing_time;
    	var actstr = $scope.point.activitytime;
    	var opstr1 = UtilsService.getDateFromString($scope.point.opening_time).toString();
    	var clstr1 = UtilsService.getDateFromString($scope.point.closing_time).toString();
    	var actstr1 = UtilsService.getDateFromString($scope.point.activitytime).toString();
    	opstr1 = opstr1.replace('00:00:00',opstr);
    	clstr1 = clstr1.replace('00:00:00',clstr);
    	actstr1 = actstr1.replace('00:00:00',actstr);
        $scope.hour = opstr1;
        $scope.hourEnd = clstr1;
        $scope.actime = actstr1; 
        //$scope.hour = UtilsService.getDateFromString($scope.point.opening_time);
        //$scope.hourEnd = UtilsService.getDateFromString($scope.point.closing_time);
    }

    function save(){
        if($scope.point){
            $scope.loading=true;
            if($scope.point.type=='2'){$scope.point.webpage=null;}
            if($scope.point.type=='2'){$scope.point.categoryId_id=null;}
            if($scope.point.type=='2'){$scope.point.urlGoogleMaps=null;}
            $scope.point.opening_time = $filter('date')($scope.hour, 'yyyy-MM-dd HH:mm:ss');
            $scope.point.closing_time = $filter('date')($scope.hourEnd, 'yyyy-MM-dd HH:mm:ss');
            $scope.point.activitytime = $filter('date')($scope.actime, 'yyyy-MM-dd HH:mm:ss');
            
            PointsDataService.save($scope.point, {})
                .success(function(data, status, headers, config){
                    $scope.loading=false;
                    if (data.error) {
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: data.error
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions);
                    } else {
                        var modalOptions = {
                            actionButtonText: 'Aceptar',
                            bodyText: '¡Registro guardado con éxito!'
                        };
                        ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result) {
                                $scope.back();
                                $scope.tableParams.reload();
                            });
                        }
                })
                .error(function(data, status, headers, config){
                    $scope.loading = false;                    
                });
        }
    }
}