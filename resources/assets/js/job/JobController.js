mt2App.controller( 'jobController' , [ '$log' , '$window' , '$location' , '$timeout' , 'JobApiService' , function ( $log , $window , $location , $timeout , JobApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Job' , 'Account', 'Account Name', 'Time Started', 'Time Completed', 'Attempts', "Status"];
    self.entries = [];
    self.rowStatusMap = {0:"info", 1:"active", 2:"success", 3:"danger"};
    self.GlythMap  = { 1:"glyphicon-forward", 2:"glyphicon-remove-sign", 3:"glyphicon-remove-sign"};
    self.loadJobs = function () {
        JobApiService.getJobs( self.loadJobsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.loadJobsSuccessCallback = function ( response ) {
        self.entries = response.data;
    };


} ] );
