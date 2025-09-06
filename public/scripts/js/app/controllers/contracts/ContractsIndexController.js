function ContractsIndexController($scope, $timeout, $state, $stateParams, PATH, PARTIALPATH, ModalService, JQGridService, ContractDataService, $http) {
	
	ContractDataService.getContracts()
	.then(function(response){
		$scope.usrtype = response.data[0].Usrtype.role;	  
		$scope.terms = response.data[0].Terms;
		$scope.privacy = response.data[0].Privacy;
		$scope.service = response.data[0].Service;
		$scope.termsac = (response.data[0].TermsAc === "1") ? true : false;
		$scope.privacyac = (response.data[0].PrivacyAc === "1") ? true : false;
		$scope.serviceac = (response.data[0].ServiceAc === "1") ? true : false;
		
	});
  
	$scope.add = function(){
		var postParams = {tac:$scope.termsac, pac:$scope.privacyac, sac:$scope.serviceac};
		ContractDataService.setAcept(postParams)
		.then(function(response){});
	}
	
	$scope.upTerm = function(){
		
    	var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
				bodyText: '¡Esta acción obligara a los clientes a aceptar nuevamente los terminos para hacer uso del sistema, ¿Esta seguro de continuar?'
					};
    	ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {
	
			var postParams = {contrId:'btnTerm', contr:$scope.terms};
			
			ContractDataService.upContract(postParams)
			.then(function(response){
				var modalOptions = {
	                actionButtonText: 'Aceptar',
	                bodyText: '¡El contrato se ha modificado exitosamente!'
	            };
	            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
			});
    	});
	}
	
	$scope.upServ = function(){
		
    	var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
				bodyText: '¡Esta acción obligara a los clientes a aceptar nuevamente los terminos para hacer uso del sistema, ¿Esta seguro de continuar?'
					};
    	ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {
		
			var postParams = {contrId:'btnServ', contr:$scope.service};
			
			ContractDataService.upContract(postParams)
			.then(function(response){
				var modalOptions = {
	                actionButtonText: 'Aceptar',
	                bodyText: '¡El contrato se ha modificado exitosamente!'
	            };
	            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
			});
    	});
	}
	
	$scope.upPriv = function(){
		
    	var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
				bodyText: '¡Esta acción obligara a los clientes a aceptar nuevamente los terminos para hacer uso del sistema, ¿Esta seguro de continuar?'
					};
    	ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {
		
			var postParams = {contrId:'btnPriv', contr:$scope.privacy};

			ContractDataService.upContract(postParams)
			.then(function(response){
				var modalOptions = {
	                actionButtonText: 'Aceptar',
	                bodyText: '¡El contrato se ha modificado exitosamente!'
	            };
	            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
			});
    	});
	}
	
	/*$scope.upContract = function(event){/*************************ESTO SOLO FUNCIONA EN EL NAVEGADOR FIREFOX*****************************************************
		
    	var modalOptions = {closeButtonText: 'Cancelar', actionButtonText: 'Aceptar', 
				bodyText: '¡Esta acción obligara a los clientes a aceptar nuevamente los terminos para hacer uso del sistema, ¿Esta seguro de continuar?'
					};
    	ModalService.showModal({templateUrl: PARTIALPATH.modal}, modalOptions).then(function (result) {
		
			switch (event.target.id){
				case 'btnServ':
					var postParams = {contrId:event.target.id, contr:$scope.service};
					break;
				
				case 'btnPriv':
					var postParams = {contrId:event.target.id, contr:$scope.privacy};
					break;
	
				case 'btnTerm':
					var postParams = {contrId:event.target.id, contr:$scope.terms};
					break;
			}
			
			ContractDataService.upContract(postParams)
			.then(function(response){
				var modalOptions = {
	                actionButtonText: 'Aceptar',
	                bodyText: '¡El contrato se ha modificado exitosamente!'
	            };
	            ModalService.showModal({templateUrl: PARTIALPATH.modalInfo}, modalOptions).then(function (result){});
			});
    	});
	}*/

}