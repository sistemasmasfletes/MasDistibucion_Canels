function ZdFilter($modal,PARTIALPATH){
    function link($scope,element,attributes){

        var modalCtrl = function($scope, $modalInstance,customFilter){                        
            
            // *** Modal events ***
            $scope.modalOptions=modalOptions;
            $scope.customFilter=customFilter;

            $scope.modalOptions.ok = function (result) {
                var cFilter={}
                for(var i=0;i<$scope.customFilter.length;i++){
                    //if($scope.customFilter[i].value)
                        cFilter[$scope.customFilter[i].name]=$scope.customFilter[i].value
                }
                $modalInstance.close(cFilter);
            };
            
            $scope.modalOptions.close = function (result) {
                $modalInstance.dismiss('cancel');
            };

            $scope.modalOptions.clearFilter = function(){
                for(var i=0;i<$scope.customFilter.length;i++){
                    $scope.customFilter[i].value = null;
                }
                $modalInstance.close({});
            }
            // *** Modal events ***


            //El objeto datepickerPopupConfig se puede obtener mediante DI. Se omite en esta parte
            //De esta manera se pueden configurar las propiedades del datepicker popup        
            // *** DatepickerOptions ***
            var dateOptions = {
                format:'dd/MM/yyyy',
                options: {
                    formatYear: 'yyyy',
                    startingDay: 1                    
                },            
                opened:false,
                showWeeks:false
            }
           
            $scope.popupDatepicker=function($event,indexFilter){
                $event.preventDefault();
                $event.stopPropagation();
                if($scope.customFilter.length>0&&indexFilter>=0)
                    $scope.customFilter[indexFilter].dateOptions.opened=true;
            }

            //Agregar al filtro de tipo date las opciones del datepicker, con la finalidad de que cada control dateṕicker
            //(si es que hay varios) tenga su propia configuración.
            angular.forEach($scope.customFilter,function(key,value,obj){            
                if(key.type=="date")
                    key.dateOptions = angular.extend({},dateOptions);                    
            },[]);
           
           // *** DatepickerOptions ***
        } 

        // *** Modal Config ***
        var modalOptions = 
        {   templateUrl:  PARTIALPATH.filter +'zdFilter.html',
            controller: ['$scope', '$modalInstance','customFilter',modalCtrl],
            resolve :{                
                customFilter: function(){
                    return $scope.customFilter
                }
            },
            size:'md',
            backdrop:false
        }
        
        $scope.showFilter = function(){
            var modalInstance = $modal.open(modalOptions)        
            
            modalInstance.result.then(function (result) {
                $scope.applyFilter({filter:result});
                $scope.isOpen = false;
            },function(reason){
               $scope.isOpen = false;
            })
            
        }      
        
        $scope.$watch('isOpen', function(value) {
            if(value)
                $scope.showFilter();
            
        });

        // *** Modal Config ***        
    }
    return {
        restrict:'EA',
        replace:true,
        scope:{
            customFilter:'=',
            isOpen:'=',
            applyFilter:'&'
        },      
        link:link
    }
}