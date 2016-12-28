mt2App.controller( 'AWeberController' , [ '$rootScope' , '$log' , '$window' , '$location' , 'AWeberService' , 'modalService'  , function ( $rootScope , $log , $window , $location , AWeberService , modalService  ) {
    var self = this;
    self.$location = $location;
    self.currentMappings = [];
    self.reports = [];


    /**
     * Click Handlers
     */
     self.loadReports = function () {
         AWeberService.getReports(self.getOrphanReportsSuccessCallback);
     };
    self.convertReport = function (reportId,deployId) {
        AWeberService.convertReport(reportId, deployId, self.getConvertReportSuccessCallback,self.getConvertReportFailCallback);
    };

    /**
     * Callbacks
     */
    self.getOrphanReportsSuccessCallback = function ( response ) {
        self.reports = response.data;
    };

    self.getConvertReportSuccessCallback = function ( response ) {
        $location.url( '/tools/awebermapping' );
        $window.location.href = '/tools/awebermapping';
    };

    self.getConvertReportFailCallback = function ( response ) {
        self.reports = response.data;
    };
    }]);
