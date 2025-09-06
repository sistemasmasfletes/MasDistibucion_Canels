function ContextService(context){
    var currentUser = {sessionId:null}
    var contextPath = {base:null, js:null, css:null}
     
    this.setUserSession = function(sessionId){
        currentUser.sessionId=sessionId;
    }
    this.getUserSession = function(){
        return currentUser.sessionId;
    }
    
    this.setContextPath = function(base,js,css){
        contextPath.base = base;
        contextPath.js = js;
        contextPath.css = css;
    }
    
    this.getContextPath = function(){
        return contextPath;
    }
}