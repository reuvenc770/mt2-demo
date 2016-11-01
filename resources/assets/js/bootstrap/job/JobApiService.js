mt2App.service( 'JobApiService' , function ( $http , $interval ) {
    var self = this;

    self.baseApiUrl = '/api/jobEntry';
    self.getJobs = function ( successCallback , failureCallback ) {
        self.httpget = $http( { "method" : "GET" , "url" : self.baseApiUrl } )
            .then( successCallback , failureCallback );
    }
} );
