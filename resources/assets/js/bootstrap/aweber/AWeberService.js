mt2App.service( 'AWeberService' , function ( $http , $log ) {
    var self = this;
    
    self.baseApiUrl = '/api/tools/getunmappedreports/';

    self.getReports = function (successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback );
    };

   
} );
