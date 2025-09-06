function AddressDataService(DataService,PATH) {
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
    
    this.getAddInformation=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.addresses +'getAddInformation',params,null);
    }
    
    this.getAddressById=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.addresses +'getAddressById',params,null);
    }
    
    this.save=function(data,httpParams){
        return DataService.save(PATH.addresses + 'save',data,httpParams);
    };
    
    this.delete=function(data,httpParams){
        return DataService.delete(PATH.addresses + 'delete',data,httpParams);
    };
    
    this.getCountry=function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;
        
        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.addresses +'getCountry',params,null);
    };
    
    this.getState=function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;
        
        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.addresses +'getState',params,null);
    };
    
    this.getCity=function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;
        
        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.addresses +'getCity',params,null);
    };
    
    this.getUserRole = function(){
        return DataService.get(PATH.users + 'getUserRole', {}, {});
    }
}