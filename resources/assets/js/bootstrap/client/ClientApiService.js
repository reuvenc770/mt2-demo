mt2App.service( 'ClientApiService' , function( $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Client';
    self.baseApiUrl = '/api/client';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + "/" + id } )
            .then( successCallback );
    }

    self.getAccounts = function ( page , count , sortField , successCallback , failureCallback  ) {
        var sort = { 'field' : sortField , 'desc' : false };

        if (/^\-/.test( sortField ) ) {
            sort.field = sort.field.substring( 1 );
            sort.desc = true;
        }

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , "sort" : sort }
        } ).then( successCallback , failureCallback );
    }

} );