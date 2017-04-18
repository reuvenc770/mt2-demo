mt2App.service( 'CpmPricingApiService' , [ '$http' , '$log' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/cpm';

    self.getPricings = function ( search , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl
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
