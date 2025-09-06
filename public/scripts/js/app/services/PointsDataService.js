function PointsDataService(DataService,PATH) {
	var data=[];
    
	this.setData=function(externalData){
        while(data.length > 0) {
            data.pop();
        }
        data = externalData.slice();
    }

    this.getData=function(){
        return data;
    }

    this.findById=function(a, id) {        
        for (var i = 0; i < a.length; i++) {
            if (a[i].id == id) return a[i];
        }
        return null;
    }
    
    this.getPointsByName=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.points +'getPointByName',params,null);
    }

    this.getPoints=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.points +'getPoints',params,null);
    }

    this.getPointById=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getPointById',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.points + 'save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.points + 'delete',data,httpParams);
    }

    this.getContact=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getContact',params,null);
}
    
    this.getContactById=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getContactById',params,null);
    }
    
    this.saveContact=function(data,httpParams){
        return DataService.save(PATH.points + 'saveContact',data,httpParams);
    }
    
    this.deleteContact=function(data,httpParams){
        return DataService.delete(PATH.points + 'deleteContact',data,httpParams);
    }
    
    this.getClassificationById=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getClassificationById',params,null);
    }
    
    this.saveClasiffication=function(data,httpParams){
        return DataService.save(PATH.points + 'saveClasiffication',data,httpParams);
    }
    
    this.generatePDF=function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);
        return DataService.generatePDF(PATH.points + 'generatePDF',params,null);
    };
    
    this.getCountry = function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getCountry',params,null);
    };    
    this.getRoute = function(paramsData){        var defaultParams = {};        if(!paramsData)            paramsData = defaultParams;        var params = angular.extend({},defaultParams,paramsData);                return DataService.get(PATH.points +'getRoutes',params,null);    };    
    this.getState = function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getState',params,null);
    };
    
    this.getCity = function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getCity',params,null);
    };
    
    this.getAddress = function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.points +'getAddress',params,null);
    };
}