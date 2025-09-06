function LoginController($scope,$cookieStore,Context,user){
    $scope.login=function(){
        
        user.getData('', {usuario:$scope.usuario.nombre,password:$scope.usuario.password})
        .success(function(data, status, headers, config) {
            alert(data);
        })
        .error(function(data, status, headers, config) {
            alert("Error " + data);
        });
        
    }
};