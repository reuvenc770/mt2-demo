mt2App.service( 'EspService' , function ( $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Esp';
    self.baseApiUrl = '/api/esp';
    self.mappingUrl = '/api/esp/mappings/';

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
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    };

    self.getMapping = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.mappingUrl + id
        } ).then( successCallback , failureCallback );
    };

    self.updateMapping = function (id, mappings, successCallback , failureCallback  ) {
        $http( {
            "method" : "PUT" ,
            "url" : this.mappingUrl +  id ,
            "data" : {mappings:mappings}
        } ).then( successCallback , failureCallback );
    }

    self.processFile = function ( fileData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.mappingUrl + 'process' ,
            "data" : fileData
        } ).then( successCallback , failureCallback );
    };
} );
