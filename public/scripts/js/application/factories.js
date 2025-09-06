function dataFactory($http) {
    var factory = {};
    
    factory.delete = function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    }
    
    factory.save = function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    }
    
    factory.getData = function(urlPath, paramsData, httpParams) {
        return $http.post(urlPath, paramsData, httpParams);
    }
    
    return factory;
}

function utilsFactory(){
    factory = {}
    
    factory.findById = function findById(arrayObject, fieldIdName, fieldId) {
        for (var i = 0; i < arrayObject.length; i++) {
            if (arrayObject[i][fieldIdName] == fieldId)
                return arrayObject[i];
        }
        return null;
    }
    
    factory.getPropFromArray =function(arrayObject, fieldIdName, fieldId, fieldSearch){
        for (var i = 0; i < arrayObject.length; i++) {
            if (arrayObject[i][fieldIdName] == fieldId)
                return arrayObject[i][fieldSearch];
        }
        return null;
    }
    
    factory.getDateFromString = function(strDate){
                                               //0123456789012345678
        //Se asume que la fecha viene en formato yyyy-mm-dd hh:MM:ss
        var y,m,d,h,mm,s;
       
        y = parseInt(strDate.substring(0,4));
        m = parseInt(strDate.substring(5,7))-1;
        d = parseInt(strDate.substring(8,10));
        h = parseInt(strDate.substring(11,13));
        mm = parseInt(strDate.substring(14,16));
        s = parseInt(strDate.substring(17));
        return new Date(y,m,d,h,mm,s);
    }
    
    factory.getTimeFromString = function(strDate){
        //Se asume que la fecha viene en formato yyyy-mm-dd hh:MM:ss
        var h,m,s;
        
        y = parseInt(strDate.substring(0,4));
        m = parseInt(strDate.substring(5,7))-1;
        d = parseInt(strDate.substring(8));
        return new Date(y,m,d);
    }
    
    return factory;    
}

function gridConfigFactory(){
    
    var factory = {};
    var jsonReader = {
        id:"id",
        repeatitems: false,
        root: function(data) {
                return data[0];
        },
        page: function(data) {
                return data[1][0].page;
        },
        total: function(data) {
                return data[1][0].totalpages;
        },
        records: function(data) {
                return data[1][0].records;
        }
    }
    
    var gridDefaults = {
        datatype:"json",
        mtype: "POST",        
        height: 334,
        //width:'100%',
        shrinkToFit: true,
        forceFit:true,
        rowNum: 10,
        rowList: [10, 20, 30],
        viewrecords: true,
        gridview: true,
        autoencode: true,
        altRows: true,
        loadtext:"",
        prmNames:{page:"page", rows:"rowsPerPage", sort:"sortField", order:"sortDir"},
        serializeGridData: function(postData) {
            return JSON.stringify(postData);
        }
    }
    
    factory.config = function(config, jsreader){     
        var ob = angular.extend(angular.extend(gridDefaults,{jsonReader: angular.extend(jsonReader,jsreader)}),config);
        return ob;
    }
    
     return factory;
}