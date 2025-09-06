function SessionData($http){
	this.get = function(urlPath, paramsData, httpParams){
		return $http.post(urlPath, paramsData, httpParams);
	}
	
}