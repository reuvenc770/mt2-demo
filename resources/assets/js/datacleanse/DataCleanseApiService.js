mt2App.service( 'DataCleanseApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/datacleanse';
    self.advertiserApiUrl = '/api/mt1/advertiser';
    self.countryApiUrl = '/api/mt1/country';
    self.categoryApiUrl = '/api/mt1/offercategory';

    self.getAll = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl ,
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    }

    self.save = function ( data , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "params" : data
        } ).then( successCallback , failureCallback );
    };

    self.getAdvertisers = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.advertiserApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.getCountries = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.countryApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.getOfferCategories = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.categoryApiUrl
        } ).then( successCallback , failureCallback );
    };
} );
