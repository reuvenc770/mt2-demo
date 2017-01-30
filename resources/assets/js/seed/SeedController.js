mt2App.controller( 'SeedController' , [ '$log' , '$window' , '$location' , '$timeout' , 'SeedApiService', '$rootScope', '$mdConstant' , 'formValidationService', 'modalService' , 'paginationService' , function ( $log , $window , $location , $timeout , SeedApiService, $rootScope , $mdConstant , formValidationService, modalService , paginationService ) {
    var self = this;
   self.email_address = '';

    self.delete = function(recordId) {
        var r = confirm("Are you sure you want to delete this?");
        if (r == true) {
            SeedApiService.deleteRow(recordId, self.deleteRowSuccess, self.deleteRowFailure)
        }
    };

    self.create = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        SeedApiService.saveRow({email_address: self.email_address}, self.addRowSuccess, self.addRowFailure);
    };

    //callbacks
    self.addRowFailure = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

    self.addRowSuccess = function ( response ) {
        $location.url( '/tools/seed' );
        $window.location.href = '/tools/seed';
    };

    self.deleteRowFailure = function ( response ) {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Failed to delete seed.');
        modalService.launchModal();
    };

    self.deleteRowSuccess = function ( response ) {
        $location.url( '/tools/seed' );
        $window.location.href = '/tools/seed';
    };
} ] );
