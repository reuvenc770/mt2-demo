mt2App.service( 'DomainService' , function ( $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Domain';
    self.baseApiUrl = '/api/domain/';
    self.baseEspApiUrl = '/api/espapi/espAccounts/';
    self.baseProxyUrl = '/api/proxy/active';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    };

    self.getEspAccounts  = function ( espName  , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseEspApiUrl + espName
        } ).then( successCallback , failureCallback );
    };

    self.getDomains = function ( type, espAccountId, successCallback, failureCallback){
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + "listDomains/" + type + "/" + espAccountId
        } ).then( successCallback , failureCallback );
    };

    self.getProxies  = function ( domainType  , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseProxyUrl
        } ).then( successCallback , failureCallback );
    };

    self.getAccounts = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" , 
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.saveNewAccount = function ( newAccount , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } ).then( successCallback , failureCallback );
    };

    self.editAccount = function ( account , successCallback , failureCallback  ) {
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : account
        } ).then( successCallback , failureCallback );
    };
    self.inactiveAccount = function (id, successCallback, failureCallback){
        $http.delete(self.baseApiUrl + id).then(successCallback, failureCallback);
    };

} );
