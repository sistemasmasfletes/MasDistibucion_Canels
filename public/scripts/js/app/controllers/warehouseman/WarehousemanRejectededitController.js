function WarehousemanRejectededitController($scope,$timeout,$state,$stateParams,PARTIALPATH,ModalService,WarehousemanDataService,UtilsService,orderId,$filter){

	var modalPath = PARTIALPATH.modal
    $scope.back=function(){$state.go('^',$stateParams)};
    
    WarehousemanDataService.getPackageRejectedById(orderId)
    .then(function(response){
    	
    	$scope.folio = response.data.data[0].ordid;
    	$scope.comments = response.data.data[0].status_reason;
    	$scope.Cid = response.data.data[0].Cid;
    	$scope.b_id = response.data.data[0].b_id;
    	$scope.pb_id = response.data.data[0].pb_id;
    	$scope.ps_id = response.data.data[0].ps_id;
    	$scope.brc_id = response.data.data[0].brc_id;
    	$scope.brc_id2 = response.data.data[0].brc_id2;
    	$scope.brc_id3 = response.data.data[0].brc_id3;
    	$scope.rt_id = response.data.data[0].rt_id;
    	$scope.weight = response.data.data[0].weight;
    	$scope.tvol = response.data.data[0].tvol;
    });
    
    $scope.dest_send = function(btnId){
    	
    	var schid;
    	var s_date;
    	var buyer;
    	var msg;
    	
    	switch(btnId){
    		case'acdes':
    				buyer = $scope.brc_id2;//corresponde al punto de entrega inicial
    				msg = 'El paquete se reenviara al punto de destino';
    			break;
    			
    		case'acorg':
    				buyer = $scope.brc_id3;//corresponde al punto de recoleccion inicial
    				msg = 'El paquete se enviara el punto de origens';
    			break;
    	}
    	
    	
    	var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
				bodyText: msg
					};
    	ModalService.showModal({templateUrl: modalPath}, modalOptions).then(function (result) {
    	
    	WarehousemanDataService.getScheduleRejected($scope.Cid)
        .then(function(response){
        	
        	for(var i = 0; i < response.data.length; i++){
        		if(response.data[i].r_id == $scope.rt_id){
                	schid = response.data[i].sch_id;
                	s_date = response.data[i].start_date;
        		}
        	}
        	
        	var urlShipping = modalPath.substr(0,modalPath.indexOf('script'))+"Ajax_UserShipping/calculateShippingPerRoute/";
    	    	
                $.post(urlShipping, {
                    idRoute : schid,
                    buyerId : $scope.bid,
                    orderId : $scope.folio,
                    pointSeller: $scope.brc_id,//origen
                    pointBuyer : buyer,
                    selectedScheduledDate : s_date,
                    tweight : $scope.weight,
                	tvol : $scope.tvol
                    
                }, function(data){

                	WarehousemanDataService.setScheduleRejected($scope.folio)
                    .then(function(response){
                    	
                    	alert(response.data.toSource());
                    	
                    });
                },'json');
    	    	
    	    });
        });
    }
}