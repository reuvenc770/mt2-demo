mt2App.controller( 'ReportController' , [ 'ReportApiService' , 'formValidationService' , 'modalService' , '$log' , '$window' , '$httpParamSerializer' , function ( ReportApiService ,formValidationService , modalService , $log , $window , $httpParamSerializer ) {
    var self = this;

    self.queryPromise = null;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.reportTotal = 0;
    self.sort = 'name';

    self.reportList = [];

    self.formErrors = [];

    self.newReportName = null;
    self.newReportId = null;
    self.reportSaving = false;
    self.formType = 'Add';
    self.editReportId = 0;

    self.loadReports = function () {
        self.queryPromise = ReportApiService.getReports(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            self.loadReportsSuccessCallback ,
            self.loadReportsFailureCallback
        );
    };

    self.showReportModal = function () {
        $('#createReport').modal( 'show' );
    };

    self.changeReportModal = function ( systemId , name , reportId ) {
        self.newReportName = name;
        self.newReportId = reportId;
        self.editReportId = systemId;
        self.formType = 'Update';

        self.showReportModal();
    };

    self.createReport = function () {
        self.reportSaving = true;
        formValidationService.resetFieldErrors(self);

        if ( self.formType === 'Add' ) {
            ReportApiService.saveReport(
                self.newReportName ,
                self.newReportId ,
                self.createReportSuccessCallback ,
                self.createReportFailureCallback
            );
        } else if ( self.formType === 'Update' ) {
            ReportApiService.updateReport(
                self.editReportId ,
                self.newReportName ,
                self.newReportId ,
                self.createReportSuccessCallback ,
                self.createReportFailureCallback
            );
        }
    };

    self.loadReportsSuccessCallback = function ( response ) {
        self.reportList = response.data.data;
        self.pageCount = response.data.last_page;
        self.reportTotal = response.data.total;
    };

    self.loadReportsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load reports. Please contact support.' );
    }

    self.createReportSuccessCallback = function ( response ) {
        self.reportSaving = false;

        self.loadReports();

        $('#createReport').modal( 'hide' );

        modalService.setModalLabel( 'Success' );
        modalService.setModalBody( 'Successfully Saved Report.' );
        modalService.launchModal();

        self.formType = 'Add';
    };

    self.createReportFailureCallback = function ( response ) {
        self.reportSaving = false;

        formValidationService.loadFieldErrors( self , response );

        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to save report. Please fix errors.' );
        modalService.launchModal();
    };
} ] );
