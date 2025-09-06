function CiudadesDataService(DataService, PATH){
    var data = [];
    
    this.setData = function(externalData){
        while(data.length > 0 ){
            data.pop();
        }
        data = externalData.slice();
    };
    
    this.getData = function(){
        return data;
    };
    
    this.findById=function(a, id) {        
        for (var i = 0; i < a.length; i++) {
            if (a[i].id == id) return a[i];
        }
        return null;
    };
    
    this.getCiudadById = function(paramsData){
        var defaultParams = {page: 1, rowPerPage: 10, sortfield:'name', sortDir: 'asc'};
        if(!paramsData){
            paramsData = defaultParams
        } 
        var params = angular.extend({}, defaultParams, paramsData);
        
        return DataService.get(PATH.ciudades + 'getCiudadById', params, null);
    };
    
    this.getStatesByCountryId = function(paramsData, config){
        var defaultParams = {
            sortField: 'name',
            sortDir: 'asc'
        };
        
        if(!paramsData){
            paramsData = defaultParams;
        }
        
        var params = angular.extend({}, defaultParams, paramsData);
        
        return DataService.get(PATH.ciudades + 'getStatesByCountryId', params, config);
    };
    
    this.getCiudades = function(paramsData, config){
        var defaultParams = {
            page: 1, 
            rowsPerPage: 10,
            sortField: 'name',
            sortDir: 'asc'
        };
        
        if(!paramsData){
            paramsData = defaultParams;
        }
        
        var params = angular.extend({}, defaultParams, paramsData);
        
        return DataService.get(PATH.ciudades + 'getCiudades', params, config);
    };
    
    this.getCountry=function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;
        
        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.addresses +'getCountry',params,null);
    };
    
    this.save=function(data,httpParams){
        return DataService.save(PATH.ciudades + 'save',data,httpParams);
    };
    
    this.delete=function(data,httpParams){
        return DataService.delete(PATH.ciudades + 'delete',data,httpParams);
    };
}