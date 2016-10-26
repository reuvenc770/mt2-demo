mt2App.controller( 'jobController' , [ '$log' , '$window' , '$location' , '$timeout' , '$interval', 'JobApiService' , function ( $log , $window , $location , $timeout , $interval, JobApiService ) {
    var self = this;
    self.$location = $location;

    self.entries = [];
    self.rowStatusMap = { 1:"bg-warning", 2:"bg-success", 3:"bg-danger", 4:"bg-warning", 5:"bg-warning" };
    self.loadJobs = function () {
        JobApiService.getJobs( self.loadJobsSuccessCallback , self.loadAccountsFailureCallback );
        $interval(function() {
            JobApiService.getJobs( self.loadJobsSuccessCallback , self.loadAccountsFailureCallback );
        }, 10000);
    };

    self.loadJobsSuccessCallback = function ( response ) {
        self.entries = response.data;
    };


} ] );
