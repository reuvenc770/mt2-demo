mt2App.controller( 'BulkSuppressionController' , [ '$scope' , '$log' , '$timeout' , 'BulkSuppressionApiService', 'formValidationService' , 'modalService' , function ( $scope , $log, $timeout, BulkSuppressionApiService , formValidationService , modalService) {

    var self = this;

    self.file = '';
    self.emailString = '';
    self.emails = [];
    self.formErrors = {};

    self.emailsLoaded = false;
    self.testUserId = 217;
    self.suppressionReasonCode = '';

    self.uploadSuppressions = function () {
        formValidationService.resetFieldErrors(self);

        self.emails = self.splitString(self.emailString);
        self.emails = self.createUniqueList(self.emails);

        var data = {
            'user_id': self.testUserId,
            'suppfile': self.file,
            'suppressionReasonCode': self.suppressionReasonCode,
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
        formValidationService.resetFieldErrors(self);

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

    $scope.$on('flow::fileAdded', function (event, $flow, flowFile) {
        $(function () { $('[data-toggle="tooltip"]').tooltip() } );
    });
    /**
     *  Modals and callbacks
     */
    self.uploadEmailsSuccessCallback = function() {
        modalService.setModalLabel('Success');
        modalService.setModalBody('Emails suppressed.');
        modalService.launchModal();
    }

    self.uploadEmailsFailureCallback = function( response ) {
        formValidationService.loadFieldErrors( self , response );
    }

    self.fileTransferSuccessCallback = function() {
        self.emailsLoaded = true;
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    }

    self.fileTransferFailureCallback = function(files) {
        var fileString = files['data'].join(', ');
        modalService.setModalLabel('Error');
        modalService.setModalBody('Suppression of files ' + fileString + ' failed to transfer to server.');
        modalService.launchModal();
    }

    self.flowCompleteCallback = function() {
        modalService.setModalLabel('Success');
        modalService.setModalBody('File uploaded.');
        modalService.launchModal();
    };

    self.loadReasonsSuccessCallback = function ( response ) {
        self.suppressionReasons = response.data;
    };

    self.loadReasonsFailureCallback = function ( response ) {
            modalService.simpleToast('Failed to load suppression reasons.');
    };

} ] );
