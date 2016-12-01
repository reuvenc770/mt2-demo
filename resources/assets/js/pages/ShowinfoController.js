mt2App.controller( 'ShowinfoController' , [ 'ShowinfoApiService' , '$mdToast' , '$log' , '$window' , function ( ShowinfoApiService , $mdToast , $log , $window ) {
    var self = this;
    self.api = ShowinfoApiService;

    self.isLoading = false;

    self.recordId = null;
    self.records = {};
    self.suppression = {};

    self.suppressionReasons = [];
    self.selectedReason = '';

    /**
     * Event Handlers
     */

    self.loadData = function ( $event , recordForm ) {
        $event.preventDefault();

        if ( recordForm.recordId.$valid ) {
            self.isLoading = true;

            self.api.getRecords( self.getType() , self.recordId , self.loadDataSuccessCallback , self.loadDataFailureCallback );
        } else {
            $mdToast.showSimple( 'Please correct form errors and try again.' );
        }
    };

    self.suppressRecord = function ( $event , suppressionForm ) {
        $event.preventDefault();

        if ( suppressionForm.suppressionReason.$valid ) {
            self.api.suppressRecord( self.recordId , self.selectedReason , self.suppressRecordSuccessCallback , self.suppressRecordFailureCallback );
        } else {
            $mdToast.showSimple( 'Please correct form errors and try again.' );
        }

    }

    /**
     * Helpers
     */

    self.getType = function () {
        var re = /^\d{1,}$/;

        return ( re.exec( self.recordId ) ? 'eid' : 'email' );
    }

    self.loadReasons = function () {
        self.api.getSuppressionReasons( self.loadReasonsSuccessCallback , self.loadReasonsFailureCallback );
    };

    /**
     * Callbacks
     */

    self.loadDataSuccessCallback = function ( response ) {

            self.records = response.data.data;
            self.suppression = response.data.suppression;
            if(response.data.data[0].message !== "Error: no results found"){
                $mdToast.showSimple( 'Record information loaded' );
            } else {
                $mdToast.showSimple( 'Record had no information' );
            }

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

    self.suppressRecordSuccessCallback = function ( response ) {
        $mdToast.showSimple( response.data.message);
    };
    self.suppressRecordFailureCallback = function ( response ) {
        $mdToast.showSimple( response.data.message);
    };
} ] );
