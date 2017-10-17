mt2App.service( 'UserApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/user';
    self.baseProfile = '/api/profile';
    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    };

    self.getAccounts = function ( successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback , failureCallback );
    };

    self.saveNewAccount = function ( newAccount , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } ).then( successCallback , failureCallback );
    };

    self.editAccount = function ( account , successCallback , failureCallback  ) {
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    };

    self.updateProfile = function ( account , successCallback , failureCallback  ) {
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseProfile + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    }

    self.deleteAccount = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : this.baseApiUrl + '/' + id
        } ).then( successCallback , failureCallback );
    };
} );
