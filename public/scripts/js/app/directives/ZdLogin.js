function ZdLogin($modal,PARTIALPATH,AuthService,authService){
    function link($scope,element,attributes){

        var modalCtrl = function($scope, $modalInstance){                        
            
            // *** Modal events ***
            $scope.modalOptions=modalOptions;            
            $scope.usuario = {};
            $scope.login = function(){
                $scope.loginError = false;
                $scope.loading = true;
                var payload = {user: $scope.usuario.user, password: $scope.usuario.password};
                AuthService.login(payload).then(function(data,xhr,headers,config){
                    $scope.loading = false;
                    authService.loginConfirmed();
                    $modalInstance.close(data);
                },
                function(){
                    $scope.loading = false;
                    $scope.loginError = true;
                }
                );
            }
            $scope.modalOptions.ok = function (result) {               
                $modalInstance.close({});
            };
            
            $scope.modalOptions.close = function (result) {
                $modalInstance.dismiss('cancel');
            };

            $scope.alert = { type: 'danger', msg: 'Login incorrecto. Verifique sus datos e intente de nuevo' };

            // *** Modal events ***

        } 

        // *** Modal Config ***
        var modalOptions = 
        {   templateUrl:  PARTIALPATH.base +'login.html',
            controller: ['$scope', '$modalInstance',modalCtrl],            
            size:'sm',
            backdrop:'static',
            keyboard:false
        }
        
        $scope.showLoginDialog = function(){
            var modalInstance = $modal.open(modalOptions)        
            
            modalInstance.result.then(function (result) {
                $scope.isOpen = false;
            },function(reason){
               $scope.isOpen = false;
            })
            
        }      
        
        $scope.$on('event:auth-loginRequired', function() {
            $scope.showLoginDialog();
        });

        $scope.$on('event:auth-loginConfirmed', function() {
            
        });
        // *** Modal Config ***        
    }
    return {
        restrict:'A',
        replace:true,
        scope:{
            isOpen:'='
        },      
        link:link
    }
}