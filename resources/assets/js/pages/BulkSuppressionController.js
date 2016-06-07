mt2App.controller( 'BulkSuppressionController' , [ '$log' , 'BulkSuppressionApiService', function ( $log, BulkSuppressionApiService ) {

    var self = this;

    self.file = '';
    self.emailString = '';
    self.emails = [];

    self.emailsLoaded = false;
    self.testUserId = 217;
    self.reason = '';

    self.uploadSuppressions = function () {

        self.emails = self.splitString(self.emailString);
        self.emails = self.createUniqueList(self.emails);

        var data = {
            'user_id': self.testUserId,
            'suppfile': self.file,
            'suppressionReasonCode': self.selectedReason,
            'emails': self.emails
        };

        BulkSuppressionApiService.uploadEmails(data, 
                self.uploadEmailsSuccessCallback,
                self.uploadEmailsFailureCallback);
    };

    self.loadReasons = function () {
        BulkSuppressionApiService.getSuppressionReasons( self.loadReasonsSuccessCallback , self.loadReasonsFailureCallback );
    };

    self.splitString = function(str) {
        var commaTest = /,/;

        if (commaTest.exec(str)) {
            return str.split(',');
        }

        // the other option is newline
        return str.split('\n');
    }

    self.createUniqueList = function(array) {
        var seen = {};
        var l = array.length;
        var i;
        var output = [];

        for (i = 0; i < l; i++) {
            seen[array[i]] = array[i];
        }

        for (i in seen) {
            output.push(seen[i]);
        }

        return output;
    }

    self.startTransfer = function(file) {
        // File uploaded to MT2, need to move to MT1bin
        // and notify when complete
        self.file = file.relativePath;

        BulkSuppressionApiService.transferFiles(
            self.fileTransferSuccessCallback,
            self.fileTransferFailureCallback
        );
        
    }

    self.enableSubmission = function() {
        self.emailsLoaded = true;
    }


    /**
     *  Modals and callbacks
     */

    self.uploadEmailsSuccessCallback = function() {
        self.setModalLabel('Success!');
        self.setModalBody('Emails suppressed.');
        self.launchModal();
    }

    self.uploadEmailsFailureCallback = function(message) {
        self.setModalLabel('Error');
        self.setModalBody('Suppression failed to upload: ' + message);
        self.launchModal();
    }

    self.fileTransferSuccessCallback = function() {
        self.emailsLoaded = true;
        self.setModalLabel('Success!');
        self.setModalBody('File uploaded.');
        self.launchModal();
    }

    self.fileTransferFailureCallback = function(files) {
        var fileString = files['data'].join(', ');
        self.setModalLabel('Error');
        self.setModalBody('Suppression of files ' + fileString + ' failed to transfer to server.');
        self.launchModal();
    }

    self.loadReasonsSuccessCallback = function ( response ) {
        self.suppressionReasons = response.data;
    };

    self.loadReasonsFailureCallback = function ( response ) {
        $mdToast.showSimple( 'Failed to Load Suppression Reasons' );
    };


    self.setModalLabel = function ( labelText ) {
        var modalLabel = angular.element( document.querySelector( '#pageModalLabel' ) );
        modalLabel.text( labelText );
    };

    self.setModalBody = function ( bodyText ) {
        var modalBody = angular.element( document.querySelector( '#pageModalBody' ) );
        modalBody.text( bodyText );
    }

    self.launchModal = function () {
        $( '#pageModal' ).modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel( '' );
        self.setModalBody( '' );

        $( '#pageModal' ).modal('hide');
    };

} ] );
