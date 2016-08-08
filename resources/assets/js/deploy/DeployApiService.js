mt2App.service( 'DeployApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/deploy';
    self.baseEspApiUrl = '/api/espapi/all';
    self.offerSearchUrl = '/api/offer/search/';
    self.domainsApiUrl = '/api/domain/listActiveDomains/';
    self.templateUrl = '/api/mailingtemplate/templates/';
    self.cakeUrl = '/api/deploy/cakeaffiliates/';
    self.pagerApiUrl = '/api/pager/Deploy';

    self.getDeploys = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.getEspAccounts = function (successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.baseEspApiUrl } )
            .then( successCallback , failCallBack );
    };

    self.getMailingDomains = function (espAccountId, domainType, successCallback, failCallback){
        $http( {
            "method" : "GET" ,
            "url" : this.domainsApiUrl + domainType + '/' + espAccountId
        } ).then( successCallback , failCallback );
    };

    self.getTemplates = function (espAccountId, successCallback, failCallback){
        $http( {
            "method" : "GET" ,
            "url" : this.templateUrl + espAccountId
        } ).then( successCallback , failCallback );
    };

    self.getCakeAffiliates = function (successCallback, failCallback){
        $http( {
            "method" : "GET" ,
            "url" : this.cakeUrl
        } ).then( successCallback , failCallback );
    };

    self.getOffersSearch = function ( searchText , successCallback , failCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : this.offerSearchUrl ,
            "params" : {searchTerm : searchText}
        } ).then( successCallback , failCallback );
    };

    self.insertDeploy = function ( deployObject , successCallback , failCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : deployObject
        } ).then( successCallback , failCallback );
    };

});
