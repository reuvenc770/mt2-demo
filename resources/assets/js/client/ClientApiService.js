mt2App.service( 'ClientApiService' , [ 'paginationService' , '$http' , '$log' , function( paginationService , $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Client';
    self.baseApiUrl = '/api/client';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + "/" + id } )
            .then( successCallback );
    }

    self.getAccounts = function ( page , count , sortField , successCallback , failureCallback  ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , "sort" : sort }
        } ).then( successCallback , failureCallback );
    }

    self.saveClient = function ( clientData , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : clientData
        } ).then( successCallback , failureCallback );
    };

    self.updateClient = function ( clientData , successCallback , failureCallback ) {
        return $http( {
            "method" : "PUT" ,
            "params" : { "_method" : "PUT" } ,
            "url" : self.baseApiUrl + '/' + clientData.id,
            "data" : clientData
        } ).then( successCallback , failureCallback );
    };
} ] );
