function InitController($scope,$state,$cookieStore,$timeout,CONFIG,SessionData,RoutesDataService){

	$scope.sesion=null;
    SessionData.get(CONFIG.PATH + '/App/getSesionData',{})
    	.success(function(data, status, headers, config) {
    		$scope.sesion=data.session;
    		$cookieStore.put('usersession',$scope.sesion);    		
    	})
    	.error(function(data, status, headers, config) {});
};
