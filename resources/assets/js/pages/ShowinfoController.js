mt2App.controller( 'ShowinfoController' , [ 'ShowinfoApiService' , '$log' , '$window' , 'formValidationService' , 'modalService' , function ( ShowinfoApiService , $log , $window , formValidationService , modalService ) {
    var self = this;
    self.api = ShowinfoApiService;

    self.isLoading = false;
    self.isSuppressing = false;

    self.recordId = null;
    self.records = {};
    self.suppression = {};
    self.formErrors = {};

    self.suppressionReasons = [];
    self.selectedReason = '';

    /**
     * Event Handlers
     */

    self.loadData = function () {
        self.isLoading = true;
        formValidationService.resetFieldErrors(self);

        var records = self.recordId.replace(/(\r\n|\r|\n)/g, ',');
        self.api.getRecords( self.getType() , records , self.loadDataSuccessCallback , self.loadDataFailureCallback );
    };

    self.suppressRecord = function () {
        self.isSuppressing = true;
        formValidationService.resetFieldErrors(self);

        self.api.suppressRecord( self.recordId , self.selectedReason , self.suppressRecordSuccessCallback , self.suppressRecordFailureCallback );
    }

    /**
     * Helpers
     */

    self.getType = function () {
        var re = /\@/;

        return ( re.exec( self.recordId ) ? 'email' : 'eid' );
    }

    self.loadReasons = function () {
        self.api.getSuppressionReasons( self.loadReasonsSuccessCallback , self.loadReasonsFailureCallback );
    };

    /**
     * Callbacks
     */

    self.loadDataSuccessCallback = function ( response ) {
        self.isLoading = false;

        self.records = response.data.data;
        self.suppression = response.data.suppression;
        self.espSuppression = response.data.espSuppression;
        if(0 === self.records.length && 0 === self.espSuppression.length){
            modalService.setModalLabel('Error');
            modalService.setModalBody('Record had no information.');
            modalService.launchModal();
        }

    };

    self.loadDataFailureCallback = function ( response ) {
        self.isLoading = false;

        formValidationService.loadFieldErrors( self , response);
    };

    self.loadReasonsSuccessCallback = function ( response ) {
        self.suppressionReasons = response.data;
    };

    self.loadReasonsFailureCallback = function ( response ) {
            modalService.setModalLabel('Error');
            modalService.setModalBody('Failed to load suppression reasons.');
            modalService.launchModal();
    };

    self.suppressRecordSuccessCallback = function ( response ) {
        self.isSuppressing = false;

        modalService.setModalLabel('Success');
        modalService.setModalBody( response.data.message );
        modalService.launchModal();
    };
    self.suppressRecordFailureCallback = function ( response ) {
        self.isSuppressing = false;

        formValidationService.loadFieldErrors( self , response);
    };
} ] );
