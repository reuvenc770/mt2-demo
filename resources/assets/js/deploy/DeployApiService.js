mt2App.service( 'DeployApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/deploy/';
    self.baseEspApiUrl = '/api/espapi/all';
    self.offerSearchUrl = '/api/offer/search/';
    self.domainsApiUrl = '/api/domain/listActiveDomains/';
    self.templateUrl = '/api/mailingtemplate/templates/';
    self.cakeUrl = '/api/deploy/cakeaffiliates/';
    self.pagerApiUrl = '/api/pager/Deploy';
    self.listProfileUrl = '/api/listprofile/active/';
    self.cfsUrl = '/api/cfs/';

    self.getDeploys = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.getDeploy = function (deployID ,successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.baseApiUrl + deployID } )
            .then( successCallback , failCallBack )
    };

    self.getEspAccounts = function (successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.baseEspApiUrl } )
            .then( successCallback , failCallBack );
    };

    self.getListProfiles = function (successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.listProfileUrl } )
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

    self.getCreatives = function (offerId, successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.cfsUrl + 'creatives/' + offerId } )
            .then( successCallback , failCallBack );
    };

    self.exportCsv = function (selectedRows) {
        return this.baseApiUrl + 'exportcsv/?ids=' + selectedRows.join(',');

    };

    self.getFroms = function (offerId, successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.cfsUrl + 'froms/' + offerId  } )
            .then( successCallback , failCallBack );
    };

    self.getSubjects = function (offerId, successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.cfsUrl + 'subjects/' + offerId  } )
            .then( successCallback , failCallBack );
    };

    self.updateDeploy = function ( deploy , successCallback , failureCallback  ) {
        var request = deploy;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + deploy.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    };


});
