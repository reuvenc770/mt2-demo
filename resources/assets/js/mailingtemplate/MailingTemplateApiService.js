mt2App.service( 'MailingTemplateApiService' , [ 'paginationService' , '$http' , '$log' , function ( paginationService , $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/mailingtemplate';
    self.baseEspApiUrl = '/api/espapi/allactive';
    self.pagerApiUrl = '/api/pager/MailingTemplate';
    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    };

    self.getEspAccounts = function (successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.baseEspApiUrl } )
            .then( successCallback , failCallBack );
    };

    self.getAccounts = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

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

} ] );
