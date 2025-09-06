function PromotionDataService(DataService,PATH){
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

    this.getPromotion = function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'DESC'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.promotion +'getPromotion',params,config);
    }

    this.getPromotionDetail = function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'ASC'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.promotion +'getPromotionDetail',params,config);
    }

    this.delete = function(paramsData,configData){
        return DataService.delete(PATH.promotion +'delete',paramsData,configData);
    }

    this.getPromotionCosting=function(params,config){
        return DataService.get(PATH.promotion +'getPromotionCosting',params,config);
    }
    
    this.setSendPromo = function(paramsData,configData){
        return DataService.sendPromo(PATH.promotion +'sendPromo',paramsData,configData);
    }

    this.getRoutesList=function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},paramsData);

        return DataService.get(PATH.promotion +'getRoutes',params,configData);
        //return $http.post(urlGetBranchesUser, paramsData, httpParams)
    }

    this.getScheduledList=function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},paramsData);

        return DataService.get(PATH.promotion +'getSchedules',params,configData);
        //return $http.post(urlGetBranchesUser, paramsData, httpParams)
    }    
   
    this.getClientsList=function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},paramsData);

        return DataService.get(PATH.promotion +'getClients',params,configData);
        //return $http.post(urlGetBranchesUser, paramsData, httpParams)
    }
    
    this.getCategoriesList=function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},paramsData);

        return DataService.get(PATH.promotion +'getCategories',params,configData);
        //return $http.post(urlGetBranchesUser, paramsData, httpParams)
    }
    
    this.getBranchesUser=function(paramsData,configData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},paramsData);

        return DataService.get(PATH.promotion +'getBranchesUser',params,configData);
        //return $http.post(urlGetBranchesUser, paramsData, httpParams)
    }
}