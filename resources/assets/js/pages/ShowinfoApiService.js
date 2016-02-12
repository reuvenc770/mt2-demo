mt2App.service( 'ShowinfoApiService' , function ( $http , $log ) {
    var self = this;

    self.apiUrl = '/newcgi-bin/show_info_2.cgi';
    self.suppressionApiUrl = '/api/mt1/suppressionReason';

    self.getRecords = function ( type , id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.apiUrl ,
            "data" : { "type" : type , "id" : id }
        } ).then( successCallback , failureCallback );
    };

    self.getSuppressionReasons = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.suppressionApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.suppressRecord = function ( id , reason ) {
        //Need to find out where to send this to.
    };
} );
