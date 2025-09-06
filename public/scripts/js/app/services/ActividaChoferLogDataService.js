function ActividaChoferLogDataService(DataService,PATH){
    var data=[];
    
    this.setData=function(externalData){
        while(data.length > 0) {
            data.pop();
        }
        data = externalData.slice();
    };

    this.getData=function(){
        return data;
    };
    
    this.save=function(data,httpParams)
    {
        return DataService.save(PATH.actividaChoferLog + '/save', data, httpParams);
    };

    
    this.getActividaChoferLog=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'fecha', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams;

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.actividaChoferLog +'/getActividaChoferLog',params,null);
    };

    

    this.getActividaChoferLogByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.actividaChoferLog + '/getActividaChoferLogCatalog',params,null);
    };
}