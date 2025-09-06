function EstadosDataService(DataService, PATH){
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
    
    this.getEstadoById = function(paramsData){
        var defaultParams = {page: 1, rowPerPage: 10, sortfield:'name', sortDir: 'asc'};
        if(!paramsData){
            paramsData = defaultParams
        } 
        var params = angular.extend({}, defaultParams, paramsData);
        
        return DataService.get(PATH.estados + 'getEstadoById', params, null);
    };
    
    this.getEstados = function(paramsData){
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
        
        return DataService.get(PATH.estados + 'getEstados', params);
    };
    
    this.getCountry=function(paramsData){
        var defaultParams = {};
        if(!paramsData)
            paramsData = defaultParams;
        
        var params = angular.extend({},defaultParams,paramsData);
        
        return DataService.get(PATH.addresses +'getCountry',params,null);
    };
    
    this.save=function(data,httpParams){
        return DataService.save(PATH.estados + 'save',data,httpParams);
    };
    
    this.delete=function(data,httpParams){
        return DataService.delete(PATH.estados + 'delete',data,httpParams);
    };
}