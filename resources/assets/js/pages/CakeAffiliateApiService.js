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

    self.saveRedirectAndAffiliate = function ( data , newAffiliate , successCallback , failureCallback ) {
        var requestData = angular.copy( data );
        var isUpdate = data.redirect_domain_id || false;
        var requestObj = {
            "method" : ( isUpdate ? "PUT" : "POST" ) ,
            "url" : self.baseApiUrl + ( isUpdate ? '/' + data.redirect_domain_id : '' )
        };


        if ( newAffiliate ) {
            delete requestData.id;
            delete requestData.name;
        }

        if ( isUpdate ) {
            requestObj.params = { "_method" : "PUT" };
            requestObj.data = requestData;
        } else {
            requestObj.params = requestData;
        }

        return $http( requestObj ).then( successCallback , failureCallback );
    };
} ] );
