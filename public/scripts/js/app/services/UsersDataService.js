function UsersDataService(DataService,PATH){
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
   
    this.getUsers=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'username', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.users +'getUsers',params,null);
    }

    this.save=function(data,httpParams){
        return DataService.save(PATH.users + 'save',data,httpParams);
    }

    this.delete=function(data,httpParams){
        return DataService.delete(PATH.users + 'delete',data,httpParams);
    }

    this.getDriverbyName = function(params){
        if(!params) params={};
        return DataService.get(PATH.users +'getUserCatalog',params,null);
    }
}