function HttpInterceptor($rootScope,$q){
    return {
        'request':function(config){
            config.headers["X-Requested-With"]='XMLHttpRequest';
            return config;
        },

        'response':function(response){
            if(response.config.alertOnSuccess)
                $rootScope.$broadcast('event:alertOnSuccess',response);

            $rootScope.$broadcast({
                200: 'event:ok'
            }[response.status],response);
            return response;
        },

        'responseError': function (response) {            
            $rootScope.$broadcast({
                404: 'event:notFound',
                419: 'event:auth-sessionTimeout',
                440: 'event:auth-sessionTimeout',
                500: 'event:internalServerError'                
            }[response.status], response);
            return $q.reject(response);
        }
                    
    }
}