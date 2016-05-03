mt2App.controller( 'BulkSuppressionController' , [ '$log' , 'BulkSuppressionApiService', function ( $log, BulkSuppressionApiService ) {

    var self = this;

    self.file = '';
    self.emailString = '';
    self.emails = [];

    self.emailsLoaded = false;
    self.testUserId = 217;
    self.reason = '';

    self.uploadSuppressions = function () {
        console.log('Testing values:');
        console.log(self.reason);

        self.emails = self.splitString(self.emailString);
        self.emails = self.createUniqueList(self.emails);
        console.log(self.emails);

        var data = {
            'user_id': self.testUserId,
            'suppfile': self.file,
            'suppressionReasonCode': self.reason,
            'emails': self.emails
        };

        BulkSuppressionApiService.uploadEmails(data, 
                self.uploadEmailsSuccessCallback,
                self.uploadEmailsFailureCallback);
    }

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


    /**
     *  Modals and callbacks
     */

    self.uploadEmailsSuccessCallback = function() {
        self.setModalLabel('Success!');
        self.setModalBody('Suppression uploaded.');
        self.launchModal();
    }

    self.uploadEmailsFailureCallback = function() {
        self.setModalLabel('Error');
        self.setModalBody('Suppression failed to upload.');
        self.launchModal();
    }


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
