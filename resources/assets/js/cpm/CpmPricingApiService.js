mt2App.service( 'CpmPricingApiService' , [ '$http' , '$log' , 'paginationService' , function ( $http , $log , paginationService ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/CpmPricing';
    self.baseApiUrl = '/api/cpm';

    self.getPricings = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , 'sort' : sort }
        } ).then( successCallback , failureCallback );
    };

    self.create = function ( data , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" , 
            "url" : self.baseApiUrl ,
            "data" : data
        } ).then( successCallback , failureCallback );
    };

    self.update = function ( currentId , data , successCallback , failureCallback ) {
        return $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/' + currentId ,
            "params" : { "_method" : "PUT" } ,
            "data" : data
        } ).then( successCallback , failureCallback );
    };
} ] );
