mt2App.controller( 'ShowinfoController' , [ 'ShowinfoApiService' , '$mdToast' , '$log' , '$window' , function ( ShowinfoApiService , $mdToast , $log , $window ) {
    var self = this;
    self.api = ShowinfoApiService;

    self.isLoading = false;

    self.recordId = null;
    self.records = {};

    self.suppressionReasons = [];
    self.selectedReason = '';

    /**
     * Event Handlers
     */

    self.loadData = function ( $event ) {
        $event.preventDefault();

        self.isLoading = true;

        self.api.getRecords( self.getType() , self.recordId , self.loadDataSuccessCallback , self.loadDataFailureCallback );
    };

    self.suppressRecord = function ( $event ) {
        $event.preventDefault();

        self.api.suppressRecord( self.recordId , self.selectedReason , self.suppressRecordSuccessCallback , self.suppressRecordFailureCallback );
    }

    /**
     * Helpers
     */

    self.getType = function () {
        var re = /\d{1,}/;

        return ( re.exec( self.recordId ) ? 'eid' : 'email' );
    }

    self.loadReasons = function () {
        self.api.getSuppressionReasons( self.loadReasonsSuccessCallback , self.loadReasonsFailureCallback );
    };

    /**
     * Callbacks
     */

    self.loadDataSuccessCallback = function ( response ) {
        self.records = response.data;

        $mdToast.showSimple( 'Successfully Loaded Record' );

        self.isLoading = false;
    };

    self.loadDataFailureCallback = function ( response ) {
        $mdToast.showSimple( 'Failed to Load Record' );

        self.isLoading = false;
    };

    self.loadReasonsSuccessCallback = function ( response ) {
        self.suppressionReasons = response.data;
    };

    self.loadReasonsFailureCallback = function ( response ) {
        $mdToast.showSimple( 'Failed to Load Suppression Reasons' );
    };

    self.suppressRecordSuccessCallback = function ( response ) {};
    self.suppressRecordFailureCallback = function ( response ) {};
} ] );
