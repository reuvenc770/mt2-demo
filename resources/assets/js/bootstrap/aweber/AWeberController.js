mt2App.controller( 'AWeberController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'AWeberService' , 'modalService'  , function ( $rootScope , $log , $window , $location , AWeberService , modalService  ) {
    var self = this;
    self.$location = $location;

    self.reports = [];


    /**
     * Click Handlers
     */
     self.loadReports = function () {
         AWeberService.getReports(self.getOrphanReportsSuccessCallback);
     };

    /**
     * Callbacks
     */
    self.getOrphanReportsSuccessCallback = function ( response ) {
        self.reports = response.data;
    };

} ] );
