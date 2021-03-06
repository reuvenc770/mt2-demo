mt2App.service( 'EspApiService' , [ 'paginationService' , '$http' , '$log' , function ( paginationService , $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/EspApiAccount';
    self.baseApiUrl = '/api/espapi';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    }

    self.getAccounts = function ( page , count , sortField, successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , 'sort' : sort }
        } ).then( successCallback , failureCallback );
    }

    self.saveNewAccount = function ( newAccount , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } ).then( successCallback , failureCallback );
    }

    self.editAccount = function ( account , successCallback , failureCallback  ) {
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    };
    self.toggleRow = function ( recordId, direction, successCallback, failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : this.baseApiUrl + '/' + recordId,
            "params" : { "direction" : direction }
        } ).then( successCallback , failureCallback );
    };

    self.generateCustomId = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/generatecustomid'
        } ).then( successCallback , failureCallback );
    };

    self.toggleStats  = function ( id , currentStatus , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/toggleStats/' + id ,
            "data" : { "currentStatus" : currentStatus }
        } ).then( successCallback , failureCallback );
    };

    self.toggleSuppression  = function ( id , currentStatus , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/toggleSuppression/' + id ,
            "data" : { "currentStatus" : currentStatus }
        } ).then( successCallback , failureCallback );
    };

    self.activate  = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/activate/' + id
        } ).then( successCallback , failureCallback );
    };

    self.deactivate  = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/deactivate/' + id
        } ).then( successCallback , failureCallback );
    };
} ] );
