mt2App.service( 'ShowinfoApiService' , function ( $http , $log ) {
    var self = this;

    self.apiUrl = '/api/showinfo';
    self.suppressionApiUrl = '/api/mt1/suppressionReason';

    self.getRecords = function ( type , id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.apiUrl + '/' + id ,
            "data" : { "id" : id , "type" : type }
        } ).then( successCallback , failureCallback );
    };

    self.getSuppressionReasons = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.suppressionApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.suppressRecord = function ( id , reason , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.apiUrl ,
            "data" : { "id" : id , "reason" : reason }
        } ).then( successCallback , failureCallback );
    };
} );
