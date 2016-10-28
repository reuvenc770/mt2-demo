mt2App.service( 'ShowinfoApiService' , function ( $http , $log ) {
    var self = this;

    self.apiUrl = '/api/showinfo';
    self.suppressionApiUrl = '/api/suppressionReason';

    self.getRecords = function ( type , id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.apiUrl + '/' + id ,
            "params" : { "recordId" : id , "type" : type }
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
            "data" : { "id" : id , "selectedReason" : reason }
        } ).then( successCallback , failureCallback );
    };
} );
