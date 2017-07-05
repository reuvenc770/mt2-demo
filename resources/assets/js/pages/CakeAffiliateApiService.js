mt2App.service( 'CakeAffiliateApiService' , [ '$http' , 'paginationService' , function ( $http , paginationService ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/CakeAffiliate';
    self.baseApiUrl = '/api/affiliates';

    self.loadAffiliateRedirectDomains = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , 'sort' : sort }
        } ).then( successCallback , failureCallback );
    };

    self.loadAffiliates = function ( successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/affiliateslist'
        } ).then( successCallback , failureCallback );
    };

    self.loadOfferTypes = function ( successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/offertypelist'
        } ).then( successCallback , failureCallback );
    };

    self.saveRedirectAndAffiliate = function ( data , successCallback , failureCallback ) {
        var isUpdate = data.redirect_domain_id || false;
        var requestObj = {
            "method" : ( isUpdate ? "PUT" : "POST" ) ,
            "url" : self.baseApiUrl + ( isUpdate ? '/' + data.redirect_domain_id : '' )
        };

        if ( isUpdate ) {
            requestObj.params = { "_method" : "PUT" };
            requestObj.data = data;
        } else {
            requestObj.params = { "data" : data };
        }

        return $http( requestObj ).then( successCallback , failureCallback );
    };
} ] );
