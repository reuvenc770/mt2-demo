mt2App.service( 'DomainService' , function ( $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Domain';
    self.baseApiUrl = '/api/domain';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
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
    }
} );
