function ngJqgrid($window){
    return {
        restrict: 'E',
        replace: true,
        scope: {
            config: '=',
            data:   '=?',
            insert: '=?',
            api:    '=?',
            vapi: '=?'
        },
        link: function (scope, element, attrs) {
            var table, div;
            scope.$watch('config', function (value) {
                 
                element.children().empty();
                table = angular.element('<table id="' + attrs.gridid + '" style="width:100%"></table>');
                element.append(table);
                if (attrs.pagerid) {                    
                    value.pager = '#' + attrs.pagerid;
                    var pager = angular.element(value.pager);
                    if (pager.length == 0) {
                        div = angular.element('<div id="' + attrs.pagerid + '"></div>');
                        element.append(div);
                    }
                }
                
                $(table).jqGrid(value);
                scope.vapi = function() {
                    var args = Array.prototype.slice.call(arguments,0);
                    return table.jqGrid.apply(table, args);
                };
                
                scope.api = {
                    insert: function(rows) {
                        if (rows) {
                            for (var i = 0; i < rows.length; i++) {
                                scope.data.push(rows[i]);
                            }
                            table.jqGrid('setGridParam', { data: scope.data })
                                 .trigger('reloadGrid');
                        }
                    },
                    clear: function() {
                        scope.data.length = 0;
                        table.jqGrid('clearGridData', { data: scope.data })
                            .trigger('reloadGrid');
                    },
                    refresh: function() {
                        table
                            .jqGrid('clearGridData')
                            .jqGrid('setGridParam', { data: scope.data })
                            .trigger('reloadGrid');
                    }
                };
            });
            scope.$watch('data', function (value) {               
                $(table).jqGrid('setGridParam', { data: value })
                     .trigger('reloadGrid')
                ;
            });
        }
    };
}