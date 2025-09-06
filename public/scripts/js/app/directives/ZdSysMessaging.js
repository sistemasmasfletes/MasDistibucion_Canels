function ZdSysMessaging($modal,PARTIALPATH){
    function link($scope,element,attributes){

        var modalCtrl = function($scope, $modalInstance){                        
            
            // *** Modal events ***
            $scope.modalOptions=modalOptions;            
            
            $scope.modalOptions.ok = function (result) {               
                $modalInstance.close({});
            };
            
            $scope.modalOptions.close = function (result) {
                $modalInstance.dismiss('cancel');
            };
            // *** Modal events ***
        } 

        // *** Modal Config ***
        var modalOptions = 
        {   templateUrl:  PARTIALPATH.base +'zdSysMessaging.html',
            controller: ['$scope', '$modalInstance',modalCtrl],            
            size:'sm',
            backdrop:false,
            keyboard:false,
            actionButtonText:"Aceptar"
        }
        
        $scope.showDialog = function(eventType,message){
            modalOptions.type=eventType;
            if(eventType=='forbidden'){                
                modalOptions.headerTitle = "Acceso no autorizado";
                modalOptions.bodyText = 'Usted no cuenta con los permisos necesarios para realizar esta acción.';
            }else if(eventType=='internalServerError'){
                modalOptions.headerTitle = "Error interno del Servidor";
                modalOptions.bodyText = 'Ocurrió un error en el servidor. Favor de contactar al Administrador del Sistema.';
            }else if(eventType=='error'){
                modalOptions.headerTitle = "Error";
                modalOptions.bodyText = message;
            }else if(eventType=='success'){
                modalOptions.headerTitle = "Aviso";
                modalOptions.bodyText = message;
            }

            var modalInstance = $modal.open(modalOptions)        
            
            modalInstance.result.then(function (result) {                
            },function(reason){               
            })
            
        }
        
        $scope.$on('event:auth-forbidden', function() {
            $scope.showDialog('forbidden');
        });

        $scope.$on('event:internalServerError', function() {
            $scope.showDialog('internalServerError');
        });

        $scope.$on('event:alertOnSuccess', function(event,response) {           
            var defaultSuccessMessage = "La acción se realizó satisfactoriamente.";
            var defaultErrorMessage = "Ocurrió un error al realizar la acción.";
            var eventType = "";
           
            if(response.data.error){
                eventType = 'error';
                defaultErrorMessage = response.data.error;
            }else{eventType = 'success';}               
 
            if(response.data.success){
                if(response.data.success==true){
                    eventType = 'success';
                    if(response.data.message)
                        defaultSuccessMessage = response.data.message;                    
                }else{
                    eventType = 'error';
                    if(response.data.message)
                        defaultErrorMessage = response.data.message;
                }
            }

            $scope.showDialog(eventType,eventType=='success' ? defaultSuccessMessage : defaultErrorMessage);


        });

        // *** Modal Config ***        
    }
    return {
        restrict:'A',
        replace:true,
        /*scope:{
            isOpen:'='
        },*/      
        link:link
    }
}