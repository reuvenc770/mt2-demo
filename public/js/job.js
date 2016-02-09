mt2App.controller( 'jobController' , [ '$log' , '$window' , '$location' , '$timeout' , 'JobApiService' , function ( $log , $window , $location , $timeout , JobApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Job' , 'Account', 'Account Name', 'Time Started', 'Time Completed', 'Attempts', "Status"];
    self.entries = [];
    self.classes = ["info","active","success","danger"];
    self.glyths  = ["","forward","remove-sign","ok-sign"];
    self.loadJobs = function () {
        JobApiService.getJobs( self.loadJobsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.loadJobsSuccessCallback = function ( response ) {
        self.entries = response.data;
    };


} ] );

mt2App.service( 'JobApiService' , function ( $http , $interval ) {
    var self = this;

    self.baseApiUrl = '/api/jobEntry';
    self.getJobs = function ( successCallback , failureCallback ) {
        self.httpget = $http( { "method" : "GET" , "url" : self.baseApiUrl } )
            .then( successCallback , failureCallback );
        //call on page load;
        $interval(function() {
            $http( { "method" : "GET" , "url" : self.baseApiUrl } )
                .then( successCallback , failureCallback );
        }, 10000);

    }
} );

//# sourceMappingURL=job.js.map
