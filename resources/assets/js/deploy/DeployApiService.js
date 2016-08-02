mt2App.service( 'DeployApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/deploy';
    self.baseEspApiUrl = '/api/espapi/all';
    self.offerSearchUrl = '/api/offer/search/';

    self.getDeploys = function (successCallback, failureCallback) {
        $http({"method": "GET", "url": this.baseApiUrl})
            .then(successCallback, failureCallback);
    };

    self.getEspAccounts = function (successCallback, failCallBack){
        $http( { "method" : "GET" , "url" : this.baseEspApiUrl } )
            .then( successCallback , failCallBack );
    };

    self.getOffersSearch = function ( searchText , successCallback , failCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : this.offerSearchUrl ,
            "params" : {searchTerm : searchText}
        } ).then( successCallback , failCallback );
    };

});
