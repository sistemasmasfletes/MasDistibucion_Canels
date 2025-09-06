function RoutesDataService($http,PATH) {
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
    
    this.getRoutes=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return $http.post(PATH.routes +'getRoutes',params,null);
    }

    this.getRoutesByName=function(paramsData){        
        if(!paramsData)
            paramsData = {'param1':''};        

        return $http.post(PATH.routes +'getRouteCatalog',paramsData,null);
    }

    this.save=function(data,httpParams){
        return $http.post(PATH.routes + 'save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return $http.post(PATH.routes + 'delete',data,httpParams);
    }

    this.toggleOpenClose = function(data,httpParams){
        return $http.post(PATH.routes +'routeToggleOpenClose',data,httpParams);
    }

    this.routePointChangeOrder = function(data,httpParams){
        return $http.post(PATH.routes +'routePointChangeOrder',data,httpParams);
    }

    this.routePointDelete = function(data,httpParams){
        return $http.post(PATH.routes +'routePointDelete',data,httpParams);
    }
}