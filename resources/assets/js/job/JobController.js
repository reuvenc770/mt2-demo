mt2App.controller( 'jobController' , [ '$log' , '$window' , '$location' , '$timeout' , '$interval', 'JobApiService' , function ( $log , $window , $location , $timeout , $interval, JobApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Job' , 'Account', 'Account Name', 'Time Started', 'Time Completed', 'Attempts', "Status"];
    self.entries = [];
    self.rowStatusMap = { 1:"mt2-bg-warn", 2:"mt2-bg-success", 3:"mt2-bg-danger", 4:"mt2-bg-warn", 5:"mt2-bg-warn" };
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
