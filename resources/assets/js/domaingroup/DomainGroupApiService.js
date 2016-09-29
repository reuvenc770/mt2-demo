mt2App.service( 'DomainGroupApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/ispgroup';
    self.emailDomainUrl = '/api/ispgroup/domains';
    self.pagerApiUrl = '/api/pager/DomainGroup';
    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    };

    self.getAccounts = function ( page , count , sortField , successCallback , failureCallback ) {
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
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    };


} );
