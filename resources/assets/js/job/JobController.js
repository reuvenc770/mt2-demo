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
