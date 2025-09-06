function JQGridService($state,$rootScope){
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
        shrinkToFit: true,
        forceFit:true,
        rowNum: 30,
        rowList: [10, 20, 30, 50, 100],
        viewrecords: true,
        gridview: true,
        autoencode: true,
        altRows: true,
        loadtext:"",
        loadui:'disable',
        prmNames:{page:"page", rows:"rowsPerPage", sort:"sortField", order:"sortDir"},
        serializeGridData: function(postData) {
            return JSON.stringify(postData);
        },
        loadError:function(xhr,status,error){
            switch(xhr.status){
                case 401:
                    $rootScope.$broadcast('event:auth-loginRequired', xhr);
                    break;
                case 403:                    
                    $rootScope.$broadcast('event:auth-forbidden', xhr);
                    break;
                case 500:
                    $rootScope.$broadcast('event:internalServerError', xhr);
                    break;
            }
        }
    }

    this.config = function(config, jsreader){        
        var gridConfig = angular.extend({},
                                angular.extend({},
                                    gridDefaults,
                                    {jsonReader: angular.extend({},jsonReader,jsreader)}
                                )
                                ,config);
        return gridConfig;
    }

 
    this.resize = function(gridId, parent){
        angular.element(window).on('resize', function(event, ui) {
            var parentWidth=0;
            if(parent)
                parentWidth = parent.width();
            else
                parentWidth = angular.element("#gbox_"+gridId).parent().parent().parent().width();

            var currentWidth = angular.element("#gbox_"+gridId).width();
            var w = parentWidth - 1;
            if (Math.abs(w - currentWidth) > 2)
                angular.element("#"+gridId).setGridWidth(w);
        }).trigger('resize');
    }
}