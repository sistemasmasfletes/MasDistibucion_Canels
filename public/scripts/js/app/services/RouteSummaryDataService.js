function RouteSummaryDataService(DataService,PATH){
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
    
    this.getRouteSummary=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary + 'getRouteSummary',params,null);
    }

    this.getRouteSummaryByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.routeSummary + 'getRouteSummaryCatalog',params,null);
    }
    
    this.getRouteSummaryPoints = function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary + 'getRouteSummaryPoints',params,null);
    }

    this.getRouteSummaryPoints1 = function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary + 'getRouteSummaryPoints1',params,null);
    }
    
    this.getRouteSummaryPointsByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.routeSummary + 'getRouteSummaryPointsCatalog',params,null);
    }
    
    this.getRouteSummaryPackage = function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary +'getRouteSummaryPackage',params,null);
    }
    
     this.getRouteSummaryFavorites = function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary +'getRouteSummaryFavorites',params,null);
    }
    
    this.getActivityPackage = function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary +'getActivityPackage',params,null);
    }
    
    this.getPacksRoute = function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary +'getPacksRoute',params,null);
    }

    this.getRouteSummaryPackageByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.routeSummary + 'getRouteSummaryPackagesCatalog',params,null);
    }
    
    this.getActivityName = function(params){
        if(!params) params={};
        return DataService.get(PATH.routeSummary + 'getActivityName',params,null);
    }
    
    this.getEvidence = function(params){
        if(!params) params={};
        return DataService.get(PATH.routeSummary + 'getEvidence',params,null);
    }
    
    this.getEvidenceByName = function(params){
        if(!params) params={};
        return DataService.get(PATH.routeSummary + 'getEvidence',params,null);
    }
    
    this.save=function(data,httpParams){
        return DataService.save(PATH.routeSummary + 'save',data,httpParams);
    }
    
    this.saveCurrentHour = function (params){
        return DataService.saveCurrentHour(PATH.routeSummary + 'saveCurrentHour',params);
    }
    
    this.saveProgress = function (params){
        return DataService.saveProgress(PATH.routeSummary + 'saveProgress',params);
    }
    
    this.saveHourPoint = function (params){
        return DataService.saveHourPoint(PATH.routeSummary + 'saveHourPoint',params);
    }
    
    this.saveStatus = function (params){
        return DataService.saveStatus(PATH.routeSummary + 'saveStatus',params);
    }
    
    this.getScheduleRouteId = function (params){
        return DataService.saveStatus(PATH.routeSummary + 'getScheduleRouteId',params);
    }

    this.getEvidence=function(paramsData){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!paramsData)
            paramsData = defaultParams

        var params = angular.extend({},defaultParams,paramsData);

        return DataService.get(PATH.routeSummary + 'getEvidence',params,null);
    }
    
    this.getCountPacks=function($stateParams){
        var defaultParams = {page: 1, rowsPerPage: 10, sortField: 'name', sortDir: 'asc'};
        if(!$stateParams)
            $stateParams = defaultParams

        var params = angular.extend({},defaultParams,$stateParams);

        return DataService.get(PATH.routeSummary + 'getCountPacks',params,null);
    }
    
    this.savePointEvidence = function (params){
        return DataService.savePointEvidence(PATH.routeSummary + 'savePointEvidence',params);
    }
    
    this.saveEndHourRoute = function (params){
        return DataService.saveEndHourRoute(PATH.routeSummary + 'saveEndHourRoute',params);
    }
    
    this.saveEvidenceCI = function (params){
        return DataService.saveEvidenceCI(PATH.routeSummary + 'saveEvidenceCI',params);
    }
/********************************PAQUETES NO ENCONTRADOS**********************************/    
    this.packNoFound = function (params){
        return DataService.saveEvidenceCI(PATH.routeSummary + 'packnofound',params);
    }
/*****************************************************************************************/   

    this.addProductToCar = function(params){
        //path: Variable enviada desde PHP que contien la ruta raíz de la aplicación. .../public
        return DataService.addProductToCar(path +'/User/AjaxCart/' + 'addToCart',params,null);  
    }

    this.updateStatusPromotion = function(params){
        return DataService.get(PATH.promotionSchedule + 'updateStatusPromotion',params,null);
    }
}
