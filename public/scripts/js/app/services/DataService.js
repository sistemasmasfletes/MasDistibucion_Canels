function DataService($http) {
    
    this.delete=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };

    this.save=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };

    this.edit=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };

    this.get=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };

    this.saveCurrentHour=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.saveProgress=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.getScheduleRouteId=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.saveHourPoint=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.saveStatus=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.getActivityName=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.getCountPacks=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.savePointEvidence=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    };
    
    this.saveEndHourRoute=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    }
    
    this.saveEvidenceCI=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    }

    this.generatePDF=function(urlPath, paramsData, httpParams){
        return $http.post(urlPath, paramsData, httpParams);
}

    this.addProductToCar=function(urlPath, paramsData, httpParams) {		
    	return $http.post(urlPath, paramsData, httpParams);
    }

    this.sendPromo=function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    }

}
