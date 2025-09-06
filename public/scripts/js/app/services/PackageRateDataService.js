function PackageRateDataService(DataService,PATH){
    //Array estático de los últimos elementos cargados
    var dataRoute=[];
    var dataPoints=[]

    this.setData=function(externalData,type){
        var arr = type==1 ? dataRoute : dataPoints;
        while(arr.length > 0) {
            arr.pop();
        }
        if(type==1)
            dataRoute = externalData.slice();
        else
            dataPoints = externalData.slice();
    }

    this.getData=function(type){
        return type==1 ? dataRoute : dataPoints;
    }

    this.getPackageRate = function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'date', sortDir: 'desc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.packageRate +'getRates',params,config);
    }

    this.getElementsForRates = function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'date', sortDir: 'desc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.packageRate +'getElementsForRates',params,config);
    }

    this.getRoutesWithRates = function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.packageRate +'getRoutesWithRates',params,config);
    }

    this.getPointsWithRates = function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.packageRate +'getPointsWithRates',params,config);
    }
    
    this.save=function(data,httpParams){
        return DataService.save(PATH.packageRate + 'save',data,httpParams);
    }

    this.getRateByElement= function(paramsData,config){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'id', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.packageRate +'getRateByElement',params,config);
    }
}