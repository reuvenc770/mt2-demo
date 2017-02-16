mt2App.service( 'DeployApiService' , [ 'paginationService' , '$http' , function ( paginationService , $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/deploy/';
    self.baseEspApiUrl = '/api/espapi/allactive';
    self.offerSearchUrl = '/api/offer/search/';
    self.domainsApiUrl = '/api/domain/listActiveDomains/';
    self.templateUrl = '/api/mailingtemplate/templates/';
    self.cakeUrl = '/api/deploy/cakeaffiliates/';
    self.pagerApiUrl = '/api/pager/Deploy';
    self.listProfileUrl = '/api/listprofile/listcombine';
    self.cfsUrl = '/api/cfs/';


    self.getDeploys = function ( page , count , sortField , type, data, successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count, "sort" : sort , "type": type, "data" : data }
        } ).then( successCallback , failureCallback );
    };

    self.searchDeploys = function ( count , sortField , data, successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : 1 , "count" : count, "sort" : sort , "data" : data }
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
    self.massUpload = function ( deploy , successCallback , failureCallback  ) {
        var request = deploy;
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl + "massupload" ,
            "data" : request
        } ).then( successCallback , failureCallback );
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

    self.validateDeploy = function ( deploy , successCallback , failureCallback  ) {
        var request = deploy;
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl + "validatedeploys" ,
            "data" : {filename: request}
        } ).then( successCallback , failureCallback );
    };

    self.checkForPackages = function (successCallback,failCallBack){
        $http( { "method" : "GET" , "url" : this.baseApiUrl + 'check' } )
            .then( successCallback , failCallBack );
    };

    self.deployPackages = function (packages, userName, successCallback, failCallBack){
        var request = packages;
        var responseType = packages.length > 1 ? "json" : "arraybuffer";
        $http( {
            "method" : "POST" ,
            "responseType" : responseType,
            "url" : this.baseApiUrl + "package/create?username=" + userName ,
            "data" : request
        } ).then( successCallback , failCallBack );
    };

    self.copyToFuture = function ( deployIds , date, successCallback , failureCallback  ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl + "copytofuture" ,
            "data" : {deploy_ids: deployIds, "future_date": date}
        } ).then( successCallback , failureCallback );
    };
} ] );

