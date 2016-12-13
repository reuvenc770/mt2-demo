mt2App.service( 'ReportApiService' , function ( $http ) {
    var self = this;

    self.reportApiUrl = '/api/report';

    self.getRecords = function ( query , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.reportApiUrl ,
            "params" : query
        } ).then( successCallback , failureCallback );
    };
} );
