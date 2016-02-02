mt2App.service( 'EspApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/esp';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback ,
                function ( response ) {
                    $log.log( response );
                }
            );
    }

    self.getAccounts = function ( successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback ,
                function ( response ) {
                    $log.log( response );
                }
            );
    }

    self.saveNewAccount = function ( newAccount ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } );
    }

    self.editAccount = function ( account ) {
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } );
    }
} );
